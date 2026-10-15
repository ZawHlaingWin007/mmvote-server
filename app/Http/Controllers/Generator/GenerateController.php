<?php

namespace App\Http\Controllers\Generator;

use App\Election;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Company;
use App\Voter;
use Illuminate\Support\Facades\DB;
use App\ElectionVoter;
use App\Exports\ExportVoterList;
use App\Imports\VoterImport;
use App\Jobs\MailServiceJob;
use App\Jobs\SMSServiceJob;
use App\Setting;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

class GenerateController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function index()
    {
        $elections = Election::all();

        $company = Company::all();
        return view('generator.index', compact('elections', 'company'));
    }

    public function vidList()
    {
        if (request()->ajax()) {
            $electionId = $_GET['election_id'];
            $done = $_GET['done'];

            $election = Election::find($electionId);
            if (!$election) {
                // return response()->json(['error' => 'Election Not Found'],404);
            }
            if (($done == 0 || $done == 1) && $done != '') {
                $DT_data = ElectionVoter::with(['voter', 'log'])->where('election_id', $electionId)->where('done', $done)->get();
            } else {
                $DT_data = ElectionVoter::with(['voter', 'log'])->where('election_id', $electionId)->get();
            }
            return datatables()->of($DT_data)
                ->addColumn('sms_log',function($DT_data){
                    $button = '<button type="button" data-id="' . $DT_data->id . '" class="btn sms_log"><i class="fa fa-list"></i> Log</button>';

                    return $button;
                })
                ->addColumn('reminder_log',function($DT_data){
                    $button = '<button type="button" data-id="' . $DT_data->id . '" class="btn reminder_log"><i class="fa fa-list"></i> Reminder Log</button>';

                    return $button;
                })
                ->addColumn('action', function ($DT_data) use ($election) {
                    $button = '<button type="button" data-id="' . $DT_data->id . '" data-voter_id="' . $DT_data->voter->voter_id . '" class="btn" id="btn_print" data-name="' . $election->name . '"><i class="fa fa-print"></i> Print</button>';

                    return $button;
                })
                ->rawColumns(['action','sms_log','reminder_log'])
                ->addIndexColumn()
                ->make(true);
        }
        $company =  DB::table('company')->latest('created_at')->first();

        $electionLidt = Election::all();        
        return view('generator.generatedVid-list', compact('company', 'electionLidt'));
    }

    public function store(Request $request)
    {
        $voter = new Voter;
        $voter->voter_id = $request->vid;
        $voter->vote_count = $request->vote_count;
        $voter->name = $request->name;
        $voter->email = $request->email;
        $voter->phone_no = $request->ph_no;
        $voter->save();

        $voter_data = Voter::where("voter_id", $request->vid)->first();
        $elections = Election::all();
        if (count($elections) > 0) {
            foreach ($elections as $election) {
                $election_voter = new ElectionVoter();
                $election_voter->election_id = $election->id;
                $election_voter->voter_id = $voter_data->id;
                $election_voter->save();
            }
        }

        // DB::table('logs')->insert([
        //     'voter_id' => $voter->id,
        // ]);

        return response()->json(['success' => 'Voter ID Generated Successfully.', 'vid' => $request->vid,'id' => $voter->id]);
    }

    public function generateVidBlade()
    {

        $company =  DB::table('company')->latest('created_at')->first();        
        $election =  Election::where('status', '1')->first();
        return view('generator.vidgenerate', compact('company', 'election'));
    }

    public function excelGenerate(Request $request)
    {
        if ($request->ajax()) {
            if ($request->hasFile('file')) {
                $validator = Validator::make(
                    [
                        'extension' => strtolower($request->file->getClientOriginalExtension()),
                    ],
                    [
                        'extension'      => 'required|in:csv,xlsx,xls',
                    ]
                );

                if ($validator->fails()) {
                    return response()->json(['errors' => $validator->errors()->all()]);
                }

                try {
                    $import = new VoterImport();
                    $import->import($request->file('file'));
                } catch (ValidationException $e) {

                    $failures = $e->failures();
                    return response()->json($failures[0]->errors());
                }

                return response()->json(['success' => 'Data Added Successfully']);
            } else {
                return response()->json(['errors' => 'Excel File is Required!']);
            }
        } else {
            return abort(404);
        }
    }

    public function export()
    {
        return Excel::download(new ExportVoterList(), 'Voter.xlsx');
    }

    public function excelDownload()
    {
        $file_path = public_path() . '/upload/generate_template.xlsx';
        // dd($file_path);
        if (file_exists($file_path)) {
            return response()->download($file_path);
        } else {
            // Error
            exit('Requested file does not exist on our server!');
        }
    }

    public function sendMessage(Request $request)
    {
        $payload = $request->all();

        if ($request->type === 'all_reminder') {
            if (!$request->filled('election_id')) {
                return response()->json(['errors' => 'Please select an election first.']);
            }

            $payload['check_val'] = ElectionVoter::where('election_id', $request->election_id)
                ->where(function ($query) {
                    $query->where('done', 0)->orWhereNull('done');
                })
                ->pluck('voter_id')
                ->all();

            if (count($payload['check_val']) === 0) {
                return response()->json(['errors' => 'No voters who have not voted for this election.']);
            }
        }

        if (!empty($payload['check_val'])) {
            Log::info('Getting Request => ', $payload);
            $setting = Setting::first();
            if($request->method == 'sms')
            {
                SMSServiceJob::dispatch($payload);
            }elseif($request->method == 'email')
            {
                MailServiceJob::dispatch($setting,$payload);
            }else{
                SMSServiceJob::dispatch($payload);
                MailServiceJob::dispatch($setting,$payload);
            }
        } else {
            Log::info('User Dont Select => ', $request->all());
        }

        return response()->json(['success' => $request->method == 'sms' ? 'SMS are sending!' : ($request->method == 'email' ? 'Emails are sending!' : 'SMS and Emails are sending now!') ]);
    }    
}
