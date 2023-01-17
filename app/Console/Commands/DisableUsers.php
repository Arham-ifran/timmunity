<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\User;
use App\Models\EmailTemplate;
use Illuminate\Support\Facades\Storage;
use App\Jobs\UserDisabledEmailJob;
use DB;

class DisableUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:DisableUsers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Disable users after specific number of days.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $account_inactivity_time_limit_days =  settingValue('account_inactivity_time_limit');

        if(isset($account_inactivity_time_limit_days))
        {
            $users = User::where('is_active', '=' , 1)->whereNotNull('last_active_at')->get();

            if(!$users->isEmpty())
            {
                foreach ($users as $user)
                {
                    $user_disabled_date = Carbon::createFromTimeStamp(strtotime($user->last_active_at), "UTC")->addDays($account_inactivity_time_limit_days);
                    $current_date = Carbon::now('UTC');

                    if($current_date->gt($user_disabled_date))
                    {
                        $user->update([
                            'is_active' => 0,
                            'disabled_at' => date("Y-m-d H:i:s")
                        ]);

                        $email_template = EmailTemplate::where('type','account_disabled_after_inactivity')->first();
                        $name = $user->name;
                        $email = $user->email;
                        $contact_link = url('/contact-us');
                        $subject = $email_template->subject;
                        $content = $email_template->content;

                        $search = array("{{name}}","{{no_of_days}}","{{contact_link}}","{{app_name}}");
                        $replace = array($name,$account_inactivity_time_limit_days,$contact_link,env('APP_NAME'));
                        $content  = str_replace($search,$replace,$content);
                        dispatch(new \App\Jobs\UserDisabledEmailJob($email,$subject,$content));
                        // sendEmail($email, $subject, $content);
                    }
                }
            }
        }
    }
}
