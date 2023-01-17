<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Products;
use App\Models\ProductVariation;
use App\Models\EmailTemplate;
use App\Models\SiteSettings;
use Auth;
use File;
use PDF;
use Carbon\Carbon;

class ProductLicenses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:ProductLicenses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Low License follow up emails';

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
     * @return int
     */
    public function handle()
    {
        $data =[];
        $settings = SiteSettings::first();
        // $data['products'] = Products::whereHas('licenses')->get();
        $data['products'] = Products::where('product_type',0)->get();
        $flag = 0;
        $reminder = 0;
        $flags_arr = array();
        $interval_arr = array();
        $current_date = Carbon::now()->format('d-m-Y');
        $data['low_licensed_products'] = array();
        // Loop through all products
        foreach($data['products'] as $product){
            if($product->variations){   // If product is variable / has variations
                $data['low_licensed_product_variations'] = array();
                foreach($product->variations as $variation) // Loop through the variations
                {
                    if($variation->available_license_count  < $settings->license_count_low_notification_threshold) // If the un-used license count is less thant the threshold
                    {
                        $last_notification_date = Carbon::parse($variation->last_low_key_notify_time);  // Last low Notification Time
                        $notification_date = $last_notification_date->addDays($settings->low_license_notification_duration)->format('d-m-Y');   // Next notification time
                        // Update the  license notify count and last notification time for the variation
                        $flag = $variation->license_keys_notify_flag == null ? 1 : $variation->license_keys_notify_flag+1;
                        // If the notification is to be sent
                        if( $flag == 1 || $current_date == $notification_date) {
                            ProductVariation::where('id', $variation->id)->update(
                                    [
                                    'license_keys_notify_flag' => $flag,
                                    'last_low_key_notify_time' => Carbon::now()
                                ]
                            );
                            $low_variation = (object)array();
                            $low_variation->product = $product->product_name.' '.$variation->variation_name;
                            $low_variation->available_license_count = $variation->available_license_count;
                            $low_variation->flag = $flag;
                            array_push($data['low_licensed_products'],$low_variation);
                        }
                    }
                }
                // array_push($data['low_licensed_products'],$data['low_licensed_product_variations']);
            }
            // If the product has no variation
            if(count($product->variations) == 0) {
                if($product->available_license_count < $settings->license_count_low_notification_threshold) // If the un-used license count is less thant the threshold
                {
                    $last_notification_date = Carbon::parse($product->last_low_key_notify_time);
                    $notification_date = $last_notification_date->addDays($settings->low_license_notification_duration)->format('d-m-Y');

                    $flag = $product->license_keys_notify_flag == null ? 1 : $product->license_keys_notify_flag+1;
                    // If the notification is to be sent
                    if( $flag == 1 || $current_date == $notification_date) {
                        Products::where('id', $variation->id)->update(
                            [
                                'license_keys_notify_flag' => $flag,
                                'last_low_key_notify_time' => Carbon::now()
                            ]
                        );
                        $low_variation = (object)array();
                        $low_variation->product = $product->product_name;
                        $low_variation->available_license_count = $product->available_license_count;
                        $low_variation->flag = $flag;
                        array_push($data['low_licensed_products'],$low_variation);
                        array_push($interval_arr, $notification_date);
                    }

                }
            }
        }

        if(count($data['low_licensed_products']) > 0){
            $data['flag'] = $flag;
            // Make And Save Low License Key Detail File
            $pdf = PDF::loadView('admin.license.low-licenses-attachment',$data);
            $upload_path = public_path() . '/storage/LowLicenses/' ;
            $fileName =  'low-licenes-key-'. time() .'.pdf' ;
            if (File::exists($upload_path . $fileName)) {
                unlink($upload_path.$fileName);
            }
            if (!File::exists(public_path() . '/storage/LowLicenses/')) {
                File::makeDirectory($upload_path, 0777, true);
            }
            $pdf->save($upload_path . $fileName);
            $attachment_path =  $upload_path . $fileName;
            // Transformation Of Low License Count Email
            $name = 'Admin';
            $emails = explode(',',$settings->low_license_email_recipients);
            $email_template = EmailTemplate::where('type','low_license_key_count_notification')->first();
            $lang = app()->getLocale();
            $email_template = transformEmailTemplateModel($email_template,$lang);
            $content = $email_template['content'];
            $subject = $email_template['subject'];
            $search = array("{{name}}","{{app_name}}");
            $replace = array($name,env('APP_NAME'));
            $content = str_replace($search,$replace,$content);
            dispatch(new \App\Jobs\LowLicenseKeysJob($emails,$subject,$content,$attachment_path));
        }
        // dd($data['low_licensed_products']);

        $this->info('Notfication has been send successfully.');
    }
}
