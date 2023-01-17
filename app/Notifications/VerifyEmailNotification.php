<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\EmailTemplate;

class VerifyEmailNotification extends VerifyEmail
{
    public $verifyEmailRoute;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($verifyEmailRoute)
    {
        $this->verifyEmailRoute = $verifyEmailRoute;
    }

    /**
     * Get the verification URL for the given notifiable.
     *
     * @param  mixed  $notifiable
     * @return string
     */
    public function toMail($notifiable)
    {
       $link =  URL::temporarySignedRoute(
            $this->verifyEmailRoute ?: 'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
        $name = $notifiable->name;
        $email = $notifiable->email;
        $email_template = EmailTemplate::where('type','customer_sign_up_confirmation')->first();
        $lang = app()->getLocale();
        $email_template = transformEmailTemplateModel($email_template,$lang);
        $content = $email_template['content'];
        $subject = $email_template['subject'];
        $search = array("{{name}}","{{link}}","{{app_name}}");
        $replace = array($name,$link,env('APP_NAME'));
        $content = str_replace($search,$replace,$content);
        return (new MailMessage)
            ->view('admin.emails.template', ['content' => $content])
            ->subject($subject);
    }
}
