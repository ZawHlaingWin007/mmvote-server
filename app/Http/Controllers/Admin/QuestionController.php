<?php

namespace App\Http\Controllers\Admin;

use App\Election;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Question;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class QuestionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($election_id)
    {
        if (request()->ajax()) {            
            $DT_data = Question::latest()->where('questions.election_id', $election_id)->join('election', 'election.id', 'questions.election_id')->get(['questions.*', 'election.status']);
            return datatables()->of($DT_data)
                ->addIndexColumn()
                ->addColumn('detail', function ($DT_data) {
                    $button = '<a href="' . route('admin.question-detail.index',['election_id' => $DT_data->election_id,'question_id' => $DT_data->id]) . '" class="btn btn-primary btn-xs btn-flat"><i class="fa fa-plus"></i> Add</a>';
                    $button .= '&nbsp;&nbsp;';                    
                    return $button;                
                })
                ->addColumn('action', function ($DT_data) {
                    if ($DT_data->status == 1) {
                        $button = '<button type="button" name="edit" id="' . $DT_data->id . '" class="edit btn btn-info btn-xs btn-flat" disabled><i class="fas fa-edit"></i> Edit</button>';
                        $button .= '&nbsp;&nbsp;';
                        $button .= '<button type="button" name="delete" id="' . $DT_data->id . '" class="delete btn btn-danger btn-xs btn-flat" disabled><i class="fa fa-trash"></i> Delete</button>';
                    } else {
                        $button = '<button type="button" name="edit" id="' . $DT_data->id . '" class="edit btn btn-info btn-xs btn-flat"><i class="fas fa-edit"></i> Edit</button>';
                        $button .= '&nbsp;&nbsp;';
                        $button .= '<button type="button" name="delete" id="' . $DT_data->id . '" class="delete btn btn-danger btn-xs btn-flat"><i class="fa fa-trash"></i> Delete</button>';
                    }
                    return $button;
                })
                ->rawColumns(['action','detail'])
                ->make(true);
        }
        $election_modal = new Election;
        $election = $election_modal->electionWithId($election_id);
        if ($election) {
            if ($election->ques_flag == 1) {
                $elections = $election_modal->electionWithoutCurrent($election_id);
                return view('admin.question.index', compact('election', 'elections'));
            } else {
                abort(404);
            }
        } else {
            abort(404);
        }
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $rules = array(
            'no_mm' => 'required',
            'ques' => 'required|string',
        );

        $error = Validator::make($request->all(), $rules);

        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $form_data = array(
            'no'        => $request->no_mm,
            'ques'       => $request->ques,
            'election_id' => $request->election_id,            
        );

        Question::create($form_data);

        return response()->json(['success' => 'Data Added successfully.']);
    }

    public function edit($question_id)
    {
        // dd($id);
        if (request()->ajax()) {
            $data = Question::findOrFail($question_id);
            return response()->json(['data' => $data]);
        }
    }

    public function update(Request $request)
    {
        // dd($request->all());

        $rules = array(
            'no_mm' => 'required',
            'ques' => 'required|string',
        );

        $error = Validator::make($request->all(), $rules);

        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }


        $form_data = array(
            'no'       =>   $request->no_mm,
            'ques'            =>   $request->ques,
            'election_id' => $request->election_id,            
        );
        Question::whereId($request->hidden_id)->update($form_data);

        return response()->json(['success' => 'Data is successfully updated']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = Question::findOrFail($id);
        // dd($data);

        $data->delete();

        $nonedata = DB::table('questions')->count() == 0 ? true : false;

        if ($nonedata) {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('questions')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        } else {
            $last_id = Question::latest()->first()->id + 1;
            DB::statement("ALTER TABLE `questions` AUTO_INCREMENT = $last_id");
        }
    }
}
