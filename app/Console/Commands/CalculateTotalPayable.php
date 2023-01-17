<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\VoucherPayment;
use Illuminate\Support\Facades\Log;

class CalculateTotalPayable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:CalculateTotalPayable';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'CalculateTotalPayable';

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
        $voucher_payments = VoucherPayment::get();
        $data['total_payments'] = 0;
        foreach($voucher_payments as $ind => $voucher_payment)
        {
            $data['total_payments'] += $voucher_payment->total_payable;
            $voucher_payment->total_payable_amount = $voucher_payment->total_payable;

        }
    }
}
