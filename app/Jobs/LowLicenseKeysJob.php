<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Mail\LowLicenseKeysMail;
use Mail;

class LowLicenseKeysJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $emails, $subject, $details, $attachment;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($emails, $subject, $details, $attachment)
    {
        $this->emails = $emails;
        $this->subject = $subject;
        $this->details = $details;
        $this->attachment = $attachment;
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

            $data = new LowLicenseKeysMail($this->subject,$this->details,$this->attachment);
           Mail::to($this->emails)->send($data);
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
}
