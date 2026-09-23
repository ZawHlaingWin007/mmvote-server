<?php
namespace App\Classes;
use App\Setting;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
class MPSSBulk
{
    // this config will work with MPSS server
    public static function sendSMS($mobileNumber, $voter, $type = null, $url = null)
    {
        $setting = Setting::first();
        if ($setting->sms_api_url != null && $setting->sms_api_url != '') {
            $sms_api_url = $setting->sms_api_url;
        } else {
            // $sms_api_url = "http://159.138.135.30/smsserver/sendsms-token";
            $sms_api_url = "https://thirdparty.mmpaymal.com/smsserver/sendsms-token";
        }
        if ($setting->sms_url_method != null && $setting->sms_url_method != '') {
            $sms_url_method = $setting->sms_url_method;
        } else {
            $sms_url_method = "POST";
        }
        if ($setting->sms_token != null && $setting->sms_token != '') {
            $token = $setting->sms_token;
        } else {
            $token = "lKwrR0do7Ncd8ebzire137tt";
        }
        if ($setting->sms_sender != null && $setting->sms_sender != '') {
            $sms_sender = $setting->sms_sender;
        } else {
            $sms_sender = "LOYAR";
        }
        if ($voter) {
            $url != null ? $url : $url = route('vote.link', ['voter_id' => $voter->voter_id]);
            if ($type == 'reminder') {
                $message = ($setting->reminder_text == null) ? Lang::get('message.reminder') . $url :
                    str_replace(['[:VoterName]', '[:ShareCount]'], [$voter->name, $voter->vote_count], $setting->reminder_text) . $url;
            } else if ($type == 'member_announce') {
                $message = ($setting->member_annouce == null) ? Lang::get('message.annouce') :
                    str_replace(['[:MemberName]', '[:ShareCount]'], [$voter->name], $setting->member_annouce);
            } else if ($type == 'voter_announce') {
                $message = ($setting->voter_annouce == null) ? Lang::get('message.annouce') :
                    str_replace(['[:VoterName]', '[:ShareCount]'], [$voter->name, $voter->vote_count], $setting->voter_annouce);
            } else if ($type == 'member') {
                $message = ($setting->member_sms_text == null) ? Lang::get('message.member') . $url . " \n" . Lang::get('message.contact') :
                    str_replace('[:MemberName]', $voter->name, $setting->member_sms_text) . $url;
            } else if ($type == 'otp') {
                $message = $url;
            } else {
                $message = ($setting->sms_text == null) ? Lang::get('message.text') . $url :
                    str_replace(['[:VoterName]', '[:ShareCount]'], [$voter->name, $voter->vote_count], $setting->sms_text) . $url;
            }
        } else {
            return response()->json(['errors' => $type == "member" ? 'Member' : 'Voter' . ' Not Found!']);
        }
        $data = [];
        if (count($mobileNumber) > 1) {
            foreach ($mobileNumber as $phone) {
                $phone = str_replace("-", "", $phone);
                $phone = str_replace(" ", "", $phone);
                Log::info('Many Phones => ', ['ph' => $phone]);
                $res = self::sendApi($phone, $message, $sms_sender, $sms_api_url, $sms_url_method, $token);
                $data[] = $res;
            }
        } else {
            $mobileNumber = str_replace("-", "", $mobileNumber[0]);
            $mobileNumber = str_replace(" ", "", $mobileNumber);
            Log::info('Phones => ', ['ph' => $mobileNumber]);
            $res = self::sendApi($mobileNumber, $message, $sms_sender, $sms_api_url, $sms_url_method, $token);
            $data[] = $res;
        }
        return response()->json(['info' => $data]);
    }
    private static function sendApi($mobileNumber, $message, $sms_sender, $sms_api_url, $sms_url_method, $token)
    {
        $data = [
            "to"        =>      $mobileNumber,
            "message"   =>      $message,
            "sender"    =>      $sms_sender
        ];
        $ch = curl_init($sms_api_url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $sms_url_method);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json'
        ]);
        Log::info('CURL data => ', [$ch]);
        $result = curl_exec($ch);
        // $info = json_decode($result);
        return $result;
    }
}