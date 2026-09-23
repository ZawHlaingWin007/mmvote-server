<?php

namespace App\Http\Middleware;

use App\Setting;
use Closure;
use Illuminate\Support\Facades\View;

class SettingMiddleware
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
        $setting = Setting::first();
        View::share('setting', $setting);
        // config([            
        //     'mail.from.name' => $setting->email_sender ? $setting->email_sender : 'E-Voting',
        //     'mail.from.address' => $setting->email_address ? $setting->email_address : 'evoting.helper@gmail.com',
        //     'mail.username' => $setting->email_address ? $setting->email_address : 'evoting.helper@gmail.com',
        //     'mail.password' => $setting->email_password ? $setting->email_password : 'qcgnmxenimwrynki',
        // ]);
        
        return $next($request);
    }
}
