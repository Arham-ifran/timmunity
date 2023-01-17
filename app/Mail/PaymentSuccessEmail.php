<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentSuccessEmail extends Mailable
{
    use Queueable, SerializesModels;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subject, $data, $quotation_pdf)
    {
        $this->subject = $subject;
        $this->content = $data;
        $this->quotation_pdf = $quotation_pdf;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if(!empty($this->quotation_pdf)){
            $email = $this->subject($this->subject)->view('admin.emails.template')->with('content', $this->content)
                        ->attach($this->quotation_pdf, [
                            'mime' => 'application/pdf',
                        ]);
        }else{
            $email = $this->subject($this->subject)->view('admin.emails.template')->with('content', $this->content);

        }

    }
}