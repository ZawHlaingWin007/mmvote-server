<?php

namespace App\Http\Controllers\Admin;

use App\Election;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Question;
use App\QuestionDetail;
use Illuminate\Support\Facades\Validator;

class QuestionDetailController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index($election_id,$question_id)
    {
        $question = Question::with('question_detail')->findOrFail($question_id);
        if(request()->ajax()){          
            $DT_data = QuestionDetail::where('question_id', $question_id)->get();              
            return datatables()->of($DT_data)
                ->addIndexColumn()               
                ->addColumn('action', function ($DT_data) {
                    $edit_route = route('admin.question-detail.edit',['election_id' => $DT_data->question->election_id,'question_id' => $DT_data->question_id,'id' => $DT_data->id]);
                    $detail_route = route('admin.question-detail.show',['election_id' => $DT_data->question->election_id,'question_id' => $DT_data->question_id,'id' => $DT_data->id]);
                    $button = '<a href="'.$detail_route.'" class="btn btn-default btn-xs btn-flat"><i class="fas fa-eye"></i> Detail</a>';
                    $button .= '&nbsp;&nbsp;';
                    if ($DT_data->status == 1) {                        
                        $button .= '<button type="button" class="edit btn btn-info btn-xs btn-flat" disabled><i class="fas fa-edit"></i> Edit</button>';
                        $button .= '&nbsp;&nbsp;';
                        $button .= '<button type="button" name="delete" id="' . $DT_data->id . '" class="delete btn btn-danger btn-xs btn-flat" disabled><i class="fa fa-trash"></i> Delete</button>';
                    } else {
                        $button .= '<a href="'.$edit_route.'" class="edit btn btn-info btn-xs btn-flat"><i class="fas fa-edit"></i> Edit</a>';                        
                        $button .= '&nbsp;&nbsp;';
                        $button .= '<button type="button" name="delete" id="' . $DT_data->id . '" class="delete btn btn-danger btn-xs btn-flat"><i class="fa fa-trash"></i> Delete</button>';
                    }
                    return $button;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        $election_modal = new Election;
        $election = $election_modal->electionWithId($election_id);
        if ($election) {
            if ($election->ques_flag == 1) {
                $elections = $election_modal->electionWithoutCurrent($election_id);
                return view('admin.question-detail.index',compact('question','elections','election'));
            } else {
                abort(404);
            }
        } else {
            abort(404);
        }
        
    }

    public function create($election_id,$question_id)
    {
        $election_modal = new Election;
        $election = $election_modal->electionWithId($election_id);
        if ($election) {
            if ($election->ques_flag == 1) {                
                $elections = $election_modal->electionWithoutCurrent($election_id);
                return view('admin.question-detail.create',compact('elections','election','question_id'));
            } else {
                abort(404);
            }
        } else {
            abort(404);
        }
    }

    public function store(Request $request)
    {
        $rules = array(
            'name'    =>  'required|string|max:255',
            'image'         =>  'required|mimes:png,jpg,jpeg',
            'description'         =>  'required',
        );

        $error = Validator::make($request->all(), $rules);

        if ($error->fails()) {
            return redirect()->back()->withErrors($error)->withInput();
        }

        $image = $request->file('image');
        $upload_path = public_path() . '/upload/question-detail/';
        $file = $image->getClientOriginalExtension();
        $name = rand().'.'. $file;
        $image->move($upload_path, $name);

        $new_name = '/upload/question-detail/' . $name;

        $form_data = array(
            'name'        => $request->name,
            'description' => $request->description,
            'image'       => $new_name,
            'question_id' => $request->question_id,
        );

        QuestionDetail::create($form_data);

        return redirect()->route('admin.question-detail.index',['election_id' => $request->election_id,'question_id' => $request->question_id]);
    }

    public function edit($election_id,$question_id,$id)
    {
        $election_modal = new Election;
        $election = $election_modal->electionWithId($election_id);
        if ($election) {
            if ($election->ques_flag == 1) {                
                $question_detail = QuestionDetail::with('question')->findOrFail($id);
                $elections = $election_modal->electionWithoutCurrent($election_id);
                return view('admin.question-detail.edit',compact('question_detail','elections','election'));
            } else {
                abort(404);
            }
        } else {
            abort(404);
        }
    }

    public function update(Request $request,$id)
    {                
        $rules = array(
            'name'    =>  'required|string|max:255',            
            'description'         =>  'required',
        );

        $error = Validator::make($request->all(), $rules);

        if ($error->fails()) {
            return redirect()->back()->withErrors($error)->withInput();
        }

        $questionDetail = QuestionDetail::findOrFail($id);

        if($request->hasFile('image')){
            $image = $request->file('image');
            $upload_path = public_path() . '/upload/question-detail/';
            $file = $image->getClientOriginalExtension();
            $name = rand().'.'. $file;
            $image->move($upload_path, $name);

            $new_name = '/upload/question-detail/' . $name;
        }else{
            $new_name = $questionDetail->image;
        }

        $form_data = array(
            'name'        => $request->name,
            'description' => $request->description,
            'image'       => $new_name,
            'question_id' => $request->question_id,
        );
        
        $questionDetail->update($form_data);

        return redirect()->route('admin.question-detail.index',['election_id' => $request->election_id,'question_id' => $request->question_id]);
    }

    public function show($election_id,$question_id,$id)
    {
        $election_modal = new Election;
        $election = $election_modal->electionWithId($election_id);
        if ($election) {
            if ($election->ques_flag == 1) {
                $question_detail = QuestionDetail::with('question')->findOrFail($id);
                $elections = $election_modal->electionWithoutCurrent($election_id);
                return view('admin.question-detail.show',compact('question_detail','elections','election'));
            } else {
                abort(404);
            }
        } else {
            abort(404);
        }
    }

    public function destroy($id)
    {        
        $data = QuestionDetail::findOrFail($id);
        
        $path = public_path(). $data->image;

        if($data->image && $data->image != "/images/user.png")
        {
            if (file_exists($path)) {
                unlink($path);
            }
        }
        $data->delete();
        return response()->json(['success' => 'Data is successfully deleted']);
    }
}
