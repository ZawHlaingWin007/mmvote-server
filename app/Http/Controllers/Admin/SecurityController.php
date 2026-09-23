<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Setting;
use App\Election;
use App\Question;
use App\Candidate;
use App\Voter;
use App\Result;
use App\Answer;
use App\Voting;
use App\Log;
use App\ElectionVoter;
use Illuminate\Support\Facades\DB;

class SecurityController extends Controller
{
    public function index()
    {
        return view('admin.setting.security.index');
    }

    public function resetElections()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    
        Election::truncate();
        Result::truncate();
        Question::truncate();
        Answer::truncate();
        Candidate::truncate();
        ElectionVoter::truncate();
        Voting::truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        return back()->with('success', 'Reset elections and result data successfully!');
    }

    public function resetVoters()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    
        Voter::truncate();
        Log::truncate();
        Result::truncate();
        Candidate::query()->update(['vote_count' => 0]);
        Answer::truncate();
        ElectionVoter::truncate();
        Voting::truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        return back()->with('success', 'Reset voters and logs data successfully!');
    }

    public function update(Request $request)
    {                           
        Setting::first()->update([
            'otp_enable' =>  $request->otp_enable == 'on' ? '1' : '0',
            'result_enable' => $request->result_enable == 'on' ? '1' : '0',
            'otp_valid_time' => $request->otp_valid_time,
            'otp_valid_time_type' => $request->time_type,
            'voting_record' => $request->voting_record == 'on' ? '1' : '0',
            'result_detail' => $request->result_detail == 'on' ? '1' : '0',
        ]);
        return response()->json(['success' => 'Successfully Status Changed!']);
    }
}
