<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\LanguageTranslation;
use App\Models\LanguageModule;
use App\Models\Languages;
use Mail;

class SecondaryPlatformAccountGeneration implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $secondary_platforms, $user,  $user_name ,$duration_months;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($secondary_platforms, $user,  $user_name ,$duration_months)
    {
        $this->secondary_platforms = $secondary_platforms;
        $this->user = $user;
        $this->user_name = $user_name;
        $this->duration_months = $duration_months;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            createAccountOnSecondaryPlatforms($this->secondary_platforms, $this->user,  $this->user_name ,$this->duration_months);
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
}
