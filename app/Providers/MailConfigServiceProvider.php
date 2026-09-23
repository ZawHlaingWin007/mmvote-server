<?php

namespace App\Providers;

use App\Setting;
use Illuminate\Support\ServiceProvider;

class MailConfigServiceProvider extends ServiceProvider
{
    public function boot()
    {
        try {
            $settings = Setting::first();            
            config([
                'mail.host' => $settings->smtp_host,
                'mail.port' => $settings->smtp_port,
                'mail.username' => $settings->mail_username,
                'mail.password' => $settings->mail_password,
                'mail.encryption' => $settings->mail_encryption,
                'mail.from.address' => $settings->mail_from_address,
                'mail.from.name' => $settings->mail_from_name,
                'mail.subject' => $settings->mail_subject,
            ]);
        } catch (\Throwable $th) {
        }
    }
}
