<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendVoucherPaymentEmail extends Mailable
{
     use Queueable, SerializesModels;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subject, $data, $pdf)
    {
        $this->subject = $subject;
        $this->content = $data;
        $this->pdf = $pdf;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->subject)->view('admin.emails.template', ['content' => $this->content])->attach($this->pdf, [
            'as' => 'Invoice.pdf',
            'mime' => 'application/pdf',
        ]);
    }
    // public function build()
    // {
    //     return $this->subject("Voucher Redeemed!")->view('admin.emails.voucher-redeem-email', ['email_data' => $this->email_data]);
    // }
}
