<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Mail\SendOrderDraftEmail;
use Mail;

class SendOrderDraftEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $email, $subject, $details,$quotation_pdf;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($email, $subject, $details)
    {
        $this->email = $email;
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

            $data = new SendOrderDraftEmail($this->subject,$this->details);
            Mail::to($this->email)->send($data);

        } catch (\Throwable $th) {
            //throw $th;
        }
    }
}
