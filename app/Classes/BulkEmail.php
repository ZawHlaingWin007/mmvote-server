<?php
namespace App\Classes;

use App\Setting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;

class BulkEmail{
    public static function sendEmail($emails,$voter,$type = null,$url = null)
    {

        $setting = Setting::first();
        
        if($voter)
        {
            $url != null ? $url : $url = route('vote.link', ['voter_id' => $voter->voter_id]);

            if($type == 'reminder')
            {
                $template = $setting->reminder_text == null ? Lang::get('message.reminder') : $setting->reminder_text;
            } else if ($type == 'member_announce') {
                $template = $setting->member_annouce == null ? Lang::get('message.annouce') : $setting->member_annouce;
            } else if ($type == 'voter_announce') {
                $template = $setting->voter_annouce == null ? Lang::get('message.annouce') : $setting->voter_annouce;
            } else if($type == 'member'){
                $template = $setting->member_sms_text == null ? Lang::get('message.member') .$url. Lang::get('message.contact') : $setting->member_sms_text;
            }else{
                $template = $setting->sms_text == null ? Lang::get('message.text') : $setting->sms_text;
            }

            $content = self::applyPlaceholders($template, $voter);

            $time = Carbon::now();
            $datetime = $time->toDateTimeString();
            $DT = explode(' ', $datetime);
            $image = public_path() . '/images/mti_logo.png';
        }else{
            return response()->json(['errors' => $type == "member" ? 'Member' : 'Voter'.' Not Found!']);
        }        
        
        if(count($emails) > 1)
        {
            foreach($emails as $email)
            {
                $email = str_replace(" ", "", $email);
                Mail::send(
                    'vid_email',
                    array(
                        'link' => $url,
                        'image' => $image,
                        'content' => $content,
                        'date' => $DT[0],
                        'time' => $DT[1],
                    ),
                    function ($message) use ($email,$setting) {
                        $message->from($setting->email_address ? $setting->email_address : config()->get('mail.username'));
                        $message->subject($setting->email_subject ? $setting->email_subject : 'E-Voting');
                        $message->to($email);
                    }
                );
            }
        }else{
            $emails = str_replace(" ", "", $emails[0]);
            Mail::send(
                'vid_email',
                array(
                    'link' => $url,
                    'image' => $image,
                    'content' => $content,
                    'date' => $DT[0],
                    'time' => $DT[1],
                ),
                function ($message) use ($emails,$setting) {
                    $message->from($setting->email_address ? $setting->email_address : config()->get('mail.username'));
                    $message->subject($setting->email_subject ? $setting->email_subject : 'E-Voting');
                    $message->to($emails);
                }
            );
        }        

        if (Mail::failures()) {
            return response()->json(['errors' => 'Email Send Failed.']);
        } else {
            return response()->json(['success' => 'Message Send Successfully.']);
        }
    }

    private static function applyPlaceholders($text, $voter)
    {
        $text = html_entity_decode((string) $text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        $name = isset($voter->name) ? (string) $voter->name : '';
        $share = isset($voter->vote_count) ? (string) $voter->vote_count : '';

        $text = preg_replace_callback(
            '/\[\s*:?\s*(?:<[^>]*>\s*)*(?:VoterName|MemberName)(?:\s*<[^>]*>)*\s*\]/iu',
            function () use ($name) {
                return $name;
            },
            $text
        );

        $text = preg_replace_callback(
            '/\[\s*:?\s*(?:<[^>]*>\s*)*ShareCount(?:\s*<[^>]*>)*\s*\]/iu',
            function () use ($share) {
                return $share;
            },
            $text
        );

        return $text;
    }
}
