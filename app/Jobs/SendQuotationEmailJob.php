<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Mail\SendQuotationEmail;
use Mail;

class SendQuotationEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $email, $subject, $details,$attachments;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($email, $subject, $details,$attachments)
    {
        $this->email = $email;
        $this->subject = $subject;
        $this->details = $details;
        $this->attachments = $attachments;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            //code...

            $data = new SendQuotationEmail($this->subject,$this->details,$this->attachments);
            Mail::to($this->email)->send($data);

        } catch (\Throwable $th) {
            //throw $th;
        }
    }
}
