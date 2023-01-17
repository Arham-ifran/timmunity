<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LowLicenseKeysMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subject, $data, $attached_file)
    {
        $this->subject = $subject;
        $this->content = $data;
        $this->attached_file = $attached_file;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->subject)->view('admin.emails.template', ['content' => $this->content])->attach($this->attached_file, [
            'mime' => 'application/pdf',
        ]);
    }
}
