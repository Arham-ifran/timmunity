<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Mail;
use App\Mail\SentResetPasswordMail;
use App\Models\EmailTemplate;

class ResetPasswordNotification extends ResetPassword
{
    public $resetPasswordRoute;

    public $resetPasswordConfig;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($token, $resetPasswordRoute = null, $resetPasswordConfig = null)
    {
        parent::__construct($token);
        $this->resetPasswordRoute = $resetPasswordRoute;
        $this->resetPasswordConfig = $resetPasswordConfig;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toMail($notifiable)
    {
        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $notifiable, $this->token);
        }

        if (static::$createUrlCallback) {
            $url = call_user_func(static::$createUrlCallback, $notifiable, $this->token);
        } else {
            $name = isset($notifiable->name) ? $notifiable->name : $notifiable->firstname .' '. $notifiable->lastname; 
            $link = url(config('app.url').route($this->resetPasswordRoute ?: 'password.reset', [
                    'token' => $this->token,
                    'email' => $notifiable->getEmailForPasswordReset(),
                ], false));
            $email_template = EmailTemplate::where('type','reset_password')->first();
            $lang = app()->getLocale();
            $email_template = transformEmailTemplateModel($email_template,$lang);
            $content = $email_template['content'];
            $subject = $email_template['subject'];
            $search = array("{{name}}","{{link}}","{{app_name}}");
            $replace = array($name,$link,env('APP_NAME'));
            $content = str_replace($search,$replace,$content);
        }

        return (new MailMessage)
            ->view('admin.emails.template', ['content' => $content])
            ->subject($subject);
    }
}
