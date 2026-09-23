<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SMSAPICallBackJob;
use App\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class SmsSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.setting.sms_api_setting.sms_api_setting');

    }
    
    public function update(Request $request)
    {        
        $rules = [
            'sms_api_url' => 'required_with:sms_url_method,sms_token,sms_sender',
            'sms_url_method' => 'required_with:sms_sender,sms_token,sms_api_url',
            'sms_token' => 'required_with:sms_sender,sms_url_method,sms_api_url',
            'sms_sender' => 'required_with:sms_token,sms_url_method,sms_api_url',
        ];

        $error = Validator::make($request->all(), $rules,[
            'sms_token.required_with' => 'The sms api key is required when sms api url / sms url method / sms sender is present.'
        ]);

        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        Setting::first()->update([
            'sms_api_url' =>  $request->sms_api_url,
            'sms_url_method' => $request->sms_url_method,
            'sms_token' => $request->sms_token,
            'sms_sender' => $request->sms_sender,            
        ]);
        return response()->json(['success' => 'Successfully Status Changed!']);
    }

    public function callBackStatus(Request $request)
    {
        Log::debug('Callback Log', [
            $request->all()
        ]);
        SMSAPICallBackJob::dispatch($request->all());
        return response()->json([
            'status' => true,
        ],200);
    }
}
