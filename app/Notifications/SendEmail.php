<?php

namespace App\Notifications;

use App\Setting;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SendEmail extends Notification
{
    use Queueable;
    private $content;
    private $url;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($content, $url)
    {
        $this->content = $content;
        $this->url = $url;        
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $setting = Setting::first();
        $time = Carbon::now();
        $datetime = $time->toDateTimeString();
        $DT = explode(' ', $datetime);
        // $image = public_path() . '/images/mtsh-logo.jpg';
        $image = public_path() . $setting->logo_image;
     
        return (new MailMessage)
        ->subject(config('mail.subject'))        
        ->view('vid_email', array(
            'link' => $this->url,
            'footer' => $setting->mail_footer,
            'image' => $image,
            'content' => $this->content,
            'date' => $DT[0],
            'time' => $DT[1],
        ));            
    } 
}
