<?php

namespace App\Classes;

use App\Log as AppLog;
use App\Setting;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;

class BulkSMS
{
    /**
     * Send SMS to the given phone numbers.
     *
     * @param array $mobileNumber
     * @param object|null $voter
     * @param string|null $type
     * @param string|null $url
     * @return \Illuminate\Http\JsonResponse
     */
    public static function sendSMS(array $mobileNumber, $voter = null, $type = null, $url = null)
    {
        $setting = Setting::first();
        Log::info($setting);
        // Retrieve settings and log necessary information
        $sms_api_url = $setting->sms_api_url ?? '';
        $sms_url_method = $setting->sms_url_method ?? 'POST';
        $token = $setting->sms_token ?? '';
        $sms_sender = $setting->sms_sender ?? '';

        if (!$sms_api_url || !$token || !$sms_sender) {
            Log::info("1");
            return response()->json(['errors' => 'SMS settings are incomplete.']);
        }

        // Default URL if not provided
        $url = $url ?? route('vote.link', ['voter_id' => $voter->voter_id ?? '']);

        // Construct the SMS message based on type
        $message = self::constructMessage($type, $setting, $voter, $url);
        Log::debug("Constructed Message", [$message]);
        Log::channel('single')->info(print_r($message, true));
        if (!$message) {
            return response()->json(['errors' => 'Invalid message type.']);
        }

        // Clean and format phone numbers
        $mobileNumber = array_map(function($phone) {
            return str_replace(['-', ' '], '', $phone);
        }, $mobileNumber);

        $data = [
            "to" => $mobileNumber,
            "message" => $message,
            "sender" => $sms_sender,
            "from" => $sms_sender,
            "clientReference" => (string) rand(1, 999),
            "deliveryReceiptUrl" => url('/api/sms/call-back/status')
        ];
        // "deliveryReceiptUrl" => 'https://evoting.mtshmyanmar.com/api/sms/call-back/status'

        // Initialize cURL and send the request
        $ch = curl_init($sms_api_url);
        curl_setopt_array($ch, [
            CURLOPT_CUSTOMREQUEST => $sms_url_method,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $token,
                'Content-Type: application/json'
            ]
        ]);

        $result = curl_exec($ch);
        $info = json_decode($result);
        curl_close($ch);

        return response()->json(['info' => $info]);
    }

    /**
     * Construct the SMS message based on type and settings.
     *
     * @param string|null $type
     * @param object $setting
     * @param object|null $voter
     * @param string $url
     * @return string|null
     */
    private static function constructMessage($type, $setting, $voter, $url)
    {
        if (!$voter) {
            return null;
        }

        Log::debug("Message Params", [
            $type,
            $setting,
            $voter,
            $url
        ]);

        $name = $voter->name;
        $vote_count = $voter->vote_count;

        switch ($type) {
            case 'reminder':
                // return ($setting->reminder_text ?? Lang::get('message.reminder')) . str_replace(['[:VoterName]', '[:ShareCount]'], [$name, $vote_count], $setting->reminder_text) . $url;
                return str_replace(
                        ['[:VoterName]', '[:ShareCount]'],
                        [$name, $vote_count],
                        $setting->reminder_text ?? Lang::get('message.reminder')
                    ) . ' ' . $url;
            case 'member_announce':
                return ($setting->member_annouce ?? Lang::get('message.annouce')) . str_replace(['[:MemberName]'], [$name], $setting->member_annouce);
            case 'voter_announce':
                return ($setting->voter_annouce ?? Lang::get('message.annouce')) . str_replace(['[:VoterName]', '[:ShareCount]'], [$name, $vote_count], $setting->voter_annouce);
            case 'member':
                return ($setting->member_sms_text ?? Lang::get('message.member') . $url . " \n" . Lang::get('message.contact')) . str_replace('[:MemberName]', $name, $setting->member_sms_text) . $url;
            case 'otp':
                return $url;
            default:
                // return ($setting->sms_text ?? Lang::get('message.text')) . str_replace(['[:VoterName]', '[:ShareCount]'], [$name, $vote_count], $setting->sms_text) . $url;
                // return str_replace(['[:VoterName]', '[:ShareCount]'], [$name, $vote_count], $setting->sms_text) . $url;
                return str_replace(
                        ['[:VoterName]', '[:ShareCount]'],
                        [$name, $vote_count],
                        $setting->sms_text
                    ) . ' ' . $url;
        }
    }
}
