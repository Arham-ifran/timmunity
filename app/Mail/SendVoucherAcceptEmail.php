<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendVoucherAcceptEmail extends Mailable
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
        return $this->subject($this->subject)->view('admin.emails.template', ['content' => $this->content])->attach($this->details['excel_url']);
    }
    // public function build()
    // {
    //     return $this->subject("Voucher Approved!")->view('admin.emails.voucher-approved')->with( 'email_data' , $this->email_data)
    //     ->attach($this->email_data['excel_url']);
    // }
}
