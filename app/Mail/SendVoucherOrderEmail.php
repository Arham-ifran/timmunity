<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendVoucherOrderEmail extends Mailable
{
     use Queueable, SerializesModels;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subject, $content, $details)
    {
        $this->subject = $subject;
        $this->content = $content;
        $this->details = $details;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        //    return $this->subject($this->subject)->view('admin.emails.template', ['content' => $this->content])->attach($this->details['excel_url']);
           return $this->subject($this->subject)->view('admin.emails.template', ['content' => $this->content]);
    }
}
