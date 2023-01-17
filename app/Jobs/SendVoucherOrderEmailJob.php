<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Mail\SendVoucherOrderEmail;
use Mail;
use App\Models\SiteSettings;

class SendVoucherOrderEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $email, $subject, $content, $details;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($email, $subject, $content, $details)
    {
        $this->email = $email;
        $this->subject = $subject;
        $this->content = $content;
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

            $data = new SendVoucherOrderEmail($this->subject,$this->content,$this->details);
            $order_emails_from_site_settings   = SiteSettings::first();
            $bcc_email_array  = explode(',',$order_emails_from_site_settings->orders_bcc_email_recipients);

            Mail::to($this->email)->bcc($bcc_email_array)->send($data);

        } catch (\Throwable $th) {
            //throw $th;
        }
    }
}
