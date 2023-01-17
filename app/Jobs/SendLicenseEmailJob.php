<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Mail\SendLicenseEmail;
use Mail;

class SendLicenseEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $email, $subject, $details, $file;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($email, $subject, $details, $file = null)
    {
        $this->email = $email;
        $this->subject = $subject;
        $this->details = $details;
        $this->file = $file;
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

            $data = new SendLicenseEmail($this->subject,$this->details,$this->file);
            Mail::to($this->email)->send($data);

        } catch (\Throwable $th) {
            //throw $th;
        }
    }
}
