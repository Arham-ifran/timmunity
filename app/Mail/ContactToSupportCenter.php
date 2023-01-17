<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\SiteSettings;

class ContactToSupportCenter extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($from_email,$subject, $content)
    {
        $content = $content;
        $site_settings = SiteSettings::first();

        // $this->from($from_email);
        $this->from($site_settings->inquiry_email, $site_settings->site_name);
        $this->to($site_settings->inquiry_email);

        $this->subject($subject);

        $this->viewData = compact('content','content');
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('admin.emails.template');
    }
}
