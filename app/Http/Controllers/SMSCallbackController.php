<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SMSCallbackController extends Controller
{
    public function smsCallback(Request $request)
    {
        Log::debug("CALLBACK REQUEST", $request->all());
    }
}
