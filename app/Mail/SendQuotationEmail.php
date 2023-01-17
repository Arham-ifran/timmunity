<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendQuotationEmail extends Mailable
{
    use Queueable, SerializesModels;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subject, $data, $attachments)
    {
        $this->subject = $subject;
        $this->content = $data;
        $this->attachments_list = $attachments;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
            if ($this->attachments_list) {
            
                $email =  $this->subject($this->subject)->view('admin.emails.template')->with('content', $this->content);
                foreach($this->attachments_list as $file) {
                    $email->attach($file, [
                                    'mime' => 'application/pdf',
                                ]);
                }
             
            }
    }
}
