<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\User;
use App\Models\EmailTemplate;
use Illuminate\Support\Facades\Storage;
use DB;

class AccountInactivityEmailNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'account_inactivity:notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email notifications about account inactivity to users after specific number of days.';

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
        $first_notification_days =  settingValue('account_inactivity_first_notification');
        $second_notification_days =  settingValue('account_inactivity_second_notification');
        $third_notification_days =  settingValue('account_inactivity_third_notification');

        if(isset($account_inactivity_time_limit_days) && (isset($first_notification_days) || isset($second_notification_days) || isset($third_notification_days)))
        {
            $first_notification_date = isset($first_notification_days) ? Carbon::now('UTC')->subDays($account_inactivity_time_limit_days)->addDays($first_notification_days)->format('Y-m-d') : '1970-01-01';
            $second_notification_date = isset($second_notification_days) ? Carbon::now('UTC')->subDays($account_inactivity_time_limit_days)->addDays($second_notification_days)->format('Y-m-d') : '1970-01-01';
            $third_notification_date = isset($third_notification_days) ? Carbon::now('UTC')->subDays($account_inactivity_time_limit_days)->addDays($third_notification_days)->format('Y-m-d') : '1970-01-01';

            $users = User::where('status', '=' , 1)->whereRaw('DATE(last_active_at) = ?',$first_notification_date)->orWhereRaw('DATE(last_active_at) = ?',$second_notification_date)->orWhereRaw('DATE(last_active_at) = ?',$third_notification_date)->get();

            if(!$users->isEmpty())
            {
                foreach ($users as $user)
                {
                    $disable_date_human_readable = Carbon::createFromTimeStamp(strtotime($user->last_active_at), "UTC")->addDays($account_inactivity_time_limit_days)->format('d M, Y');
                    $last_active_at = Carbon::createFromTimeStamp(strtotime($user->last_active_at), "UTC")->format('Y-m-d');

                    $remaining_no_of_days = 0;

                    if($first_notification_date == $last_active_at)
                    {
                        $remaining_no_of_days = $first_notification_days;
                    }
                    else if($second_notification_date == $last_active_at)
                    {
                        $remaining_no_of_days = $second_notification_days;
                    }
                    else if($third_notification_date == $last_active_at)
                    {
                        $remaining_no_of_days = $third_notification_days;
                    }

                    if($remaining_no_of_days != 0)
                    {
                        // **********************************************************************  //
                        // Send Email About account inactivity notification after number of days  //
                        // **********************************************************************  //

                        if($remaining_no_of_days == 1)
                        {
                            $remaining_no_of_days = 'tomorrow';
                        }
                        else
                        {
                            $remaining_no_of_days = $remaining_no_of_days. ' days';
                        }

                        $email_template = EmailTemplate::where('type','account_inactivity_follow_up_email')->first();
                        $name = $user->name;
                        $email = $user->email;
                        $login_link = url('/signin');
                        $contact_link = url('/contact-us');
                        $subject = $email_template->subject;
                        $content = $email_template->content;

                        $search = array("{{name}}","{{remaining_days}}","{{disable_date}}","{{login_link}}","{{contact_link}}","{{app_name}}");
                        $replace = array($name,$remaining_no_of_days,$disable_date_human_readable,$login_link,$contact_link,env('APP_NAME'));
                        $content  = str_replace($search,$replace,$content);

                        sendEmail($email, $subject, $content);
                    }
                }
            }
        }
    }
}
