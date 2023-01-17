<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Mail\SentInvitationMail;
use Mail;
use App\Models\SiteSettings;
use App\Models\Contact;
use Illuminate\Support\Facades\Log;

class RegistrationEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $email, $subject, $details, $type;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($email, $subject, $details, $type = "Contact")
    {
        $this->email = $email;
        $this->subject = $subject;
        $this->details = $details;
        $this->type = $type;
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
            $bcc_email_array = [];

            $contact_detail   = Contact::where('email', $this->email)->first();
            if(isset($contact_detail->type) && $contact_detail->type == 3){
                $bcc_email_array  = explode(',',$registration_email_from_site_settings->registration_email_recipients);
            }

            $data = new SentInvitationMail($this->subject,$this->details);
            if(count($bcc_email_array) == 0){
                Mail::to($this->email)->send($data);

            }else{
                Mail::to($this->email)->bcc($bcc_email_array)->send($data);
            }

        } catch (\Throwable $th) {
            //throw $th;
        }

    }
}
