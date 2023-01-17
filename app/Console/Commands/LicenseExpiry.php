<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Traits\FSecureTrait;
use App\Models\License;

class LicenseExpiry extends Command
{
    use FSecureTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:LicenseExpiry';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Expire License wen date passed';

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
        $licenses_with_expiry_date = License::where('expired', 0)->whereNotNull('expiry_date')->get();
        foreach( $licenses_with_expiry_date as $license )
        {
            if(\Carbon\Carbon::parse($license->expiry_date)->isPast())
            {
                $response = $this->cancelLicenseHelper($license->product_id , $license->license_key );
                $license->expired = 1;
                $license->status = 3;
                $license->save();
            }
        }
        // dd($licenses_with_expiry_date);
        $this->info('The expired licenses has been suspended.');
    }
}
