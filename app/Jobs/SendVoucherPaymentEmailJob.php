<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Mail\SendVoucherPaymentEmail;
use Mail;

class SendVoucherPaymentEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $email, $subject, $details, $pdf;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($email, $subject, $details, $attachment)
    {
        $this->email = $email;
        $this->subject = $subject;
        $this->details = $details;
        $this->pdf = $attachment;
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

            $data = new SendVoucherPaymentEmail($this->subject,$this->details, $this->pdf);
            Mail::to($this->email)->send($data);

        } catch (\Throwable $th) {
            //throw $th;
        }
    }
}
