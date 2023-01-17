<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendLicenseEmail extends Mailable
{
    use Queueable, SerializesModels;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subject, $data, $file=null)
    {
        $this->subject = $subject;
        $this->content = $data;
        $this->file = $file;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if(isset($this->file)){
            $email = $this->subject($this->subject)->view('admin.emails.template')->with('content', $this->content)
                        ->attach($this->file);
        }else{
            $email = $this->subject($this->subject)->view('admin.emails.template')->with('content', $this->content);
        }
    }
}
