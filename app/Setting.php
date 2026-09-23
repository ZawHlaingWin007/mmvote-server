<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'fav_name',
        'fav_icon',
        'logo_name',
        'logo_image',
        'sms_text',
        'reminder_text',
        'member_sms_text',
        'otp_enable',
        'result_enable',
        'otp_valid_time',
        'otp_valid_time_type',
        'member_annouce',
        'voter_annouce',
        'voting_record',
        'result_detail',
        'sms_api_url',
        'sms_url_method',
        'sms_token',
        'sms_sender',
        'smtp_host',
        'smtp_port',
        'mail_username',
        'mail_password',
        'mail_encryption',
        'mail_from_address',
        'mail_from_name',
        'mail_subject',
        'mail_footer'
    ];

    protected $table = 'settings';
}
