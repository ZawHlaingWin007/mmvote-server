<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Validator;

class EmailSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        return view('admin.setting.email_setting.email_setting');

    }

    public function update(Request $request)
    {        
        $rules = array(               
            'mail_password' => 'required_with:mail_username',
        );

        $error = Validator::make($request->all(), $rules);

        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        Setting::first()->update([
            'smtp_host' =>  $request->smtp_host,
            'smtp_port' => $request->smtp_port,
            'mail_username' => $request->mail_username,
            'mail_password' => $request->mail_password,            
            'mail_encryption' => $request->mail_encryption,            
            'mail_from_address' => $request->mail_from_address,            
            'mail_from_name' => $request->mail_from_name,
            'mail_subject' => $request->mail_subject,
            'mail_footer' => $request->mail_footer,
        ]);
        Artisan::call('config:cache');
        Artisan::call('queue:restart');
        return response()->json(['success' => 'Successfully Status Changed!']);
    }    
}
