<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Mail\SendOrderEmail;
use Mail;
use App\Models\SiteSettings;

class SendOrderEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $email, $subject, $details,$quotation_pdf;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($email, $subject, $details,$quotation_pdf)
    {
        $this->email = $email;
        $this->subject = $subject;
        $this->details = $details;
        $this->quotation_pdf = $quotation_pdf;
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
        
            $data = new SendOrderEmail($this->subject,$this->details,$this->quotation_pdf);
            $order_emails_from_site_settings   = SiteSettings::first();
            $bcc_email_array  = explode(',',$order_emails_from_site_settings->orders_bcc_email_recipients);
            Mail::to($this->email)->bcc($bcc_email_array)->send($data);

        } catch (\Throwable $th) {
            //throw $th;
        }
    }
}
