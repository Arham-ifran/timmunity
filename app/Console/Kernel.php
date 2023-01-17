<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\Contact;
use App\Models\SiteSettings;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\VoucherPaymentInvoices::class,
        Commands\ProductLicenses::class,
        Commands\DisableUsers::class,
        Commands\SoftDeleteUsers::class,
        Commands\VerifyResellerDomains::class,
        Commands\LicenseExpiry::class,
        Commands\DistributorVoucherPaymentInvoices::class,
        Commands\ClearTelescope::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {

        $schedule->command('command:DisableUsers')->dailyAt('16:00');
        $schedule->command('command:SoftDeleteUsers')->dailyAt('16:00');
        $schedule->command('command:ProductLicenses')->everyFiveMinutes();
        // $schedule->command('command:verifyResellerDomainDns')->everyFiveMinutes();
        $schedule->command('command:LicenseExpiry')->dailyAt('23:00');
        // $schedule->command('command:VoucherPaymentInvoices')->fridays()->at('17:00');
        // $schedule->command('command:VoucherPaymentInvoices')->cron('0 17 */28 * 3 ');
        $schedule->command('command:DistributorVoucherPaymentInvoices')->everyMinute();
        $schedule->command('command:ClearTelescope')->weekly();
        // $schedule->command('command:ClearTelescope')->everyMinute();

        $site_settings = SiteSettings::first();
        $reseller_invoice_cron_day = $site_settings->reseller_invoice_cron_day;
        $reseller_invoice_cron_days_duration = $site_settings->reseller_invoice_cron_days_duration;

        $resellers = Contact::where('type',3)->get();


        foreach($resellers as $reseller){
            
            $reseller_invoice_cron_day = $site_settings->reseller_invoice_cron_day;
            $reseller_invoice_cron_days_duration = $site_settings->reseller_invoice_cron_days_duration;
            
            if($reseller->reseller_invoice_cron_day != null){
                $reseller_invoice_cron_day = $reseller->reseller_invoice_cron_day;
            }
            
            if($reseller->reseller_invoice_cron_days_duration != null){
                $reseller_invoice_cron_days_duration = $reseller->reseller_invoice_cron_days_duration;
                
            }
            $cron = '0 17 */'.$reseller_invoice_cron_days_duration.' * '.$reseller_invoice_cron_day;
            $cron = '* * */'.$reseller_invoice_cron_days_duration.' * '.$reseller_invoice_cron_day;
            if($reseller_invoice_cron_days_duration == 0){
                $cron = '0 17 * * *';
                // $cron = '* * * * *';
            }
            // $cron = '14 11 * * *';
            
            $a = $schedule->command('command:VoucherPaymentInvoices '.$reseller->user->id)->cron($cron);
            // $a = $schedule->command('command:VoucherPaymentInvoices '.$reseller->user->id)->everyMinute();
            Log::info('reseller_id', [$reseller->user->id, $cron]);
            Log::info('', []);
            Log::info('', []);
            Log::info('time', [Carbon::now()]);
            Log::info('', []);
            // Log::info('a', [$a]);
        }
    }


    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
