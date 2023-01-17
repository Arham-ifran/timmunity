<?php

namespace App\Console\Commands;

use Illuminate\Http\Request;
use Illuminate\Console\Command;
use App\Models\ResellerRedeemedPage;
use Artisan;


class VerifyResellerDomains extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:verifyResellerDomainDns';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Match reseller domain with server ip and update url of reseller';

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
    public function handle(Request $request)
    {
        \Log::info("Cron is working fine!");
        $reseller_redeem_pages  = ResellerRedeemedPage::where('domain','!=','')->where('is_domain_verified',0)->get();

        foreach($reseller_redeem_pages as $ind => $reseller_redeem_page){

            $dns_lookup_response = dns_get_record($reseller_redeem_page->domain, DNS_A);
            if( array_search(env('server_ip'), array_column($dns_lookup_response, 'ip')) !== false )
            {
                $url = isset($reseller_redeem_page->url) ? $reseller_redeem_page->url : '';
                $url_exploded = explode("/", parse_url($url, PHP_URL_PATH));
                $last_two_segments_sliced = array_slice($url_exploded, -2, count($url_exploded), true);
                $last_two_segments = implode('/',$last_two_segments_sliced);
                $new_url = $reseller_domain.'/'.$last_two_segments;
                $new_url = str_replace(' ','',$new_url);

                $reseller_redeem_pages  = ResellerRedeemedPage::where('domain', $reseller_redeem_page->domain)->update([

                    'is_domain_verified' => 1,
                    'url' => $new_url
                ]);
            }
            // $dns_lookup_response = dns_get_record($reseller_redeem_page->domain, DNS_A);
            // if( array_search(env('server_ip'), array_column($dns_lookup_response, 'ip')) !== false )
            // {
            //     $url            = isset($reseller_redeem_page->url)?$reseller_redeem_page->url:'';
            //     // $current_url    = \URL::current();
            //     $current_url    = env('reseller_domain');
            //     $new_url        = str_replace($current_url,$reseller_redeem_page->domain,$url);
            //     $new_url        = str_replace(' ', '',$new_url);
            //     $ip_based_url   = 'https://'.env('server_ip').$new_url;
            //     $ip_based_url        = str_replace(' ', '',$ip_based_url);
            //     $reseller_redeem_pages  = ResellerRedeemedPage::where('domain', $reseller_redeem_page->domain)->update([

            //         'is_domain_verified' => 1,
            //         'url' => $ip_based_url
            //     ]);
            // }
        }
    }
}
