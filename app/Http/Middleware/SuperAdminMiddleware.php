<?php

namespace App\Http\Middleware;

use App\Setting;
use Closure;
use Illuminate\Support\Facades\Auth;

class SuperAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(Auth::user()->type != 'superadmin')
        {
            if($request->segment(2) != 'voting')
            {
                abort(403);
            }else{
                $setting = Setting::first();
                if($setting->voting_record == '0' && $request->segment(3) == 'votingrecord')
                {
                    abort(403);
                }elseif($setting->result_detail == '0' && $request->segment(3) == 'votingresult')
                {
                    abort(403);
                }
            }
        }
        return $next($request);
    }
}
