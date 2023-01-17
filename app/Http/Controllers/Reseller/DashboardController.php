<?php

namespace App\Http\Controllers\Reseller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VoucherOrder;
use App\Models\Voucher;
use App\Models\VoucherPayment;
use App\Models\Products;
use App\Models\ContactCountry;
use App\Models\VoucherOrderTax;
use App\Models\ProductVariation;
use App\Models\Contact;
use App\Models\License;
use App\Http\Traits\AdminNotificationTrait;
use Yajra\DataTables\DataTables;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use App\Models\EmailTemplate;
use App\Models\ResellerRedeemedPage;
use App\Models\VoucherPaymentReference;
use App\Http\Traits\PaymentTrait;
use Auth;
use Carbon\Carbon;
use Hashids;
use Session;
use App\Exports\VouchersExport;
use Maatwebsite\Excel\Facades\Excel;

class DashboardController extends Controller
{
    use PaymentTrait;
    use AdminNotificationTrait;
    /**
     * Reseller Dashboard
     *
     */
    public function dashboard( Request $request )
    {
        $data = [];
        if ($request->ajax()) {
            $data = VoucherOrder::with('vouchers','voucher_taxes','product','variation','product.generalInformation','product.customer_taxes','product.customer_taxes.tax')
                ->where('reseller_id', Auth::user()->id)->orderBy('created_at','desc');
            $datatable = Datatables::of($data);
            $datatable->addColumn('ids', function ($row) {
                return '';
            });
            $datatable->addColumn('order_id', function ($row) {
                return str_replace(' ','',$row->reseller->name).'-'.str_pad($row->id,3,'0',STR_PAD_LEFT).'-'.date('dmY',strtotime($row->created_at));
            });
            $datatable->addColumn('action', function ($row) {
                $html = '<a title="Vouchers" class="" href="'.route("frontside.reseller.vouchers", Hashids::encode($row->id)).'">';
                    $html .= '<button class="btn btn-secondary btn-sm">';
                        $html .= '<i class="fa fa-gift" aria-hidden="true"></i>';
                    $html .= '</button>';
                $html .= '</a>&nbsp;';

                $html .= '<a title="Download Vouchers" href="'.route("frontside.reseller.voucher.order.vouchers.export",Hashids::encode($row->id)) .'" class="">';
                    $html .= '<button class="btn btn-success btn-sm">';
                        $html .= '<i class="fa fa-download" aria-hidden="true"></i>';
                    $html .= '</button>';
                $html .= '</a>';
                return $html;
            });
            $datatable->addColumn('statuss', function ($row) {
                switch ($row->status) {
                    case 0:
                        return '<span class="badge bg-yellow">'.__('Pending').'</span>';
                        break;
                    case 1:
                        return '<span class="badge bg-green">'.__('Approved').'</span>';
                        break;
                    case 2:
                        return '<span class="badge bg-red">'.__('Rejected').'</span>';
                        break;
                }
            });
            $datatable->addColumn('active_status', function ($row) {
                switch ($row->is_active) {
                    case 0:
                        return '<span class="badge bg-yellow">'.__('In Active').'</span>';
                        break;
                    case 1:
                        return '<span class="badge bg-green">'.__('Active').'</span>';
                        break;
                }
            });
            $datatable->addColumn('product_name', function ($row) {
                // $html = $row->product->product_name . ' ' . @$row->variation->variation_name;
                $html = $row->product->product_name.' ' ;
                $html .= $row->product->project == null ? @$row->variation->variation_name : '' ;

                if($row->product->secondary_project_ids != ''){
                    $projects = $row->product->secondary_projects_array;
                    $html .= '<br><strong>Secondary Platforms</strong><br>'.implode(',',$projects);
                }
                return $html;
            });
            $datatable->addColumn('date', function ($row) {
                return \Carbon\Carbon::parse($row->created_at)->format('d/M/Y');
            });
            $datatable->addColumn('discount', function ($row) {
                return @$row->discount_percentage;
            });
            $datatable->editColumn('quantity', function ($row) {
                return @$row->quantity;
            });
            $datatable->editColumn('used_quantity', function ($row) {
                return @$row->used_quantity;
            });
            $datatable->editColumn('remaining_quantity', function ($row) {
                return @$row->remaining_quantity;
            });
            $datatable->addColumn('unit_price', function ($row) {
                return currency_format($row->unit_price * $row->exchange_rate,$row->currency_symbol,$row->currency) ;
            });
            $datatable->addColumn('taxes', function ($row) {
                return $row->taxes;
                $html = '';
                $count = count($row->voucher_taxes);
                foreach($row->voucher_taxes as $ind => $voucher_tax){
                    $html .= $voucher_tax->tax->amount;
                    $html .= $voucher_tax->tax->type==1 ? ' %' : '';
                    if($ind < $count-1){
                        $html.=', ';
                    }
                }
                if($count > 0){
                    $html .= ', '.$row->vat_percentage.'% VAT';

                }else{
                    $html .= $row->vat_percentage.'% VAT';

                }
                return $html;
            });
            $datatable->addColumn('total_payable_amount', function ($row) {
                return currency_format($row->total_payable,$row->currency_symbol,$row->currency);
            });
            $datatable->addColumn('pending_payment', function ($row) {
                // return currency_format($row->remaining_total,$row->currency_symbol,$row->currency);
                return currency_format($row->pending_payment,$row->currency_symbol,$row->currency);
            });
            $datatable->editColumn('street_address', function ($row) {
                return $row->street_address;
            });
            $datatable->editColumn('city', function ($row) {
                return $row->city;
            });
            $datatable->addColumn('country', function ($row) {
                return @$row->contact_country->name;
            });
            $datatable = $datatable->rawColumns(['product_name','active_status','statuss','action']);
            return $datatable->make(true);
        }
        $data['total_orders'] = VoucherOrder::where('reseller_id', Auth::user()->id)->count();
        $data['total_vouchers'] = VoucherOrder::join('vouchers', 'vouchers.order_id', 'voucher_orders.id')->where('voucher_orders.reseller_id', Auth::user()->id)->count();
        $data['used_vouchers'] = VoucherOrder::join('vouchers', 'vouchers.order_id', 'voucher_orders.id')->where('vouchers.status',0)->where('voucher_orders.reseller_id', Auth::user()->id)->count();
        $data['remaining_vouchers'] = VoucherOrder::join('vouchers', 'vouchers.order_id', 'voucher_orders.id')->where('vouchers.status','!=',0)->where('voucher_orders.reseller_id', Auth::user()->id)->count();
        $data['products'] = Products::withCount('variations')->where('is_active', 1)->get();
        $data['countries'] = ContactCountry::all();
        $data['vat_percentage']   = isset(Auth::user()->contact->contact_countries->vat_in_percentage) ? Auth::user()->contact->contact_countries->vat_in_percentage : 0;;
        $data['redeem_page'] = ResellerRedeemedPage::where('reseller_id', Auth::user()->id)->first();

        if(isset(Auth::user()->contact->contact_countries->is_default_vat) && Auth::user()->contact->contact_countries->is_default_vat == 1)
        {
            $data['vat_percentage'] =  \App\Models\SiteSettings::first()->defualt_vat;
        }
        $data['default_vat'] = \App\Models\SiteSettings::first()->defualt_vat;
        return Auth::user()->email_verified_at == null ? view('frontside.reseller.dashboard', $data)->with(session()->flash('alert-warning', __('Your email is unverified! Kindly verify your email.'))) : view('frontside.reseller.dashboard', $data);
    }
    /**
     * View Vouchers
     *
     */
    public function vouchers($voucher_order_id, Request $request)
    {
        $voucher_order_id = Hashids::decode($voucher_order_id)[0];
        if ($request->ajax()) {
            $data = Voucher::with('customer')->where('order_id', $voucher_order_id)->get();
            $datatable = Datatables::of($data);
            $datatable->editColumn('code', function ($row) {
                return $row->code;
            });
            $datatable->addColumn('status', function ($row) {
                switch ($row->status) {
                    case 0:
                        return '<span class="badge  bg-green">'.__('Redeemed').'</span>';
                        break;
                    case 1:
                        return '<span class="badge  badge-light">'.__('Active').'</span>';
                        break;
                    case 2:
                        return '<span class="badge badge-danger">'.__('Disabled').'</span>';
                        break;
                    case 3:
                        return '<span class="badge bg-yellow">'.__('Pending').'</span>';
                        break;
                };
            });
            $datatable->addColumn('customer', function ($row) {
                return $row->customer != null ? @$row->customer->email : $row->email;
            });
            $datatable->addColumn('redeemed_time', function ($row) {
                return $row->redeemed_at ? \Carbon\Carbon::parse($row->redeemed_at)->format('d/M/Y') : '';
            });

            $datatable = $datatable->rawColumns(['status']);
            return $datatable->make(true);
        }
        $data = [];
        $data['ajax_url'] = route("frontside.reseller.vouchers", Hashids::encode($voucher_order_id));
       return Auth::user()->email_verified_at == null ? view('frontside.reseller.voucher',$data)->with(session()->flash('alert-warning', __('Your email is unverified! Kindly verify your email.'))) : view('frontside.reseller.voucher',$data);
    }
    /**
     * View Vouchers Payments
     *
     */
    public function voucherPayments($voucher_order_id, Request $request)
    {
        $voucher_order_id = Hashids::decode($voucher_order_id)[0];
        if ($request->ajax()) {

            $data = VoucherPayment::whereHas('voucher_order',function($query) use($voucher_order_id) {
                $query->where('id', $voucher_order_id);
            })->with('voucher_order','voucher','reseller')->get();
            $datatable = Datatables::of($data);
            $datatable->addColumn('action', function ($row) {
                $html = '';
                if($row->is_paid == 0)
                {
                    $html .= '<a title="Make Payment" class="" href="'.route("frontside.reseller.voucher.payment", Hashids::encode($row->id)).'">';
                        $html .= '<button class="btn btn-secondary btn-sm">';
                            $html .= '<i class="fa fa-link" aria-hidden="true"></i>';
                        $html .= '</button>';
                    $html .= '</a>';
                }
                $html .= '<a title="View Invoice" href="'.$row->invoice_pdf_asset .'" class="">';
                    $html .= '<button class="btn btn-info btn-sm">';
                        $html .= '<i class="fa fa-file-text-o" aria-hidden="true"></i>';
                    $html .= '</button>';
                $html .= '</a>';
                return $html;
            });
            $datatable->addColumn('statuss', function ($row) {
                $html = $row->is_paid == 1 ? ( $row->is_partial_paid == 1 ? __('Partially Paid') : __('Paid') ) : __('Un Paid') ;
                return $html;
            });
            $datatable->addColumn('product', function ($row) {
                // $html = $row->voucher_order->product->product_name . ' ' . @$row->voucher_order->variation->variation_name;
                $html = $row->voucher_order->product->product_name.' ' ;
                $html .= $row->voucher_order->product->project == null ? @$row->voucher_order->variation->variation_name : '' ;
                return $html;
            });
            $datatable->addColumn('vouchers', function ($row) {
                $voucher_ids_array = explode(',', $row->voucher_ids);
                $html = '';
                foreach(Voucher::whereIn('id',$voucher_ids_array)->get() as $voucher){
                    $html .= $voucher->code.'<br>';
                }
                return $html;
            });
            $datatable->addColumn('price_per_voucher', function ($row) {
                $unit_price = $row->voucher_order->unit_price;
                $discount_percentage = $row->voucher_order->discount_percentage;
                $discount_amount = $unit_price * $discount_percentage / 100;

                $html = currency_format(( $unit_price - $discount_amount) * $row->exchange_rate,$row->voucher_order->currency_symbol,$row->voucher_order->currency);
                return $html;
            });
            $datatable->addColumn('quantity', function ($row) {
                $voucher_ids_array = explode(',', $row->voucher_ids);
                $html = count($voucher_ids_array);
                return $html;
            });
            $datatable->addColumn('amount', function ($row) {
                $html = currency_format($row->total_payable * $row->exchange_rate,$row->voucher_order->currency_symbol,$row->voucher_order->currency);
                return $html;
            });
            $datatable->addColumn('taxes', function ($row) {
                $html = '';
                $count = count($row->voucher_order->voucher_taxes);
                foreach($row->voucher_order->voucher_taxes as $ind => $voucher_tax){
                    $html .= $voucher_tax->tax->amount;
                    $html .= $voucher_tax->tax->type==1 ? ' %' : '';
                    if($ind < $count-1){
                        $html.=', ';
                    }
                }
                if($count > 0){
                    $html .=  ', ';
                }
                $html .=  $row->voucher_order->vat_percentage.'% VAT';
                return $html;
            });

            $datatable = $datatable->rawColumns(['vouchers','action']);
            // dd($datatable->make(true));
            return $datatable->make(true);
        }
        $data['ajax_url'] = route("frontside.reseller.voucher.payments",Hashids::encode($voucher_order_id));

        return Auth::user()->email_verified_at == null ? view('frontside.reseller.voucher-payment', $data)->with(session()->flash('alert-warning', __('Your email is unverified! Kindly verify your email.'))) : view('frontside.reseller.voucher-payment', $data);
    }
    /**
     * Save the order placed by reseller for vouchers
     */
    public function generateVoucherSheetExcel($voucher_order_id)
    {
        try {
            $voucher_order_id = Hashids::decode($voucher_order_id)[0];
        } catch (\Throwable $th) {

        }
        $voucher_order = VoucherOrder::where('id',$voucher_order_id)->first();
        $vouchers = Voucher::where('order_id', $voucher_order_id)->get()->toArray();
        $voucher_array[] = array();
        $main_platform = $voucher_order->product->project == null ? 'TIMmunity'  : $voucher_order->product->project->name;
        $voucher_array[] = [
            'Main Platform',
            $main_platform,
        ];
        $product_name = $voucher_order->product->product_name;
        if($voucher_order->variation){
            $product_name .= ' '.$voucher_order->variation->variation_name;
        }
        $voucher_array[] = [
            'Product Name',
            $product_name,
        ];
        $voucher_array[] = [
            'Reseller',
            $voucher_order->reseller->name

        ];
        $voucher_array[] = ['#', 'Voucher Code'];

        foreach ($vouchers as $index => $voucher) {
            $voucher_array[] = [
                '#' => $index+1,
                'Voucher Code' => $voucher['code']
            ];
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        for ($i = 0; $i < count($voucher_array); $i++) {
            //set value for indi cell
            $row = $voucher_array[$i];
            //writing cell index start at 1 not 0
            $j = 1;
            foreach ($row as $x => $x_value) {
                $sheet->setCellValueByColumnAndRow($j, $i + 1, $x_value);
                $j = $j + 1;
            }
        }

        $old_file = public_path().'/storage/vouchers/vouchers-'.Hashids::encode($voucher_order->id).'.xlsx';
        if (file_exists($old_file)) {
            unlink($old_file);
        }
        ob_clean();
        // $writer = new Xlsx($spreadsheet);
        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $writer->save(public_path().'/storage/vouchers/vouchers-'.Hashids::encode($voucher_order->id).'.xlsx');
        return public_path('storage/vouchers/vouchers-'.Hashids::encode($voucher_order->id).'.xlsx');
    }
    public function orderVoucher(Request $request)
    {
        // dd(Auth::user()->total_credit_amount , Auth::user()->credit_limit);
        if(Auth::user()->total_credit_amount > Auth::user()->credit_limit)
        {
            return redirect()->back()->with(session()->flash('alert-warning',__('You have reached your credit limit so you cannot place further orders. Please clear your due payments or contact admin in case of any concerns.')));
        }
        $input = $request->all();
        $product_ids = $input['product_id'];
        $variation_id_index = 0;
        $orders_placed = array();
        
        foreach($product_ids as $ind => $product_id)
        {
            try {
                $product_id = Hashids::decode($product_id)[0];
            } catch (\Throwable $th) {
                //throw $th;
            }
            $check_variation = ProductVariation::where('product_id', $product_id)->first();
            $variation_id = null;
            if( $check_variation )
            {    
                $variation_id = $input['variation_id'][$variation_id_index];
                try {
                    $variation_id = Hashids::decode($variation_id)[0];
                } catch (\Throwable $th) {
                    //throw $th;
                }
            }
            $order = $this->orderVoucherSingle($request, $product_id, $variation_id, $input['quantity'][$ind]);
            array_push($orders_placed, $order);
        }
        return redirect()->route('frontside.reseller.dashboard')->with(session()->flash('alert-success', __('Vouchers ordered successfully')));
    }
    public function orderVoucherSingle($request, $product_id, $variation_id = null, $quantity)
    {
        $input = $request->all();
        $product = Products::with('generalInformation','customer_taxes','customer_taxes.tax')->where('id', $product_id)->first();
        $extra_price = 0;
        // $unit_price = $product->generalInformation->sales_price;
        $currency_acceptance = checkCurrencyAcceptibility(Session::get('currency_code'));
        $variation = null;
        if($variation_id != null)
        {
            $variation = ProductVariation::where('id', $variation_id )->first();
        }
        // if($variation_id != null)
        // {
        //     $variation = ProductVariation::where('id', $variation_id )->first();
        //     if($variation->reseller_sales_price != null)
        //     {
        //         $unit_price = $variation->reseller_sales_price;
        //     }
        //     else
        //     {
        //         if($variation->variation_sales_price == null){
        //             $extra_price = $variation->extra_price;
        //             $unit_price = $product->generalInformation->sales_price + $extra_price;
        //         }else{
        //             $unit_price = $variation->variation_sales_price;
        //         }
        //     }
        // }
        $reseller = Contact::whereHas('user', function($q) use($request){
                $q->where('id',Hashids::decode($request->reseller_id)[0]);
            })->first();
        $unit_price = resellerOrderPrice($reseller->id, $product_id, $variation_id);
        // $unit_price -= $unit_price * $product->generalInformation->voucher_discount_percentage / 100;
        // if(isset($request->reseller_id) && $request->reseller_id != '')
        // {
        //     $reseller = Contact::whereHas('user', function($q) use($request){
        //         $q->where('id',Hashids::decode($request->reseller_id)[0]);
        //     })->first();
        //     if($reseller->reseller_package){
        //         $percentage = $reseller->reseller_package->percentage;
        //         $model = $reseller->reseller_package->model;

        //         if($model == 1){    // Discount
        //             $unit_price -= $unit_price * $percentage / 100;
        //         }else{              // Increase
        //             $unit_price += $unit_price * $percentage / 100;
        //         }
        //     }
        // }
        
        $voucher_order = new VoucherOrder;
            $voucher_order->product_id = $product_id;
            $voucher_order->variation_id = ($variation_id != null) ? $variation_id : null;
            $voucher_order->reseller_id = Auth::user()->id;
            $voucher_order->phone = $input['reseller_phone_no'];
            $voucher_order->street_address = $input['address'];
            $voucher_order->city = $input['city'];
            $voucher_order->country_id = Hashids::decode($input['country_id'])[0];
            $voucher_order->quantity = $quantity;
            $voucher_order->remaining_quantity = $quantity;
            $voucher_order->unit_price = $unit_price;
            $voucher_order->saas_discount_percentage = $product->generalInformation->saas_discount_percentage;
            $voucher_order->discount_percentage = $product->generalInformation->voucher_discount_percentage == null ? 0 : $product->generalInformation->voucher_discount_percentage ;
            $voucher_order->used_quantity = 0;
            $voucher_order->total_amount = 0;
            $voucher_order->vat_percentage = $input['vat_percentage'];
            $voucher_order->message = $input['message'] ? $input['message'] : 'No Message Posted';
            $voucher_order->status = 1;
            $voucher_order->is_active = 1;
            $voucher_order->currency_symbol = $currency_acceptance ? Session::get('currency_symbol') == null ? \App\Models\Currency::where('is_active', 1)->where('is_default', 1)->first()->symbol : Session::get('currency_symbol') : "€";
            $voucher_order->currency = $currency_acceptance ? Session::get('currency_code') == null ? \App\Models\Currency::where('is_active', 1)->where('is_default', 1)->first()->code : Session::get('currency_code') : "EUR";
            $voucher_order->exchange_rate = $currency_acceptance ? Session::get('exchange_rate') == null ? \App\Models\Currency::where('is_active', 1)->where('is_default', 1)->first()->exchange_rate : Session::get('exchange_rate') : 1;
        $voucher_order->save();


        foreach($product->customer_taxes as $customer_tax)
        {
            $voucher_order_tax = new VoucherOrderTax;
            $voucher_order_tax->order_id = $voucher_order->id;
            $voucher_order_tax->tax_id = $customer_tax->tax_id;
            $voucher_order_tax->save();
        }

        $voucher_order->total_amount = $voucher_order->total_payable;
        $voucher_order->save();
        for($i = 0; $i < $quantity ; $i++)
        {
            $voucher_code = uniqid(mt_rand());
            $voucher = new Voucher;
            $voucher->status = 1;
            $voucher->order_id = $voucher_order->id;
            $voucher->code = $product ? $product->prefix.$voucher_code : $voucher_code;
            $voucher->save();
        }

        if($product->product_type == 0) {
            $pending_voucher_license_count = Voucher::doesnthave('license')->count();
            $licenses_count = License::where('is_used',0)->where('status',1)->where('is_used',0)->count();
            // If the licenses are less than the ordered vouchers generate an email to admin
            if($licenses_count < $pending_voucher_license_count ){
                $body = "One of the reseller tried to purchase a voucher but the licenses were out of stock. Kindly purchase upload more vouchers to avoid further issues.";
                $body .= "<br> <strong>Avaiable Licenses :</strong> ".$licenses_count;
                $body .= "<br> <strong>Voucher Pending for Licenses :</strong> ".$pending_voucher_license_count;
                $body .= "<br> Kindly Purchase new licenses to avoid inconvinience. ";
                $this->requestAdmintoUploadMoreVouchers($product->id, Hashids::encode($variation_id), $body);
            }
        }
        //Send / Transformation of Vourcher Order Email
        $name = Auth::user()->name;
        $email = Auth::user()->email;
        $voucher_order_id = str_pad($voucher_order->id, 3, '0', STR_PAD_LEFT);
        $voucher_order_id_new = str_replace(' ','',$voucher_order->reseller->name).'-'.str_pad($voucher_order->id,3,'0',STR_PAD_LEFT).'-'.date('dmY',strtotime($voucher_order->created_at));
        $product_name = $product->product_name;
        if($variation){
            $product_name .= ' '.$variation->variation_name;
        }
        $order_approved_email = EmailTemplate::where('type','order_vouchers_created')->first();
        $lang = app()->getLocale();
        $order_approved_email = transformEmailTemplateModel($order_approved_email,$lang);
        $content = $order_approved_email['content'];
        $subject = $order_approved_email['subject'];
        $search = array("{{name}}","{{order_id}}","{{product}}","{{app_name}}");
        $replace = array($name,$voucher_order_id_new,$product_name,env('APP_NAME'));
        $content = str_replace($search,$replace,$content);
        $details['excel_url'] = '#.';
        // if(env('APP_ENV') != 'local'){
            $details['excel_url'] = $this->generateVoucherSheetExcel($voucher_order->id);
        // }
        
        dispatch(new \App\Jobs\SendVoucherOrderEmailJob($email,$subject,$content,$details));

        //Send / Transformation for Approved Voucehr Order
        $radeem_page = ResellerRedeemedPage::where('reseller_id',$voucher_order->reseller->id)->first();
        $title = str_replace(" ", "-", @$radeem_page->title);
        $redeem_link = '';
        if($product->project_id == null){
            $redeem_link = str_replace(' ','',@$radeem_page->url);
            if(strpos($redeem_link, "http") !== 0 ){
                $redeem_link = 'http://'.$redeem_link;
            }
        }
        else
        {
            // dd($product->project->prefix);
            if("TRF" == $product->project->prefix){
                $redeem_link = env('transfer_immunity_url');
            }
            if("QRC" == $product->project->prefix){
                $redeem_link = env('qr_code_url');
            }
            if("AKQ" == $product->project->prefix){
                $redeem_link = env('aikq_url');
            }
            if("INB" == $product->project->prefix){
                $redeem_link = env('inbox_de_url');
            }
            if("OVM" == $product->project->prefix){
                $redeem_link = env('overmail_url');
            }
            if("MAI" == $product->project->prefix){
                $redeem_link = env('maili_de_url');
            }
            if("MOV" == $product->project->prefix){
                $redeem_link = env('move_immunity_url');
            }
            if("NED" == $product->project->prefix){
                $redeem_link = env('ned_link_url');
            }
            if("EMK" == $product->project->prefix){
                $redeem_link = env('email_marketing_url');
            }
        }
        $link = route('frontside.reseller.dashboard');
        $email_template = EmailTemplate::where('type','order_vouchers_approved')->first();
        $lang = app()->getLocale();
        $email_template = transformEmailTemplateModel($email_template,$lang);
        $content = $email_template['content'];
        $subject = $email_template['subject'];
        $search = array("{{name}}","{{order_id}}","{{product}}","{{link}}","{{app_name}}","{{redeem_link}}");
        $replace = array($name,$voucher_order_id_new,$product_name,$link,env('APP_NAME'),$redeem_link);
        $content = str_replace($search,$replace,$content);

        dispatch(new \App\Jobs\SendVoucherAcceptEmailJob($email,$subject,$content,$details));
        
        return $voucher_order; 
    }
    public function exportVouchers($voucher_order_id)
    {
        try {
            $voucher_order_id = Hashids::decode($voucher_order_id)[0];
        } catch (\Throwable $th) {

        }
        $voucher_order = VoucherOrder::where('id',$voucher_order_id)->first();
        $vouchers = Voucher::where('order_id', $voucher_order_id)->get()->toArray();
        $main_platform = $voucher_order->product->project == null ? 'TIMmunity'  : $voucher_order->product->project->name;
        $voucher_array[] = array();
        $voucher_array[] = [
            'Main Platform',
            $main_platform,
        ];
        $voucher_array[] = [
            'Reseller',
            $voucher_order->reseller->name

        ];
        $voucher_array[] = ['#', 'Voucher Code'];

        foreach ($vouchers as $index => $voucher) {
            $voucher_array[] = [
                '#' => $index+1,
                'Voucher Code' => $voucher['code']
            ];
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        for ($i = 0; $i < count($voucher_array); $i++) {
            //set value for indi cell
            $row = $voucher_array[$i];
            //writing cell index start at 1 not 0
            $j = 1;
            foreach ($row as $x => $x_value) {
                $sheet->setCellValueByColumnAndRow($j, $i + 1, $x_value);
                $j = $j + 1;
            }
        }

        ob_clean();
        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="vouchers.xlsx"');
        $writer->save("php://output");
    }
    public function voucherPayment($voucher_payment_id)
    {
        $voucher_payment_id = Hashids::decode($voucher_payment_id)[0];
        $voucher_payment = VoucherPayment::where('id', $voucher_payment_id)->first();
        // dd($voucher_payment->total_payable*$voucher_payment->exchange_rate);
        if($voucher_payment->is_paid == 0 && $voucher_payment->is_partial_paid == 0){
            $payment = $this->generateVoucherPaymentDetails($voucher_payment);
            // dd($payment);
            if($payment['success']){
                $voucher_payment->transaction_id = $payment['payment']->id;
                $voucher_payment->save();

                return redirect($payment['payment']->getCheckoutUrl());
            }else{
                return redirect()->back()->with(session()->flash('alert-warning',__('Payment failed. Tray again.')));
            }
        }
        return redirect()->route('frontside.home.index')->with(session()->flash('alert-warning',__('Payment has been paid.')));
    }
    public function voucherPaymentSuccess($voucher_payment_id)
    {
        $voucher_payment_id = Hashids::decode($voucher_payment_id)[0];
        $voucher_payment = VoucherPayment::where('id', $voucher_payment_id)->first();
        if($voucher_payment)
        {
            $payment = $this->getMolliePaymentDetail($voucher_payment->transaction_id);
            if($voucher_payment->is_paid == 1 || !$payment['payment']->isPaid()){
                return redirect()->route('frontside.home.index')->with(session()->flash('alert-warning', __('Something went wrong. Try again later')));
            }
            $voucher_payment->amount_paid = currency_format($voucher_payment->total_payable* $voucher_payment->exchange_rate,'',1);
            $voucher_payment->is_paid = 1;
            $voucher_payment->save();

            $reference = new VoucherPaymentReference;
            $reference->voucher_payment_id = $voucher_payment_id ;
            $reference->amount = currency_format($voucher_payment->total_payable* $voucher_payment->exchange_rate,'',1);
            $reference->method = "Online Payment";
            $reference->transaction_id = $voucher_payment->transaction_id;
            $reference->save();

            
            $paid_amount = currency_format($voucher_payment->total_payable*$voucher_payment->exchange_rate,'','',1);
            foreach($voucher_payment->details as $detail){
                if($paid_amount == 0){
                    break;
                }
                if($detail->pending_payment > 0){

                    if($detail->pending_payment <= $paid_amount ){
                        $t_p = $detail->pending_payment;
                        
                        $detail->pending_payment = 0;
                        $detail->save();
                        
                        $detail->voucher_order->pending_payment = $detail->voucher_order->pending_payment - $t_p;
                        $detail->voucher_order->save();

                        $paid_amount = $paid_amount - $t_p;
                    }elseif($detail->pending_payment > $paid_amount){
                        
                        $detail->pending_payment = $detail->pending_payment - $paid_amount;
                        $detail->save();
                        
                        $detail->voucher_order->pending_payment = $detail->voucher_order->pending_payment - $paid_amount;
                        $detail->voucher_order->save();

                        $paid_amount = 0;
                    }
                }
            }
            $name = '';
            $email = '';
            if($voucher_payment->details[0]->voucher_order->reseller){
                $name = $voucher_payment->details[0]->voucher_order->reseller->name;
                $email = $voucher_payment->details[0]->voucher_order->reseller->email;

            }else{
                $name = $voucher_payment->details[0]->voucher_order->distributor->name;
                $email = $voucher_payment->details[0]->voucher_order->distributor->email;

            }
            $invoice_id = 'TIM/'.\Carbon\Carbon::parse($voucher_payment->created_at)->format('Y').'/'.str_pad($voucher_payment->id, 3, '0', STR_PAD_LEFT);
            // $order_id = str_pad($voucher_payment->details[0]->voucher_order->id, 3, '0', STR_PAD_LEFT);
            $email_template = EmailTemplate::where('type','vouchers_payment_success')->first();
            $lang = app()->getLocale();
            $email_template = transformEmailTemplateModel($email_template,$lang);
            $content = $email_template['content'];
            $subject = $email_template['subject'];
            $search = array("{{name}}","{{invoice_id}}","{{app_name}}","{{transaction_id}}");
            $replace = array($name,$invoice_id,env('APP_NAME'),$voucher_payment->transaction_id);
            $content = str_replace($search,$replace,$content);
            $attachment = $voucher_payment->invoice_pdf;
            dispatch(new \App\Jobs\SendVoucherPaymentEmailJob($email,$subject,$content,$attachment));
        }
        $data = [];
        $data['voucher_payment'] = $voucher_payment;  
        
        return Auth::user()->email_verified_at == null ? view('frontside.reseller.thankyou',$data)->with(session()->flash('alert-warning', __('Your email is unverified! Kindly verify your email.'))) : view('frontside.reseller.thankyou',$data);

    }

    public function invoicesReseller(Request $request){
        if ($request->ajax()) {
            $data = VoucherPayment::whereHas('details.voucher_order',function($query) {
                $query->where('reseller_id', Auth::user()->id);
            })->get();
            
            $datatable = Datatables::of($data);
            $datatable->addColumn('action', function ($row) {
                $html = '';
                if($row->is_paid == 0)
                {
                    $html .= '<a title="Make Payment" class="" href="'.route("frontside.reseller.voucher.payment", Hashids::encode($row->id)).'">';
                        $html .= '<button class="btn btn-secondary btn-sm">';
                            $html .= '<i class="fa fa-link" aria-hidden="true"></i>';
                        $html .= '</button>';
                    $html .= '</a>';
                }
                $html .= '<a  target="_blank" title="View Invoice" href="'.$row->invoice_pdf_asset .'" class="">';
                    $html .= '<button class="btn btn-info btn-sm">';
                        $html .= '<i class="fa fa-file-text-o" aria-hidden="true"></i>';
                    $html .= '</button>';
                $html .= '</a>';
                return $html;
            });
            $datatable->addColumn('statuss', function ($row) {
                $html = __('Un-paid');
                if($row->is_paid == 1){
                    $html = $row->is_partial_paid == 1 ? __('Partially Paid') : __('Paid') ;
                }
                $html = $row->refunded_at == null ? $html : __('Refunded At').' '.Carbon::parse($row->refunded_at)->format('d-M-Y');
                return $html;
            });
            $datatable->addColumn('quantity', function ($row) {
                $count = 0;
                foreach($row->details as $detail){
                    $voucher_ids_array = explode(',', $detail->voucher_ids);
                    $count += count($voucher_ids_array);
                }
                return $count;
            });
            $datatable->addColumn('amount', function ($row) {
                $html = currency_format($row->total_payable * $row->exchange_rate,$row->currency_symbol,$row->currency);
                return $html;
            });
            $datatable->addColumn('taxes', function ($row) {
                $html = '';
                $count = count($row->details[0]->voucher_order->voucher_taxes);
                foreach($row->details[0]->voucher_order->voucher_taxes as $ind => $voucher_tax){
                    $html .= $voucher_tax->tax->amount;
                    $html .= $voucher_tax->tax->type==1 ? ' %' : '';
                    if($ind < $count-1){
                        $html.=', ';
                    }
                }
                // if($count > 0){
                //     $html .= ', '.$row->details[0]->voucher_order->vat_percentage.'% VAT';

                // }else{
                //     $html .= $row->details[0]->voucher_order->vat_percentage.'% VAT';

                // }
                return $html;
            });

            $datatable = $datatable->rawColumns(['vouchers','action']);
            // dd($datatable->make(true));
            return $datatable->make(true);

        }

        return view('frontside.reseller.reseller-invoices');
    }


}
