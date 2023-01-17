<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\EmailTemplate;
use App\Models\Products;
use App\Models\ProductVariation;
use App\Models\SiteSettings;
use App\Models\User;
use App\Models\Voucher;
use App\Models\VoucherOrder;
use App\Models\VoucherPayment;
use App\Models\VoucherPaymentOrderDetail;
use Hashids;
use Illuminate\Support\Facades\Log;

class VoucherPaymentInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:VoucherPaymentInvoices {reseller_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Voucher Payment Inovices';

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
        
            /********** Reseller Invoices Start **********/
            $pending_payment_orders = VoucherOrder::with('vouchers')->whereHas('vouchers', function($query){
                $query->where('is_invoiced',0);
                $query->where('status',0);
            })->where('reseller_id',$this->argument('reseller_id'))->orderBy('reseller_id','desc')->get();

            $user = User::where('id',$this->argument('reseller_id'))->first();
            Log::info('$pending_payment_orders',[$pending_payment_orders]);
            $reseller_data = array();
            $payload = [];
            $currency = null;
            $currency_symbol = null;
            $exchange_rate = 1;
            if(count($pending_payment_orders) > 0){
                foreach($pending_payment_orders as $voucher_order)
                    {
                    if( $currency == null )
                    {
                        $currency = $voucher_order->currency;
                        $currency_symbol = $voucher_order->currency_symbol;
                        $exchange_rate = $voucher_order->exchange_rate;
                    }
                    else if($currency != false)
                    {
                        $currency = $voucher_order->currency == $currency ? $voucher_order->currency : false;
                        $currency_symbol = $voucher_order->currency_symbol == $currency_symbol ? $voucher_order->currency_symbol : false;
                    }
                    
                    if( !isset( $reseller_data[$voucher_order->reseller_id] ) )
                    {
                        $reseller_data[$voucher_order->reseller_id] = [];
                    }
                    
                    $payload = [];
                    $payload['voucherOrder'] = $voucher_order;
                    $payload['voucherOrderID'] = $voucher_order->id;
                    $payload['voucherOrderProductID'] = $voucher_order->product_id;
                    $payload['voucherOrderVariationID'] = $voucher_order->variation_id;
                    
                    $payload['voucherIDs'] = Voucher::where('order_id', $voucher_order->id)->where('status',0)->where('is_invoiced',0)->pluck('id')->toArray();
                    
                    array_push($reseller_data[$voucher_order->reseller_id], $payload);
                    
                    Voucher::whereIn('id', $payload['voucherIDs'])->update(['is_invoiced' => 1]);
                }
                foreach($reseller_data as $reseller_id => $data)
                {
                    $voucher_payment = new VoucherPayment;
                    $voucher_payment->is_paid = 0;
                    $voucher_payment->payload = json_encode($payload);
                    $voucher_payment->save();
                    foreach($data as $d)
                    {
                        $voucher_payment_order_detail = new VoucherPaymentOrderDetail();
                        $voucher_payment_order_detail->voucher_payment_id = $voucher_payment->id;
                        $voucher_payment_order_detail->voucher_order_id = $d['voucherOrderID'];
                        $voucher_payment_order_detail->voucher_ids = implode(',',$d['voucherIDs']);
                        $voucher_payment_order_detail->reseller_id = $reseller_id;
                        $voucher_payment_order_detail->save();
                        
                        $voucher_payment_order_detail_payment = $voucher_payment_order_detail->total_payable;
                        $voucher_payment_order_detail->pending_payment = $voucher_payment_order_detail_payment;
                        $voucher_payment_order_detail->save();
                        
                        $v_order = VoucherOrder::where('id',$d['voucherOrderID'])->first();
                        $v_order->pending_payment = $v_order->pending_payment + $voucher_payment_order_detail_payment;
                        $v_order->save();
                    }
                    $voucher_payment->currency_symbol = $currency == false ? '€' : $currency_symbol ;
                    $voucher_payment->currency = $currency == false ? 'EUR' : $currency;
                    $voucher_payment->exchange_rate = $currency == false ? 1 : $exchange_rate;
                    $voucher_payment->payload = json_encode($data);
                    $voucher_payment->total_amount = number_format($voucher_payment->total_payable * $exchange_rate,2);
                    $voucher_payment->updated_at = Carbon::now();
                    $voucher_payment->save();

                    $payment_relif_days = SiteSettings::first()->payment_relief_days;
                    $name = $data[0]['voucherOrder']->reseller->name;
                    $email = $data[0]['voucherOrder']->reseller->email;
                    $link = route("frontside.reseller.voucher.payment", Hashids::encode($voucher_payment->id));
                    $email_template = EmailTemplate::where('type','vouchers_payment_generated')->first();
                    $lang = app()->getLocale();
                    $email_template = transformEmailTemplateModel($email_template,$lang);
                    $content = $email_template['content'];
                    $subject = $email_template['subject'];
                    $search = array("{{name}}","{{order_id}}","{{link}}","{{app_name}}","{{no_of_days}}","{{contact_link}}");
                    $replace = array($name,'$order_id',$link,env('APP_NAME'),$payment_relif_days,route('frontside.contact.index'));
                    $content = str_replace($search,$replace,$content);
                    $details['excel_url'] = $voucher_payment->invoice_pdf;

                    dispatch(new \App\Jobs\SendVoucherPaymentEmailJob($email,$subject,$content,$details['excel_url']));
                }

            }
            

    //     /********** Reseller Invoices End **********/


    }
}
