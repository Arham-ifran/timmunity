<?php

namespace App\Http\Controllers;

use App\Models\EmailTemplate;
use App\Models\License;
use App\Models\Products;
use App\Models\ProductVariation;
use App\Models\SiteSettings;
use App\Models\Voucher;
use App\Models\VoucherOrder;
use App\Models\VoucherPayment;
use App\Models\VoucherPaymentOrderDetail;
use Illuminate\Http\Request;
use Artisan;
use PDF;
use Carbon\Carbon;
use File;
use Hashids;

class CommonController extends Controller
{
	public function complete_setup(Request $request)
	{
		$exitCode = Artisan::call('storage:link');
		$exitCode = Artisan::call('cache:clear');
		$exitCode = Artisan::call('optimize:clear');
		$exitCode = Artisan::call('route:clear');
		$exitCode = Artisan::call('view:clear');
		$exitCode = Artisan::call('route:clear');
		$exitCode = Artisan::call('route:cache');
		echo 'Cleared';
	}
    public function deletelicense(){
        $file = public_path('storage/licenses/import/123.csv');
        $importArr = $this->csvToArray($file);
        // dd($importArr);
        $i=1;
        foreach ($importArr as $index =>  $row)
        {
            $variation = ProductVariation::where('sku', $row['sku'])->first();
            $license = License::where('license_key', $row['license_key'])->where('variation_id', $variation->id)->first();
            if($license && $license->is_used == 0){
                echo($i++);
                echo('-');
                License::where('license_key', $row['license_key'])->where('variation_id', $variation->id)->delete();
            }
        }
    }
    public function csvToArray($filename = '', $delimiter = ',')
    {
        if (!file_exists($filename) || !is_readable($filename))
            return false;

        $header = null;
        $data = array();
        if (($handle = fopen($filename, 'r')) !== false)
        {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false)
            {
                if (!$header)
                    $header = $row;
                else
                    $data[] = array_combine($header, $row);
            }
            fclose($handle);
        }

        return $data;
    }

    public function check(){
       /********** Distributor Invoices Start **********/
       $pending_payment_orders = VoucherOrder::whereHas('vouchers', function($query){
        $query->where('is_invoiced',0);
        $query->where('status',0);
    })->where(function($q){
        $q->where('reseller_id',0);
        $q->orWhere('reseller_id',null);
    })->orderBy('distributor_id','desc')->get();
    $distributor_data = array();
    $payload = [];
    $currency = null;
    $currency_symbol = null;
    $exchange_rate = 1;
    dd($pending_payment_orders);
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

            if( !isset( $distributor_data[$voucher_order->distributor_id] ) )
            {
                $distributor_data[$voucher_order->distributor_id] = [];
            }

            $payload = [];
            $payload['voucherOrder'] = $voucher_order;
            $payload['voucherOrderID'] = $voucher_order->id;
            $payload['voucherOrderProductID'] = $voucher_order->product_id;
            $payload['voucherOrderVariationID'] = $voucher_order->variation_id;

            $payload['voucherIDs'] = Voucher::where('order_id', $voucher_order->id)->where(function($query){
                $query->where('redeemed_at','!=', null);
                $query->whereDate('redeemed_at', '<=', \Carbon\Carbon::now());
            })->where('is_invoiced',0)->pluck('id')->toArray();
            array_push($distributor_data[$voucher_order->distributor_id], $payload);
            Voucher::whereIn('id', $payload['voucherIDs'])->update(['is_invoiced' => 1]);
        }
        foreach($distributor_data as $distributor_id => $data)
        {
            $voucher_payment = new VoucherPayment();
            $voucher_payment->is_paid = 0;
            $voucher_payment->payload = json_encode($payload);
            $voucher_payment->save();
            foreach($data as $d)
            {
                $voucher_payment_order_detail = new VoucherPaymentOrderDetail();
                $voucher_payment_order_detail->voucher_payment_id = $voucher_payment->id;
                $voucher_payment_order_detail->voucher_order_id = $d['voucherOrderID'];
                $voucher_payment_order_detail->voucher_ids = implode(',',$d['voucherIDs']);
                $voucher_payment_order_detail->distributor_id = $distributor_id;
                $voucher_payment_order_detail->save();
            }
            $voucher_payment->currency_symbol = $currency == false ? '€' : $currency_symbol ;
            $voucher_payment->currency = $currency == false ? 'EUR' : $currency;
            $voucher_payment->exchange_rate = $currency == false ? 1 : $exchange_rate;
            $voucher_payment->payload = json_encode($data);
            $voucher_payment->total_amount = number_format($voucher_payment->total_payable * $exchange_rate,2);
            $voucher_payment->save();

            $payment_relif_days = SiteSettings::first()->payment_relief_days;
            $name = $d['voucherOrder']->distributor->name;
            $email = $d['voucherOrder']->distributor->email;
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
            dispatch(new \App\Jobs\SendVoucherOrderEmailJob($email,$subject,$content,$details));
        }

    }
    }
}
