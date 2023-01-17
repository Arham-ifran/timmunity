<?php

namespace App\Jobs;

use App\Mail\ProfileApprovalEmail;
use App\Models\SiteSettings;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Mail;

class ProfileApprovalEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $subject, $details;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($subject, $details)
    {
        $this->subject = $subject;
        $this->details = $details;
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

            $registration_email_from_site_settings   = SiteSettings::first();
            $email_address_array  = explode(',',$registration_email_from_site_settings->registration_email_recipients);
            $data = new ProfileApprovalEmail($this->subject,$this->details);
            Mail::to($email_address_array)->send($data);
        } catch (\Throwable $th) {
            //throw $th;
        }

    }
}
