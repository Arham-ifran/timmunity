<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VoucherOrder;
use App\Models\VoucherPayment;
use App\Models\Voucher;
use App\Models\Products;
use App\Models\ProductVariation;
use App\Models\License;
use App\Models\EmailTemplate;
use App\Models\Contact;
use App\Models\ResellerRedeemedPage;
use App\Models\VoucherOrderTax;
use App\Models\ContactCountry;
use App\Models\VoucherPaymentReference;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;
use App\Http\Traits\PaymentTrait;
use Hashids;
use Form;
use File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use App\Http\Traits\DistributorCommunicatioTrait;
use Session;
use Alert;
use Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VoucherController extends Controller
{
    use PaymentTrait;    
    use DistributorCommunicatioTrait;

    public function dashboard(Request $request)
    {
        if(!auth()->user()->can('Voucher Dashboard'))
        access_denied();

        $data = [];
        if($request->ajax())
        {
            // Voucher Orders Counts
            $data['total_vouchers_orders'] = VoucherOrder::where('currency', $request->currency)->get()->count();
            $data['pending_vouchers_orders'] = VoucherOrder::where('status', 0)->where('currency', $request->currency)->get()->count();
            $data['accepted_vouchers_orders'] = VoucherOrder::where('status', 1)->where('currency', $request->currency)->get()->count();
            $data['rejected_vouchers_orders'] = VoucherOrder::where('status', 2)->where('currency', $request->currency)->get()->count();
            // Voucher  Counts
            $data['total_vouchers'] = Voucher::whereHas('voucherOrder', function($query) use($request){
                $query->where('currency', $request->currency);
            })->count();
            $data['pending_vouchers'] = Voucher::where('status', 0)->whereHas('voucherOrder', function($query) use($request){
                    $query->where('currency', $request->currency);
                })->count();
            $data['accepted_vouchers'] = Voucher::where('status', 1)->whereHas('voucherOrder', function($query) use($request){
                    $query->where('currency', $request->currency);
                })->count();
            $data['cancelled_vouchers'] = Voucher::where('status', 2)->whereHas('voucherOrder', function($query) use($request){
                    $query->where('currency', $request->currency);
                })->count();
            // Vouvher Payments
            // $voucher_payments = VoucherPayment::whereHas('details.voucher_order', function($query) use($request){
            //         $query->where('currency', $request->currency);
            //     })->get();
            $data['total_payments'] =  VoucherPayment::join('voucher_payment_order_details','voucher_payment_order_details.voucher_payment_id','voucher_payments.id')
                                    ->join('voucher_orders', 'voucher_payment_order_details.voucher_order_id', 'voucher_orders.id')
                                    ->where('voucher_orders.currency', $request->currency)
                                    ->select('voucher_payments.total_amount')->sum('voucher_payments.total_amount');
            // dd($voucher_payments);
            // $data['total_payments'] = 0;
            // foreach($voucher_payments as $voucher_payment)
            // {
            //     dd($voucher_payment);
            //     $data['total_payments'] += $voucher_payment->total_amount;
            // }
            // $voucher_payments = VoucherPayment::where('is_paid',0)->whereHas('details.voucher_order', function($query) use($request){
            //         $query->where('currency', $request->currency);
            //     })->get();
            $data['pending_payments'] = VoucherPayment::join('voucher_payment_order_details','voucher_payment_order_details.voucher_payment_id','voucher_payments.id')
            ->join('voucher_orders', 'voucher_payment_order_details.voucher_order_id', 'voucher_orders.id')
            ->where('voucher_payments.is_paid',0)->where('voucher_orders.currency', $request->currency)
            ->select('voucher_payments.total_amount')->sum('voucher_payments.total_amount');
            // foreach($voucher_payments as $voucher_payment)
            // {
            //     // $data['pending_payments'] += $voucher_payment->total_payable  * $voucher_payment->exchange_rate;
            //     $data['pending_payments'] += $voucher_payment->total_amount;
            // }
            $data['recent_voucher_orders'] = VoucherOrder::orderBy('id', 'desc')->take(10)->get();

            $data['voucher_order_data'] = array(
                (object)array('label'=>'Total Orders','value'=>$data['total_vouchers_orders']),
                (object)array('label'=>'Pending Orders','value'=>$data['pending_vouchers_orders']),
                (object)array('label'=>'Accepted Orders','value'=>$data['accepted_vouchers_orders']),
                (object)array('label'=>'Rejected Orders','value'=>$data['rejected_vouchers_orders'])
            );
            $data['voucher_data'] = array(
                (object)array('label'=>'Total Vouchers','value'=>$data['total_vouchers']),
                (object)array('label'=>'Pending Vouchers','value'=>$data['pending_vouchers']),
                (object)array('label'=>'Accepted Vouchers','value'=>$data['accepted_vouchers']),
                (object)array('label'=>'Rejected Vouchers','value'=>$data['cancelled_vouchers'])
            );
            $data['voucher_payment_data'] = array(
                (object)array('label'=>'Total','value'=>currency_format($data['total_payments'],'','',1)),
                (object)array('label'=>'Pending','value'=>currency_format($data['pending_payments'],'','',1)),
                (object)array('label'=>'Paid','value'=>currency_format($data['total_payments'] - $data['pending_payments'],'','',1)),
            );
            return $data;
        }
       
        $data['voucher_order_data'] = array(
            (object)array('label'=>'Total Orders','value'=>0),
            (object)array('label'=>'Pending Orders','value'=>0),
            (object)array('label'=>'Accepted Orders','value'=>0),
            (object)array('label'=>'Rejected Orders','value'=>0)
        );
        $data['voucher_data'] = array(
            (object)array('label'=>'Total Vouchers','value'=>0),
            (object)array('label'=>'Pending Vouchers','value'=>0),
            (object)array('label'=>'Accepted Vouchers','value'=>0),
            (object)array('label'=>'Rejected Vouchers','value'=>0)
        );
        $data['voucher_payment_data'] = array(
            (object)array('label'=>'Total','value'=>0),
            (object)array('label'=>'Pending','value'=>0),
        );
        // dd( $data['voucher_payment_data'] );
        $data['currencies'] = VoucherOrder::groupBy('currency')->pluck('currency')->toArray();
        $temp_currency = array();
        $temp_currency['€'] = 'EUR';
        foreach($data['currencies'] as $ind => $d_c)
        {
            if($d_c != 'EUR')
            {
                $temp_currency[$ind] = $d_c;
            }
        }
        $data['currencies'] = $temp_currency;
        return view('admin.voucher.dashboard', $data);
    }

    public function orders(Request $request)
    {

        if(!auth()->user()->can('Voucher Order Listing'))
        access_denied();
        Session::put('voucher_code', $request->voucher_code);

        if ($request->ajax()) {

            $data_query = VoucherOrder::orderBy('created_at','desc');

            if(isset($request->internal_only) && $request->internal_only == 1){
                $data_query->where('reseller_id','!=',0);
            }
            if(isset($request->country_id) && $request->country_id != '' && $request->country_id != null){
                $data_query->whereHas('contact_country', function ($query) use($request){
                    $query->where('country_id',$request->country_id);
                });
            }
            if(isset($request->action_status) && $request->action_status != '' && $request->action_status != null){
                $data_query->where('is_active',$request->action_status);
            }
            if(isset($request->vendor_type) && $request->vendor_type != '' && $request->vendor_type != null){
                switch ($request->vendor_type) {
                    case '1':
                        $data_query->whereHas('reseller');
                        break;
                    case '2':
                        $data_query->whereHas('distributor');
                        # code...
                        break;
                }
            }

            if(isset($request->status)){
                $data_query->where('status', $request->status);
            }
            if(isset($request->currency)){
                $data_query->where('currency', $request->currency);
            }
            if(isset($request->name_email)){
                $data_query->whereHas('reseller', function($query) use($request){
                    $query->where('name','LIKE','%'.$request->name_email.'%');
                    $query->orWhere('email','LIKE','%'.$request->name_email.'%');
                });
            }
            if(isset($request->voucher_code)){

                $data_query->whereHas('vouchers', function($query) use($request){
                    $query->where('code','LIKE','%'.$request->voucher_code.'%');
                });
            }
            if(isset($request->customer_name_email)){

                $data_query->whereHas('vouchers.customer', function($query) use($request){
                    $query->where('name','LIKE','%'.$request->customer_name_email.'%');
                    $query->orWhere('email','LIKE','%'.$request->customer_name_email.'%');
                });
            }
            if(isset($request->start_date) && $request->start_date != '' ){
                $data_query->whereBetween('created_at', [Carbon::parse($request->start_date), Carbon::parse($request->end_date)->addDay()]);
            }else{
                $data_query->whereDate('created_at', '>', Carbon::now()->subDays(30));
            }
            if(isset($request->product_id) && $request->product_id != null && $request->product_id != ''){
                $data_query->where(function($query) use($request){
                    $query->where('product_id', $request->product_id);
                    if(isset($request->variation_id) && $request->variation_id != null && $request->variation_id != ''){
                        $query->where('variation_id', $request->variation_id);
                    }
                });
            }
            $data = $data_query;

            $datatable = Datatables::of($data);
            $datatable->addColumn('input', function ($row) {
                if($row->status != 0) {
                    return '<input type="checkbox" class="selectedids" value="'.Hashids::encode($row->id).'">';
                }
            });
            $datatable->addColumn('ids', function ($row) {
                return '';
            });
            $datatable->addColumn('order_id', function ($row) {
                if($row->reseller){
                    return str_replace(' ','',@$row->reseller->name).'-'.str_pad(@$row->id,3,'0',STR_PAD_LEFT).'-'.date('dmY',strtotime(@$row->created_at));
                }
                return str_replace(' ','',@$row->distributor->name).'-'.str_pad(@$row->id,3,'0',STR_PAD_LEFT).'-'.date('dmY',strtotime(@$row->created_at));

            });
            $datatable->addColumn('reseller', function ($row) {
                if($row->reseller){
                    return @$row->reseller->name.'<br>'.@$row->reseller->email;
                }
                return @$row->distributor->name.'<br>'.@$row->distributor->email.'<br> Distributor';

            });
            $datatable->addColumn('action', function ($row)  use($request) {
                $html = '' ;
                if (auth()->user()->hasAnyPermission(['Vouchers Listing','Vouchers Payment','Download Vouchers']))
                {
                    if(auth()->user()->can('Vouchers Listing')) {
                        if(isset($request->voucher_code)){
                            $html = '<a title='.__('Vouchers').' class="" href="'.route("admin.voucher.order.vouchers", Hashids::encode($row->id)).'?code='.$request->voucher_code.'">';
                        }else{
                            $html = '<a title='.__('Vouchers').' class="" href="'.route("admin.voucher.order.vouchers", Hashids::encode($row->id)).'">';
                        }
                            $html .= '<button class="btn btn-secondary btn-sm">';
                                $html .= '<i class="fa fa-gift" aria-hidden="true"></i>';
                            $html .= '</button>';
                        $html .= '</a>&nbsp;';

                    }
                    // if(auth()->user()->can('Vouchers Payment')) {
                    //     $html .= '<a title='.__('Payments').' href="'.route("admin.voucher.payment", Hashids::encode($row->id)) .'" class="">';
                    //         $html .= '<button class="btn btn-info btn-sm">';
                    //             $html .= '<i class="fa fa-credit-card" aria-hidden="true"></i>';
                    //         $html .= '</button>';
                    //     $html .= '</a>&nbsp;';
                    // }
                    if(auth()->user()->can('Download Vouchers')) {
                        $html .= '<a title='.__('Download').' href="'.route('admin.voucher.order.vouchers.export', Hashids::encode($row->id)).'" class="">';
                            $html .= '<button class="btn btn-success btn-sm">';
                                $html .= '<i class="fa fa-download" aria-hidden="true"></i>';
                            $html .= '</button>';
                        $html .= '</a>&nbsp;';
                    }
                }
                return $html;
            });
            $datatable->addColumn('active_status', function ($row) {
                // return $row->is_active;
                if($row->is_active == 0 || $row->is_active == '0'){
                    return '<span class="badge  bg-red">'.__('Inactive').'</span>';
                }
                if($row->status == 1 ||$row->is_active == '1'){
                    return $html ='<span class="badge  bg-green">'.__('Active').'</span>';
                }

            });
            $datatable->addColumn('statuss', function ($row) {
                // return $row->status;
                if($row->status == 0)
                    return '<span class="badge  bg-yellow">'.__('Pending').'</span>';
                if($row->status == 1)
                    return $html ='<span class="badge  bg-green">'.__('Approved').'</span>';
                if($row->status == 2)
                    return $html ='<span class="badge badge-danger bg-red">'.__('Rejected').'</span>';

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
                return currency_format($row->unit_price*$row->exchange_rate,$row->currency_symbol,$row->currency);
            });
            $datatable->addColumn('taxes', function ($row) {
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
                // return '';
                return currency_format($row->total_payable,$row->currency_symbol,$row->currency);
            });
            $datatable->addColumn('paid_amount', function ($row) {
                return '';
                return currency_format(($row->total_payable-$row->remaining_total),$row->currency_symbol,$row->currency);
            });
            $datatable->editColumn('pending_payment', function ($row) {
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
            $datatable->addColumn('status_action', function ($row)  use($request){
                $actions = '' ;
                if (auth()->user()->hasAnyPermission(['Voucher Approved','Voucher Reject']))
                {
                    if($row->status == 0)
                    {
                        if(auth()->user()->can('Voucher Approved')) {
                            $actions .= '<div style="display:inline-flex">';
                            $actions .= '&nbsp;' . Form::open([
                                'method' => 'GET',
                                'url' => [route('admin.voucher.order.change-status',[ Hashids::encode( $row->id ), 1, $row->product->product_name ] )],
                                'style' => 'display:inline'
                            ]);

                            // $actions .= Form::button('<i class="fa fa-check fa-fw" title="Approve Voucher Order"></i>', ['type' => 'submit','class' => 'status-action-btn btn btn-default btn-icon']);
                            $actions .= Form::button(__('Approve'), ['type' => 'submit','class' => 'status-action-btn btn btn-success btn-icon']);
                            $actions .= Form::submit('Approve', ['class' => 'hidden deleteSubmit']);

                            $actions .= Form::close();
                            $actions .= '</div>';
                        }
                        if(auth()->user()->can('Voucher Reject')) {
                            $actions .= '<div style="display:inline-flex">';

                            $actions .= '&nbsp;' . Form::open([
                                'method' => 'GET',
                                'url' => [route('admin.voucher.order.change-status',[ Hashids::encode( $row->id ), 2, $row->product->product_name ] )],
                                'style' => 'display:inline'
                            ]);

                            // $actions .= Form::button('<i class="fa fa-times fa-fw" title="Reject Voucher Order"></i>', ['type' => 'submit','class' => 'status-action-btn btn btn-default btn-icon']);
                            $actions .= Form::button(__('Reject'), ['type' => 'submit','class' => 'status-action-btn btn btn-danger btn-icon']);
                            $actions .= Form::submit('Reject', ['class' => 'hidden deleteSubmit']);

                            $actions .= Form::close();
                            $actions .= '</div>';
                        }
                    }
                    if($row->status == 1)
                    {
                        if(auth()->user()->can('Voucher Reject')) {
                            $actions .= '<div style="display:inline-flex">';

                            $actions .= '&nbsp;' . Form::open([
                                'method' => 'GET',
                                'url' => [route('admin.voucher.order.change-status',[ Hashids::encode( $row->id ), 2, $row->product->product_name ] )],
                                'style' => 'display:inline'
                            ]);

                            // $actions .= Form::button('<i class="fa fa-times fa-fw" title="Reject Voucher Order"></i>', ['type' => 'submit','class' => 'status-action-btn btn btn-default btn-icon']);
                            $actions .= Form::button(__('Reject'), ['type' => 'submit','class' => 'status-action-btn btn btn-danger btn-icon']);
                            $actions .= Form::submit('Reject', ['class' => 'hidden deleteSubmit']);

                            $actions .= Form::close();
                            $actions .= '</div>';
                        }
                    }
                    if($row->status == 2)
                    {
                        if(auth()->user()->can('Voucher Approved')) {
                            $actions .= '<div style="display:inline-flex">';

                            $actions .= '&nbsp;' . Form::open([
                                'method' => 'GET',
                                'url' => [route('admin.voucher.order.change-status',[ Hashids::encode( $row->id ), 1, $row->product->product_name ] )],
                                'style' => 'display:inline'
                            ]);

                            // $actions .= Form::button('<i class="fa fa-check fa-fw" title="Approve Voucher Order"></i>', ['type' => 'submit','class' => 'status-action-btn btn btn-default btn-icon']);
                            $actions .= Form::button('Approve', ['type' => 'submit','class' => 'status-action-btn btn btn-success btn-icon']);
                            $actions .= Form::submit('Approve', ['class' => 'hidden deleteSubmit']);

                            $actions .= Form::close();
                            $actions .= '</div>';
                        }
                    }
                }
                return $actions;
            });
            $datatable->addColumn('active_status_action', function ($row)  use($request){
                $actions = '' ;
                if(auth()->user()->can('Voucher Activated / Inactive')) {
                    if($row->is_active == 0)
                    {
                        $actions .= '<div style="display:inline-flex">';

                        $actions .= '&nbsp;' . Form::open([
                            'method' => 'GET',
                            'url' => [route('admin.voucher.order.change-status',[ Hashids::encode( $row->id ), 4, $row->product->product_name ] )],
                            'style' => 'display:inline'
                        ]);

                        $actions .= Form::button(__('Activate'), ['type' => 'submit','class' => 'status-action-btn btn btn-success btn-icon']);
                        $actions .= Form::submit('Activate Voucher Order', ['class' => 'hidden deleteSubmit']);

                        $actions .= Form::close();
                        $actions .= '</div>';
                        $actions .= '<div style="display:inline-flex">';

                    }
                    if($row->is_active == 1)
                    {
                        $actions .= '<div style="display:inline-flex">';

                        $actions .= '&nbsp;' . Form::open([
                            'method' => 'GET',
                            'url' => [route('admin.voucher.order.change-status',[ Hashids::encode( $row->id ), 5, $row->product->product_name ] )],
                            'style' => 'display:inline'
                        ]);

                        // $actions .= Form::button('<i class="fa fa-times fa-fw" title="Reject Voucher Order"></i>', ['type' => 'submit','class' => 'status-action-btn btn btn-default btn-icon']);
                        $actions .= Form::button(__('Inactive'), ['type' => 'submit','class' => 'status-action-btn btn btn-danger btn-icon']);
                        $actions .= Form::submit('Deactivate Voucher Order', ['class' => 'hidden deleteSubmit']);

                        $actions .= Form::close();
                        $actions .= '</div>';
                    }
                }
                return $actions;
            });
            $datatable = $datatable->rawColumns(['product_name','reseller','active_status','active_status_action','statuss','action','status_action']);
            return $datatable->make(true);
        }
        $productList = Products::with( 'generalInformation', 'customer_taxes', 'variations', 'variations.variation_details' )->get();
        $data['products'] = [];
        foreach ($productList as $prod) {
            // If the product is simple product
            if( count($prod->variations) == 0 ){
                $store['product_id'] = $prod->id;
                $store['variation_id'] = '';
                $store['name'] = $prod->product_name;
                $store['price'] = isset($prod->generalInformation->sales_price) ? $prod->generalInformation->sales_price : 0;
                $taxes = isset($prod->customer_taxes[0]) ? $prod->customer_taxes : [];
                $store['taxes'] = [];
                foreach( $taxes as $t )
                {
                    $store['taxes'][] = $t->tax_id;
                }

                $store['taxes'] = json_encode( $store['taxes'] );

                $data['products'][] = $store;
            }
            // If the product is variable product
            else
            {
                // dd($prod->variations);
                foreach( $prod->variations as $prod_variation )
                {
                    $variation_price = isset($prod->generalInformation->sales_price) ? $prod->generalInformation->sales_price : 0;

                    $store['product_id'] = $prod->id;
                    $store['variation_id'] = $prod_variation->id;
                    $store['name'] = $prod->product_name.' '.@$prod_variation->variation_name;
                    $store['price'] = $variation_price;
                    $taxes = isset($prod->customer_taxes[0]) ? $prod->customer_taxes : [];
                    $store['taxes'] = [];
                    foreach( $taxes as $t )
                    {
                        $store['taxes'][] = $t->id;
                    }

                    $store['taxes'] = json_encode( $store['taxes'] );

                    $data['products'][] = $store;
                }
            }
        }
        // dd($data['products']);
        $data['products_voucher_order'] = Products::withCount('variations')->where('is_active', 1)->get();
        // $data['vat_percentage']   = Auth::user()->contact->contact_countries->vat_in_percentage;
        $data['countries'] = ContactCountry::all();
        $data['resellers'] = Contact::with('contact_countries','user')->whereHas('user')->where('type', 3)->get();
        $data['reseller_email'] = isset($request->reseller_email) ? $request->reseller_email : '';
        $data['default_vat'] = \App\Models\SiteSettings::first()->defualt_vat;
        $data['currencies'] = VoucherOrder::groupBy('currency')->pluck('currency')->toArray();

        return view('admin.voucher.orders', $data);

    }
    public function orderVoucherPost(Request $request)
    {
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
        return redirect()->route('admin.voucher.orders')->with(session()->flash('alert-success',__('Order placed successfully!')));
    }
    public function orderVoucherSingle($request, $product_id, $variation_id = null, $quantity)
    {
        $input = $request->all();

        $product = Products::with('generalInformation','customer_taxes','customer_taxes.tax')->where('id', $product_id)->first();
        $extra_price = 0;
        $variation = null;
        $unit_price = $product->generalInformation->sales_price;
        if($variation_id != null)
        {
            $variation = ProductVariation::where('id', $variation_id)->first();

            if($variation->reseller_sales_price != null)
            {
                $unit_price = $variation->reseller_sales_price;
            }
            else{
                if($variation->variation_sales_price == null)
                {
                    $extra_price = $variation->extra_price;
                    $unit_price = $product->generalInformation->sales_price + $extra_price;
                }
                else
                {
                    $unit_price = $variation->variation_sales_price;
                }
            }
        }
        if(isset($request->reseller_id) && $request->reseller_id != '')
        {
            $reseller = Contact::whereHas('user', function($q) use($request){
                $q->where('id',Hashids::decode($request->reseller_id)[0]);
            })->first();
            if($reseller->reseller_package){
                $percentage = $reseller->reseller_package->percentage;
                $model = $reseller->reseller_package->model;

                if($model == 1){    // Discount
                    $unit_price -= $unit_price * $percentage / 100;
                }else{              // Increase
                    $unit_price += $unit_price * $percentage / 100;
                }
            }
        }
        // dd($unit_price);
        $voucher_order = new VoucherOrder;
            $voucher_order->product_id = $product_id;
            $voucher_order->variation_id = $variation_id ? $variation_id : null;
            $voucher_order->reseller_id = Hashids::decode($input['reseller_id'])[0];
            $voucher_order->street_address = $input['address'];
            $voucher_order->phone = $input['reseller_phone'];
            $voucher_order->city = $input['city'];
            $voucher_order->country_id = Hashids::decode($input['country_id'])[0];
            $voucher_order->quantity = $quantity;
            $voucher_order->remaining_quantity = $quantity;
            $voucher_order->saas_discount_percentage = $product->generalInformation->saas_discount_percentage;
            $voucher_order->unit_price = $unit_price;
            $voucher_order->discount_percentage = $product->generalInformation->voucher_discount_percentage == null ? 0 : $product->generalInformation->voucher_discount_percentage ;
            $voucher_order->used_quantity = 0;
            $voucher_order->total_amount = 0;
            $voucher_order->vat_percentage = $input['vat_percentage'];
            $voucher_order->message = $input['message'] ? $input['message'] : 'No Message Posted';
            $voucher_order->status = 1;
            $voucher_order->is_active = 1;
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
        $pending_voucher_license_count = Voucher::doesnthave('license')->count();
        $licenses_count = License::where('is_used',0)->where('status',1)->where('is_used',0)->count();

        $name = $input['reseller_name'];
        $email = $input['reseller_email'];
        // $voucher_order_id = str_pad($voucher_order->id, 3, '0', STR_PAD_LEFT);
        $voucher_order_id = str_replace(' ','',$name).'-'.str_pad($voucher_order->id,3,'0',STR_PAD_LEFT).'-'.date('dmY',strtotime($voucher_order->created_at));
        $product_name = $product->product_name;
        if($variation){
            $product_name .= ' '.$variation->variation_name;
        }
        $radeem_page = ResellerRedeemedPage::where('reseller_id',$voucher_order->reseller->id)->first();
        $title = str_replace(" ", "-", $radeem_page->title);
        // $link = route('voucher.redeem.page',['title'=>strtolower($title).'-redeem-page','reseller_id'=>Hashids::encode($voucher_order->reseller->id)]);
        $redeem_link = '';
        if($product->project_id == null){
            // $redeem_link = route('voucher.redeem.page',['title'=>strtolower($title),'reseller_id'=>Hashids::encode(Auth::user()->id)]);
            $redeem_link = str_replace(' ','',$radeem_page->url);
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
        $email_template = EmailTemplate::where('type','order_vouchers_created')->first();
        $lang = app()->getLocale();
        $email_template = transformEmailTemplateModel($email_template,$lang);
        $content = $email_template['content'];
        $subject = $email_template['subject'];
        $search = array("{{name}}","{{order_id}}","{{product}}","{{app_name}}","{{redeem_link}}");
        $replace = array($name,$voucher_order_id,$product_name,env('APP_NAME'),$redeem_link);
        $content = str_replace($search,$replace,$content);
        $details['excel_url'] = $this->generateVoucherSheetExcel($voucher_order->id);
        dispatch(new \App\Jobs\SendVoucherOrderEmailJob($email,$subject,$content,$details));


        $name = $voucher_order->reseller->name;
        $email = $voucher_order->reseller->email;
        $order_id = str_pad($voucher_order_id, 3, '0', STR_PAD_LEFT);

        $radeem_page = ResellerRedeemedPage::where('reseller_id',$voucher_order->reseller->id)->first();
        $title = str_replace(" ", "-", $radeem_page->title);
        // $redeem_link = route('voucher.redeem.page',['title'=>strtolower($title),'reseller_id'=>Hashids::encode($voucher_order->reseller->id)]);
        $redeem_link = '';
        if($product->project_id == null){
            // $redeem_link = route('voucher.redeem.page',['title'=>strtolower($title),'reseller_id'=>Hashids::encode($voucher_order->reseller->id)]);
            $redeem_link = str_replace(' ','',$radeem_page->url);
            if(strpos($redeem_link, "http") !== 0 ){
                $redeem_link = 'https://'.$redeem_link;
            }
        }
        else
        {
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
        $replace = array($name,$voucher_order_id,$product_name,$link,env('APP_NAME'),$redeem_link);
        $content = str_replace($search,$replace,$content);

        $details['excel_url'] = $this->generateVoucherSheetExcel($voucher_order->id);
        dispatch(new \App\Jobs\SendVoucherAcceptEmailJob($email,$subject,$content,$details));
        Alert::success(__('Success'), __('Order placed successfully!'))->persistent('Close')->autoclose(5000);
        return $voucher_order;
    }
    public function generateVoucherSheetExcel($voucher_order_id)
    {
        try {
            $voucher_order_id = Hashids::decode($voucher_order_id)[0];
        } catch (\Throwable $th) {
            $voucher_order_id = $voucher_order_id;
        }
        $voucher_array[] = array();
        $voucher_order = VoucherOrder::where('id',$voucher_order_id)->first();
        $vouchers = Voucher::where('order_id', $voucher_order_id)->get()->toArray();
        // if(!isset($voucher_order->product->project)){
        //     dd('a');
        // }else{
        //     echo(1);
        // }
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
        $upload_path = public_path() . '/storage/vouchers';
        if (!File::exists(public_path() . '/storage/vouchers')) {

            File::makeDirectory($upload_path, 0777, true);
        }
        $old_file = public_path().'/storage/vouchers/vouchers-'.Hashids::encode($voucher_order_id).'.xlsx';
        if (file_exists($old_file)) {
            unlink($old_file);
        }
        ob_clean();
        // $writer = new Xlsx($spreadsheet);
        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $writer->save(public_path().'/storage/vouchers/vouchers-'.Hashids::encode($voucher_order_id).'.xlsx');
        return public_path('storage/vouchers/vouchers-'.Hashids::encode($voucher_order_id).'.xlsx');
    }
    public function orderVouchers($id, Request $request)
    {

        $voucher_order_id = Hashids::decode($id)[0];

        if ($request->ajax()) {
            $data_query = Voucher::with('customer')->where('order_id', $voucher_order_id);
            if(isset($request->status) && $request->status !=  null && $request->status != ''){
                $data_query->where('status', $request->status);
            }
            if(isset($request->code) && $request->code !=  null && $request->code != ''){
                $data_query->where('code', $request->code);
            }
            $data = $data_query->orderBy('id','desc');

            $data_ids = $data_query->pluck('id');
            $datatable = Datatables::of($data);
            if (auth()->user()->hasAnyPermission(['Bulk Disable Vouchers','Bulk Activate Vouchers','Bulk Redeemed Vouchers']))
            {
                $datatable->addColumn('input', function ($row) use($data_ids){
                    if($row->status != 0) {
                        foreach($data_ids as $ind => $data_id){
                            $data_ids[$ind] = Hashids::encode($data_id);
                        }
                        return '<input type="checkbox" data-all-ids=\''.json_encode($data_ids).'\' class="selectedids" value="'.Hashids::encode($row->id).'">';
                    }
                });
            }
            $datatable->editColumn('code', function ($row) {
                return $row->code;
            });
            $datatable->addColumn('statuss', function ($row) {
                switch ($row->status) {
                    case 0:
                        return '<span class="badge  bg-green">'.__('Redeemed').'</span><br><span style="color:red">'.@$row->license->license_key.'<span>';
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
            $datatable->addColumn('action', function ($row) {
                $html = '';
                if (auth()->user()->hasAnyPermission(['Voucher Disable','Voucher Redeemed','Voucher Approved']))
                {
                    if($row->status == 1) {
                        $html .= auth()->user()->can('Voucher Disable') ? '<a href="'.route('admin.change.voucher.order.vouchers.status', [Hashids::encode($row->id),2]).'" class="btn btn-danger">'.__('Disable').'</a>' : '';
                        $html .= auth()->user()->can('Voucher Redeemed') ? '&nbsp; <a href="'.route('admin.change.voucher.order.vouchers.status', [Hashids::encode($row->id),0]).'" class="btn btn-default">'.__('Redeemed').'</a>' : '';
                    }
                    if($row->status == 2) {
                        $html .= auth()->user()->can('Voucher Approved') ? '<a href="'.route('admin.change.voucher.order.vouchers.status', [Hashids::encode($row->id),1]).'" class="btn btn-primary">'.__('Approve').'</a>' : '';
                        $html .= auth()->user()->can('Voucher Redeemed') ? '&nbsp; <a href="'.route('admin.change.voucher.order.vouchers.status', [Hashids::encode($row->id),0]).'" class="btn btn-default">'.__('Redeemed').'</a>' : '';
                    }
                    if($row->status != 2 && $row->status != 1 )
                    {
                        $html .= auth()->user()->can('Voucher Approved') ? '<button disabled class="btn btn-primary">'.__('Approve').'</button>' : '';
                        $html .= auth()->user()->can('Voucher Disable') ? '&nbsp; <button disabled class="btn btn-danger">'.__('Disable').'</button>' : '';
                    }
                }
                return $html;
            });
            $datatable->addColumn('customer', function ($row) {
                return $row->customer != null ? @$row->customer->email : $row->email;
            });
            $datatable->addColumn('redeemed_time', function ($row) {
                return $row->redeemed_at ? \Carbon\Carbon::parse($row->redeemed_at)->format('d/M/Y') : '';
            });

            $datatable = $datatable->rawColumns(['input','statuss','action']);
            return $datatable->make(true);
        }
        $data = [];
        $data['ajax_url'] = route("admin.voucher.order.vouchers", Hashids::encode($voucher_order_id));
        $data['code'] = isset($request->code) ? $request->code : '';
        return view('admin.voucher.vouchers',$data);
    }

    /**
     * Change Voucher Order Status
     * $status 0,1,2 change the status
     * $status 4,5 change is_active 0,1 accordingly
     *
     */
    public function orderChangeStatus($id, $status, $product_name)
    {
        $voucher_order_id = Hashids::decode($id)[0];
        $voucher_order = VoucherOrder::where('id', $voucher_order_id)->first();
        if($status == 0 || $status == 1 || $status == 2){
            if($voucher_order->distributor_id != null){
                $this->changeVoucherOrderStatus($voucher_order->distributor->shop_url.'/api',$voucher_order_id,$status);
            }

            VoucherOrder::where('id', $voucher_order_id)->update(['status' => $status]);
            if($status == 1){
                VoucherOrder::where('id', $voucher_order_id)->update(['is_active' => 1]);
            }

            Voucher::where('order_id', $voucher_order_id)->where('status','!=', 0)->update(['status' => $status]);
        }
        elseif($status == 4 || $status == 5){
            if($voucher_order->distributor_id != null){
                $this->changeVoucherOrderStatus($voucher_order->distributor->shop_url.'/api',$voucher_order_id,$status);
            }

            VoucherOrder::where('id', $voucher_order_id)->update(['is_active' => $status == 4 ? 1 : 0]);
            Voucher::where('order_id', $voucher_order_id)->where('status','!=', 0)->update(['status' => $status == 4 ? 1 : 2]);
        }
        $voucher_order = VoucherOrder::where('id', $voucher_order_id)->first();
        if($status == 1){
            $radeem_page = ResellerRedeemedPage::where('reseller_id',$voucher_order->reseller->id)->first();
            $title = str_replace(" ", "-", $radeem_page->title);

            $name = $voucher_order->reseller->name;
            $email = $voucher_order->reseller->email;
            $order_id = str_pad($voucher_order_id, 3, '0', STR_PAD_LEFT);
            $radeem_page = ResellerRedeemedPage::where('reseller_id',$voucher_order->reseller->id)->first();
            $title = str_replace(" ", "-", $radeem_page->title);
            $link = '';
            if($voucher_order->product->project_id == null){
                $link = str_replace(' ','',$radeem_page->url);
                if(strpos($link, "http") !== 0 ){
                    $link = 'https://'.$link;
                }
            }
            else
            {
                if("TRF" == $voucher_order->product->project->prefix){
                    $link = env('transfer_immunity_url');
                }
                if("QRC" == $voucher_order->product->project->prefix){
                    $link = env('qr_code_url');
                }
                if("AKQ" == $voucher_order->product->project->prefix){
                    $link = env('aikq_url');
                }
                if("INB" == $voucher_order->product->project->prefix){
                    $link = env('inbox_de_url');
                }
                if("OVM" == $voucher_order->product->project->prefix){
                    $link = env('overmail_url');
                }
                if("MAI" == $voucher_order->product->project->prefix){
                    $link = env('maili_de_url');
                }
                if("MOV" == $voucher_order->product->project->prefix){
                    $link = env('move_immunity_url');
                }
                if("NED" == $voucher_order->product->project->prefix){
                    $link = env('ned_link_url');
                }
                if("EMK" == $voucher_order->product->project->prefix){
                    $link = env('email_marketing_url');
                }
            }
            $email_template = EmailTemplate::where('type','order_vouchers_approved')->first();
            $lang = app()->getLocale();
            $email_template = transformEmailTemplateModel($email_template,$lang);
            $content = $email_template['content'];
            $subject = $email_template['subject'];
            $search = array("{{name}}","{{order_id}}","{{product}}","{{app_name}}","{{redeem_link}}");
            $replace = array($name,$voucher_order_id,$product_name,env('APP_NAME'),$link);
            $content = str_replace($search,$replace,$content);

            $details['excel_url'] = $this->generateVoucherSheetExcel($voucher_order_id);
            dispatch(new \App\Jobs\SendVoucherAcceptEmailJob($email,$subject,$content,$details));
        }
        Alert::success(__('Success'), __('Status updated successfully!'))->persistent('Close')->autoclose(5000);
        return redirect()->route('admin.voucher.orders')->with(session()->flash('alert-success',__('Status updated successfully!')));
    }
    public function changeOrderVoucherStatus($id, $status)
    {
        $voucher_id = Hashids::decode($id)[0];
        $voucher = Voucher::with('voucherOrder')->where('id', $voucher_id)->first();
        if($voucher->voucherOrder->status == 1 && $voucher->voucherOrder->is_active == 1){
            if($voucher->voucherOrder->distributor_id != null){
                $this->changeVoucherStatus($voucher->voucherOrder->distributor->shop_url.'/api',$voucher_id,$status);
            }

            Voucher::where('id', $voucher_id)->update(['status' => $status]);
            if($status == 0){
                $voucher->redeemed_at = \Carbon\Carbon::now();
                $voucher->email = Auth::user()->email;
                $voucher->save();
                $voucher->voucherOrder->used_quantity += 1;
                $voucher->voucherOrder->remaining_quantity -= 1;
                $voucher->voucherOrder->save();
            }
            $order_id = Voucher::where('id', $voucher_id)->first()->order_id;
            Alert::success(__('Success'), __('Status updated successfully!'))->persistent('Close')->autoclose(5000);
            return redirect()->route('admin.voucher.order.vouchers', Hashids::encode($order_id))->with(session()->flash('alert-success',__('Status updated successfully!')));
        }
        Alert::error(__('Error'), __('Status cannot be updated. The Order against this voucher is in pending, inactive or rejected state'))->persistent('Close')->autoclose(5000);
        return redirect()->route('admin.voucher.order.vouchers', Hashids::encode($voucher->order_id))->with(session()->flash('alert-error',__('Status cannot be updated. The Order against this voucher is in pending, inactive or rejected state')));
    }
    public function changeBulkOrderVoucherStatus(Request $request)
    {
        $ids = explode(';',$request->ids);
        $status = $request->statuss;
        foreach($ids as $key => $id){
            $ids[$key] = Hashids::decode($id)[0];
        }
        $order_id =  $order_id = Voucher::whereIn('id', $ids)->first()->order_id;
        $vouchers = Voucher::with('voucherOrder')->whereIn('id', $ids)->get();
        if($vouchers[0]->voucherOrder->status != 0){
            Voucher::whereIn('id', $ids)->update(['status' => $status]);
            $order_id = Voucher::whereIn('id', $ids)->first()->order_id;
            $count_ids = count($ids);
            if($status == 0){

                $vouchers[0]->voucherOrder->used_quantity += $count_ids;
                $vouchers[0]->voucherOrder->remaining_quantity -= $count_ids;
                $vouchers[0]->voucherOrder->save();
            }

            foreach($vouchers as $voucher){
                if($status == 0){
                    $voucher->redeemed_at = \Carbon\Carbon::now();
                    $voucher->email = Auth::user()->email;
                    $voucher->save();
                }
                if($voucher->voucherOrder->distributor_id != null){
                    $this->changeVoucherStatus($voucher->voucherOrder->distributor->shop_url.'/api',$voucher->id,$status);
                }
            }
            Alert::success(__('Success'), __('Status updated successfully!'))->persistent('Close')->autoclose(5000);
            return redirect()->route('admin.voucher.order.vouchers', Hashids::encode($order_id))->with(session()->flash('alert-success',__('Status updated successfully!')));
        }
        Alert::error(__('Error'), __('Status can be updated. The Order against this voucher is in pending or rejected state'))->persistent('Close')->autoclose(5000);
        return redirect()->route('admin.voucher.order.vouchers', Hashids::encode($order_id))->with(session()->flash('alert-error',__('Status can be updated. The Order against this voucher is in pending or rejected state')));
    }
    public function exportVouchers($voucher_order_id)
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
        $voucher_array = [];
        if( $voucher_order->reseller){
            $voucher_array[] = [
                'Reseller',
                $voucher_order->reseller->name
            ];
        }elseif( $voucher_order->distributor){
            $voucher_array[] = [
                'Distributor',
                $voucher_order->distributor->name
            ];
            
        }else{
            $voucher_array[] = [
                '',
                ''
            ];
        }
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

        $writer = new Csv($spreadsheet);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="vouchers.csv"');
        $writer->save("php://output");
    }
    public function voucherPayment($voucher_order_id, Request $request)
    {
        $voucher_order_id = Hashids::decode($voucher_order_id)[0];
        if ($request->ajax()) {
            $data = VoucherPayment::with('voucher_order','voucher','reseller')->where('voucher_order_id', $voucher_order_id)->get();
            // dd($data, $voucher_order_id);
            $datatable = Datatables::of($data);
            $datatable->addColumn('action', function ($row) {
                $html = '';
                if (auth()->user()->hasAnyPermission(['Make Voucher Payment','View Payment Voucher Invoice']))
                {
                    if($row->is_paid == 0)
                    {
                        if(auth()->user()->can('Make Voucher Payment')) {
                            $html .= '<a title='.__('Make-Payment').' class="" href="'.route("admin.make.voucher.payment", Hashids::encode($row->id)).'">';
                                $html .= '<button class="btn btn-secondary btn-sm">';
                                    $html .= '<i class="fa fa-link" aria-hidden="true"></i>';
                                $html .= '</button>';
                            $html .= '</a>';
                        }
                    }
                    $html .= '&nbsp;<a title='.__('View-Payment').' class="" href="'.route("admin.voucher.orders.payment.detail", Hashids::encode($row->id)).'">';
                        $html .= '<button class="btn btn-warning btn-sm">';
                            $html .= '<i class="fa fa-eye" aria-hidden="true"></i>';
                        $html .= '</button>';
                    $html .= '</a>';
                    if(auth()->user()->can('View Payment Voucher Invoice')) {
                        $html .= '&nbsp;<a title= '.__('View-Invoice').' href="'.$row->invoice_pdf_asset .'" class="">';
                            $html .= '<button class="btn btn-info btn-sm">';
                                $html .= '<i class="fa fa-file-text-o" aria-hidden="true"></i>';
                            $html .= '</button>';
                        $html .= '</a>';
                    }
                }
                return $html;
            });
            $datatable->addColumn('product', function ($row) {
                // $html = $row->voucher_order->product->product_name . ' ' . @$row->voucher_order->variation->variation_name;
                $html = $row->voucher_order->product->product_name.' ' ;
                $html .= $row->voucher_order->product->project == null ? @$row->voucher_order->variation->variation_name : '' ;
                return $html;
            });
            $datatable->addColumn('statuss', function ($row) {
                $html = __('Un-paid');
                if($row->is_paid == 1){
                    $html = $row->is_partial_paid == 1 ? __('Partially Paid') : __('Paid') ;
                }
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
                $html = currency_format($row->total_payable* $row->exchange_rate,$row->voucher_order->currency_symbol,$row->voucher_order->currency);
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
                if($count == 0){
                    $html .= $row->voucher_order->vat_percentage.'% VAT';
                }else{
                    $html .= ', '.$row->voucher_order->vat_percentage.'% VAT';
                }
                return $html;
            });

            $datatable = $datatable->rawColumns(['vouchers','action']);
            return $datatable->make(true);
        }
        $data['ajax_url'] = route("admin.voucher.payment",Hashids::encode($voucher_order_id));

        return view('admin.voucher.payments', $data);
    }
    public function invoices(Request $request)
    {
        
        if ($request->ajax()) {
            // $data = VoucherPayment::whereHas('details.voucher_order.reseller')->orderBy('id','desc');
            $data = VoucherPayment::join('voucher_payment_order_details','voucher_payment_order_details.voucher_payment_id','voucher_payments.id')
                        ->join('voucher_orders','voucher_orders.id','voucher_payment_order_details.voucher_order_id')
                        ->join('users','users.id','voucher_orders.reseller_id')
                        ->where('voucher_orders.reseller_id','!=',0)
                        ->select(
                            'voucher_payments.id',
                            'voucher_payments.is_paid',
                            'voucher_payments.is_partial_paid',
                            'voucher_payments.refunded_at',
                            'users.name as reseller_name',
                            'users.email as reseller_email',
                            'voucher_payments.total_amount',
                            'voucher_payments.currency',
                            'voucher_payments.currency_symbol'
                        )->groupBy('voucher_payments.id')->orderBy('voucher_payments.id','desc');
            if(isset($request->order_number) && $request->order_number != '' ){
                $order_number = explode('/',$request->order_number);
                $order_number = array_reverse($order_number)[0];
                $data->where('voucher_payments.id', $order_number);
            }
            if(isset($request->start_date) && $request->start_date != '' ){
                $data->whereBetween('voucher_payments.created_at', [Carbon::parse($request->start_date), Carbon::parse($request->end_date)->addDay()]);
            }

            if(isset($request->start_date_update) && $request->start_date_update != '' ){
                $data->whereBetween('updated_at', [Carbon::parse($request->start_date_update), Carbon::parse($request->end_date_update)->addDay()]);
            }

            if(isset($request->payment_status) && $request->payment_status != '' ){

                if($request->payment_status == 1){
                    $data->where('is_paid',$request->payment_status)->whereNull('refunded_at');
                    $data->where('is_partial_paid','!=',1);
                }elseif($request->payment_status == 0){
                    $data->where('is_paid',$request->payment_status)->whereNull('refunded_at');
                }elseif($request->payment_status == 3){
                  
                    $data->where('is_partial_paid',1);
                }
            }

            if(isset($request->product_id) && $request->product_id != null && $request->product_id != ''){
                $data->whereHas('details.voucher_order.product', function($query) use($request){
                    $query->where('product_id', $request->product_id);
                    if(isset($request->variation_id) && $request->variation_id != null && $request->variation_id != ''){
                        $query->where('variation_id', $request->variation_id);
                    }
                });
            }

            if(isset($request->manufacturer_id) && $request->manufacturer_id != null && $request->manufacturer_id != ''){
                $data->whereHas('details.voucher_order.product.manufacturer', function($query) use($request){
                    $query->where('manufacturer_id', $request->manufacturer_id);
                });
            }

            if(isset($request->name_email) && $request->name_email != null && $request->name_email != ''){
                $data->whereHas('details.voucher_order.reseller', function($query) use($request){
                    $query->where('name','LIKE','%'.$request->name_email.'%');
                    $query->orWhere('email','LIKE','%'.$request->name_email.'%');
                });
            }
            if(isset($request->invoice_payment) && $request->invoice_payment != null && $request->invoice_payment != ''){
                $data->whereHas('voucher_payment_references', function($query) use($request){
                    $query->where('method', $request->invoice_payment);
                });
            }
            $datatable = Datatables::of($data);
            $datatable->addColumn('invoicenumber', function ($row) {
                $text = 'TIM/'.\Carbon\Carbon::parse($row->created_at)->format('Y').'/'.str_pad($row->id, 3, '0', STR_PAD_LEFT) ;
                return auth()->user()->can('View Payment Voucher Invoice') ? '<a target="_blank" href="' .route("admin.voucher.orders.payment.detail", Hashids::encode($row->id)). '">'.$text.'</a>' : $text;
            });
            $datatable->addColumn('action', function ($row) {
                $html = '';
                if (auth()->user()->hasAnyPermission(['Make Voucher Payment','View Payment Voucher Invoice']))
                {
                    if($row->is_paid == 0)
                    {
                        if(auth()->user()->can('Make Voucher Payment')) {
                            $html .= '<a title='.__('Make-Payment').' class="" href="'.route("admin.make.voucher.payment", Hashids::encode($row->id)).'">';
                                $html .= '<button class="btn btn-secondary btn-sm">';
                                    $html .= '<i class="fa fa-link" aria-hidden="true"></i>';
                                $html .= '</button>';
                            $html .= '</a>';
                        }
                    }
                    $html .= '&nbsp;<a title='.__('View-Payment').' class="" href="'.route("admin.voucher.orders.payment.detail", Hashids::encode($row->id)).'">';
                        $html .= '<button class="btn btn-warning btn-sm">';
                            $html .= '<i class="fa fa-eye" aria-hidden="true"></i>';
                        $html .= '</button>';
                    $html .= '</a>';
                    if(auth()->user()->can('View Payment Voucher Invoice')) {
                        // $html .= '&nbsp;<a title= '.__('View-Invoice').' href="'.$row->invoice_pdf_asset .'" class="">';
                        $html .= '&nbsp;<a title= '.__('View-Invoice').' href="'.route('admin.voucher.invoice.pdf',Hashids::encode($row->id)) .'" class="">';
                            $html .= '<button class="btn btn-info btn-sm">';
                                $html .= '<i class="fa fa-file-text-o" aria-hidden="true"></i>';
                            $html .= '</button>';
                        $html .= '</a>';
                    }
                }
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
            $datatable->addColumn('reseller', function ($row) {
                // $reseller = $row->details[0]->voucher_order->reseller ;
                return $row->reseller_name;
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
                $voucher_payment = VoucherPayment::where('id',$row->id)->first();
                $html = currency_format($voucher_payment->total_payable* $voucher_payment->exchange_rate,$voucher_payment->currency_symbol,$voucher_payment->currency);
                // $html = currency_format($row->total_amount,$row->currency_symbol,$row->currency);
                return $html;
            });

            $datatable = $datatable->rawColumns(['vouchers','action','invoicenumber']);
            return $datatable->make(true);
        }
        $data['ajax_url'] = route("admin.voucher.invoices");
        return view('admin.voucher.payments', $data);
    }
    public function distributorInvoices(Request $request)
    {
        if ($request->ajax()) {
            // $data = VoucherPayment::whereHas('details.voucher_order.distributor')->orderBy('id','desc');
            $data = VoucherPayment::join('voucher_payment_order_details','voucher_payment_order_details.voucher_payment_id','voucher_payments.id')
                        ->join('voucher_orders','voucher_orders.id','voucher_payment_order_details.voucher_order_id')
                        ->join('distributors','distributors.id','voucher_orders.distributor_id')
                        ->whereNotNull('voucher_orders.distributor_id')
                        ->select(
                            'voucher_payments.id',
                            'voucher_payments.is_paid',
                            'voucher_payments.is_partial_paid',
                            'voucher_payments.refunded_at',
                            'distributors.name as distributor_name',
                            'distributors.email as distributor_email',
                            'voucher_payments.total_amount',
                            'voucher_payments.currency',
                            'voucher_payments.currency_symbol',
                            'voucher_payments.exchange_rate'
                        )->groupBy('voucher_payments.id')->orderBy('voucher_payments.id','desc');
         
            if(isset($request->order_number) && $request->order_number != '' ){
                $order_number = explode('/',$request->order_number);
                $order_number = array_reverse($order_number)[0];
                $data->where('voucher_payments.id', $order_number);
            }
            if(isset($request->payment_status) && $request->payment_status != '' ){

                if($request->payment_status == 1){
                    $data->where('is_paid',$request->payment_status)->whereNull('refunded_at');
                    $data->where('is_partial_paid','!=',1);
                }elseif($request->payment_status == 0){
                    $data->where('is_paid',$request->payment_status)->whereNull('refunded_at');
                }elseif($request->payment_status == 3){
                    $data->where('is_partial_paid',1);
                    
                }
            }
            if(isset($request->name_email) && $request->name_email != null && $request->name_email != ''){
                $data->whereHas('details.voucher_order.reseller', function($query) use($request){
                    $query->where('distributor_name','LIKE','%'.$request->name_email.'%');
                    $query->orWhere('distributor_email','LIKE','%'.$request->name_email.'%');
                });
            }
            if(isset($request->invoice_payment) && $request->invoice_payment != null && $request->invoice_payment != ''){


                $data->whereHas('voucher_payment_references', function($query) use($request){
                    $query->where('method', $request->invoice_payment);
                });
            }
            // dd($data, $voucher_order_id);
            $datatable = Datatables::of($data);
            $datatable->addColumn('invoicenumber', function ($row) {
                $text = 'TIM/'.\Carbon\Carbon::parse($row->created_at)->format('Y').'/'.str_pad($row->id, 3, '0', STR_PAD_LEFT) ;
                return auth()->user()->can('View Payment Voucher Invoice') ? '<a target="_blank" href="' .route("admin.voucher.orders.payment.detail", Hashids::encode($row->id)). '">'.$text.'</a>' : $text;
            });
            $datatable->addColumn('action', function ($row) {
                $html = '';
                if (auth()->user()->hasAnyPermission(['Make Voucher Payment','View Payment Voucher Invoice']))
                {
                    if($row->is_paid == 0)
                    {
                        if(auth()->user()->can('Make Voucher Payment')) {
                            $html .= '<a title='.__('Make-Payment').' class="" href="'.route("admin.make.voucher.payment", Hashids::encode($row->id)).'">';
                                $html .= '<button class="btn btn-secondary btn-sm">';
                                    $html .= '<i class="fa fa-link" aria-hidden="true"></i>';
                                $html .= '</button>';
                            $html .= '</a>';
                        }
                    }
                    $html .= '&nbsp;<a title='.__('View-Payment').' class="" href="'.route("admin.voucher.orders.payment.detail", Hashids::encode($row->id)).'">';
                        $html .= '<button class="btn btn-warning btn-sm">';
                            $html .= '<i class="fa fa-eye" aria-hidden="true"></i>';
                        $html .= '</button>';
                    $html .= '</a>';
                    if(auth()->user()->can('View Payment Voucher Invoice')) {
                        // $html .= '&nbsp;<a title= '.__('View-Invoice').' href="'.$row->invoice_pdf_asset .'" class="">';
                        $html .= '&nbsp;<a title= '.__('View-Invoice').' href="'.route('admin.voucher.invoice.pdf',Hashids::encode($row->id)) .'" class="">';
                            $html .= '<button class="btn btn-info btn-sm">';
                                $html .= '<i class="fa fa-file-text-o" aria-hidden="true"></i>';
                            $html .= '</button>';
                        $html .= '</a>';
                    }
                }
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
            $datatable->addColumn('reseller', function ($row) {
               
                    return $row->distributor_name;

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
                $html = currency_format($row->total_payable* $row->exchange_rate,$row->currency_symbol,$row->currency);
                return $html;
            });

            $datatable = $datatable->rawColumns(['vouchers','action','invoicenumber']);
            return $datatable->make(true);
        }
        $data['ajax_url'] = route("admin.voucher.distributor-invoices");
        return view('admin.voucher.distributor-payments', $data);
    }
    public function makeVoucherPayment($voucher_payment_id)
    {
        $voucher_payment_id = Hashids::decode($voucher_payment_id)[0];
        $voucher_payment = VoucherPayment::where('id', $voucher_payment_id)->first();
        $voucher = Voucher::where('id', $voucher_payment->voucher_id)->first();

        $payment = $this->generateVoucherPaymentDetails($voucher_payment,route('admin.voucher.payment.success',Hashids::encode($voucher_payment_id)));
        if($payment['success']){
            $voucher_payment->transaction_id = $payment['payment']->id;
            $voucher_payment->save();

            return redirect($payment['payment']->getCheckoutUrl());
        }else{
            Alert::error(__('Warning'), $payment['message'])->persistent('Close')->autoclose(5000);
            return redirect()->back()->with(session()->flash('alert-warning',$payment['message']));
        }
    }
    public function voucherPaymentSuccess($voucher_payment_id)
    {
        $voucher_payment_id = Hashids::decode($voucher_payment_id)[0];
        $voucher_payment = VoucherPayment::where('id', $voucher_payment_id)->first();

        if($voucher_payment)
        {
            $payment = $this->getMolliePaymentDetail($voucher_payment->transaction_id);
            if($voucher_payment->is_paid == 1 || !$payment['payment']->isPaid()){
                Alert::warning(__('Warning'), __('Something went wrong. Try again later'))->persistent('Close')->autoclose(5000);
                return redirect()->route('admin.voucher.invoices')->with(session()->flash('alert-warning', __('Something went wrong. Try again later')));
            }
            $voucher_payment->amount_paid = currency_format($voucher_payment->total_payable* $voucher_payment->exchange_rate,'','',1);
            $voucher_payment->is_paid = 1;
            $voucher_payment->save();

            $reference = new VoucherPaymentReference;
            $reference->voucher_payment_id = $voucher_payment_id ;
            $reference->amount = currency_format($voucher_payment->total_payable* $voucher_payment->exchange_rate,'','',1);
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
            $voucher = Voucher::where('id', $voucher_payment->voucher_id)->first();


            $name = '';
            $email ='';
            $order_id = '';
            if($voucher_payment->details[0]->voucher_order->reseller){
                $name = $voucher_payment->details[0]->voucher_order->reseller->name;
                $email = $voucher_payment->details[0]->voucher_order->reseller->email;
                $order_id = str_replace(' ','',$voucher_payment->details[0]->voucher_order->reseller->name).'-'.str_pad($voucher_payment->details[0]->voucher_order->id,3,'0',STR_PAD_LEFT).'-'.date('dmY',strtotime($voucher_payment->details[0]->voucher_order->created_at));
            }else{
                $name = $voucher_payment->details[0]->voucher_order->distributor->name;
                $email = $voucher_payment->details[0]->voucher_order->distributor->email;
                $order_id = str_replace(' ','',$voucher_payment->details[0]->voucher_order->distributor->name).'-'.str_pad($voucher_payment->details[0]->voucher_order->id,3,'0',STR_PAD_LEFT).'-'.date('dmY',strtotime($voucher_payment->details[0]->voucher_order->created_at));

            }
            
            
            $email_template = EmailTemplate::where('type','vouchers_payment_success')->first();
            $lang = app()->getLocale();
            $invoice_id = 'TIM/'.\Carbon\Carbon::parse($voucher_payment->created_at)->format('Y').'/'.str_pad($voucher_payment->id, 3, '0', STR_PAD_LEFT);
            $email_template = transformEmailTemplateModel($email_template,$lang);
            $content = $email_template['content'];
            $subject = $email_template['subject'];
            $search = array("{{name}}","{{invoice_id}}","{{app_name}}","{{transaction_id}}");
            $replace = array($name,$invoice_id,env('APP_NAME'),$voucher_payment->transaction_id);
            $content = str_replace($search,$replace,$content);
            $attachment = $voucher_payment->invoice_pdf;
            dispatch(new \App\Jobs\SendVoucherPaymentEmailJob($email,$subject,$content,$attachment));
            Alert::success(__('Success'), __('Payment successfully confirmed.'))->persistent('Close')->autoclose(5000);
            // return redirect()->route('admin.voucher.payment',Hashids::encode($voucher_payment->voucher_order_id))->with(session()->flash('alert-success',__('Payment successfully confirmed.')));
            return redirect()->route('admin.voucher.invoices')->with(session()->flash('alert-success',__('Payment successfully confirmed.')));
        }
    }

    public function cancelVochersAgainstProductVariation($product_id, $variation_id)
    {
        $product_id = Hashids::decode($product_id)[0];
        if($variation_id != null & $variation_id != 0){
            $variation_id = Hashids::decode($variation_id)[0];
        }
        $voucher_query = Voucher::whereHas('voucherOrder', function($query) use($product_id, $variation_id){
            $query->where('product_id', $product_id);
            if($variation_id != null & $variation_id != 0){
                $query->where('variation_id', $variation_id);
            }
        })->where('redeemed_at', null)->update(['status'=>2]);
        Alert::success(__('Success'), __('Vouchers Cancelled Successfuly!'))->persistent('Close')->autoclose(5000);
        return redirect()->back()->with(session()->flash('alert-success',__('Vouchers Cancelled Successfuly')));
    }

    public function generateVoucherReport(Request $request){


        $data['total_vouchers_orders'] = VoucherOrder::where('currency', $request->currency)->get()->count();
        $data['pending_vouchers_orders'] = VoucherOrder::where('status', 0)->where('currency', $request->currency)->get()->count();
        $data['accepted_vouchers_orders'] = VoucherOrder::where('status', 1)->where('currency', $request->currency)->get()->count();
        $data['rejected_vouchers_orders'] = VoucherOrder::where('status', 2)->where('currency', $request->currency)->get()->count();
        // Voucher  Counts
        $data['total_vouchers'] = Voucher::whereHas('voucherOrder', function($query) use($request){
            $query->where('currency', $request->currency);
        })->count();
        $data['pending_vouchers'] = Voucher::where('status', 0)->whereHas('voucherOrder', function($query) use($request){
                $query->where('currency', $request->currency);
            })->count();
        $data['accepted_vouchers'] = Voucher::where('status', 1)->whereHas('voucherOrder', function($query) use($request){
                $query->where('currency', $request->currency);
            })->count();
        $data['cancelled_vouchers'] = Voucher::where('status', 2)->whereHas('voucherOrder', function($query) use($request){
                $query->where('currency', $request->currency);
            })->count();
        // Vouvher Payments
        $data['total_payments'] =  VoucherPayment::join('voucher_payment_order_details','voucher_payment_order_details.voucher_payment_id','voucher_payments.id')
        ->join('voucher_orders', 'voucher_payment_order_details.voucher_order_id', 'voucher_orders.id')
        ->where('voucher_orders.currency', $request->currency)
        ->select('voucher_payments.total_amount')->sum('voucher_payments.total_amount');
        $data['pending_payments'] = VoucherPayment::join('voucher_payment_order_details','voucher_payment_order_details.voucher_payment_id','voucher_payments.id')
        ->join('voucher_orders', 'voucher_payment_order_details.voucher_order_id', 'voucher_orders.id')
        ->where('voucher_payments.is_paid',0)->where('voucher_orders.currency', $request->currency)
        ->select('voucher_payments.total_amount')->sum('voucher_payments.total_amount');
        $data['recent_voucher_orders'] = VoucherOrder::orderBy('id', 'desc')->take(10)->get();

        $sheet_array[] = array();
        $sheet_array[] = [
            'Vouchers Orders Report',
        ];
        $sheet_array[] = ['Metrics', 'Value'];
        $sheet_array[] = [
            'Metrics' => 'Accepted Orders' ,
            'Value'  => $data['accepted_vouchers_orders']
        ];
        $sheet_array[] = [
            'Metrics' => 'Pending Orders' ,
            'Value' => $data['pending_vouchers_orders']
        ];
        $sheet_array[] = [
            'Metrics' => 'Total Orders' ,
            'Value' => $data['total_vouchers_orders']
        ];
        $sheet_array[] = [
            'Metrics' => 'Rejected Orders' ,
            'Value' => $data['rejected_vouchers_orders']
        ];
        $sheet_array[] = [
            'Metrics' => 'Accepted Vouchers' ,
            'Value' => $data['accepted_vouchers']
        ];
        $sheet_array[] = [
            'Metrics' => 'Pending Vouchers' ,
            'Value' => $data['pending_vouchers']
        ];
        $sheet_array[] = [
            'Metrics' => 'Total Vouchers' ,
            'Value' => $data['total_vouchers']
        ];
        $sheet_array[] = [
            'Metrics' => 'Rejected Vouchers' ,
            'Value' => $data['cancelled_vouchers']
        ];
        $sheet_array[] = [
            'Metrics' => 'Payment Paid' ,
            'Value' => currency_format($data['total_payments'] - $data['pending_payments'],'','',1)
        ];
        $sheet_array[] = [
            'Metrics' => 'Payment Pending' ,
            'Value' => currency_format($data['pending_payments'],'','',1)
        ];
        $sheet_array[] = [
            'Metrics' => 'Total Payment Amount' ,
            'Value' => currency_format($data['total_payments'],'','',1)
        ];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        for ($i = 0; $i < count($sheet_array); $i++) {
            //set value for indi cell
            $row = $sheet_array[$i];
            //writing cell index start at 1 not 0
            $j = 1;
            foreach ($row as $x => $x_value) {
                $sheet->setCellValueByColumnAndRow($j, $i + 1, $x_value);
                $j = $j + 1;
            }
        }
        $old_file = public_path().'/storage/vouchers/Voucher-Orders-Resport.xlsx';
        if (file_exists($old_file)) {
            unlink($old_file);
        }
        ob_clean();
        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="voucher-orders-report.xlsx"');
        $writer->save("php://output");

        // header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        // header('Content-Disposition: attachment; filename="Voucher Orders Report"');
        // // $writer->save(public_path().'/storage/sales/Sales Resport.xlsx');
        // $writer->save('php://output');
        return public_path('storage/vouchers/Voucher-Orders-Resport.xlsx');
    }

    public function viewPayment($payment_id){
        try {
            $payment_id = Hashids::decode($payment_id)[0];
        } catch (\Throwable $th) {
            //throw $th;
        }
        $data['model'] = VoucherPayment::where('id', $payment_id)->first();
        return view('frontside.reseller.pdf.invoice', $data);

        return view('admin.voucher.payment-view', $data);

    }

    public function registerPayment($payment_id, Request $request)
    {
        try {
            $payment_id = Hashids::decode($payment_id)[0];
        } catch (\Throwable $th) {
            //throw $th;
        }
        $input = $request->all();  //[registered_amount,method]

        $voucher_payment = VoucherPayment::where('id', $payment_id)->first();
        if($voucher_payment)
        {
            $voucher_payment->amount_paid = $voucher_payment->amount_paid + $input['registered_amount'];

            // check if the user is registering extra amount
            if($voucher_payment->amount_paid >  currency_format($voucher_payment->total_payable* $voucher_payment->exchange_rate,'','',1))
            {
                Alert::error(__('Warning'), __('You are trying to register amount greater than '). currency_format($voucher_payment->total_payable* $voucher_payment->exchange_rate,'','',1))->persistent('Close')->autoclose(5000);
                return redirect()->back()->with(session()->flash('alert-success',__('You are trying to register amount greater than '). currency_format($voucher_payment->total_payable* $voucher_payment->exchange_rate,'','',1) ));
            }

            $reference = new VoucherPaymentReference;
            $reference->voucher_payment_id = $payment_id ;
            $reference->amount = $input['registered_amount'];
            $reference->method = $input['method'];
            $reference->save();

            if($voucher_payment->amount_paid <  currency_format($voucher_payment->total_payable* $voucher_payment->exchange_rate,'','',1))
            {
                $voucher_payment->is_partial_paid = 1;
                $voucher_payment->is_paid = 1;
            }
            else if(number_format($voucher_payment->amount_paid,2) ==  currency_format($voucher_payment->total_payable* $voucher_payment->exchange_rate,'','',1))
            {
                $voucher_payment->is_partial_paid = 0;
                $voucher_payment->is_paid = 1;
            }
            $voucher_payment->save();
            $paid_amount = $input['registered_amount'];
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

            $email_template = EmailTemplate::where('type','invoice_amount_registered')->first();

            $lang = app()->getLocale();
            $email_template = transformEmailTemplateModel($email_template,$lang);
            $content = $email_template['content'];
            $subject = $email_template['subject'];
            $name = '';
            $email ='';
            if($voucher_payment->details[0]->voucher_order->reseller){
                $name = $voucher_payment->details[0]->voucher_order->reseller->name;
                $email = $voucher_payment->details[0]->voucher_order->reseller->email;
            }else{
                $name = $voucher_payment->details[0]->voucher_order->distributor->name;
                $email = $voucher_payment->details[0]->voucher_order->distributor->email;

            }
            // $order_number = str_replace(' ','',$name.'-'.str_pad($voucher_payment->details[0]->voucher_order->id,3,'0',STR_PAD_LEFT).'-'.date('dmY',strtotime($voucher_payment->voucher_order->created_at));

            $amount_registered = currency_format($input['registered_amount'],$voucher_payment->currency_symbol,$voucher_payment->currency);
            $total_registered_amount = currency_format($voucher_payment->amount_paid,'','',1);
            $total_registered = currency_format($voucher_payment->amount_paid,$voucher_payment->currency_symbol,$voucher_payment->currency);
            $total_invoice_total_amount = currency_format($voucher_payment->total_payable* $voucher_payment->exchange_rate,'','',1);
            $total_invoice_total = currency_format($voucher_payment->total_payable* $voucher_payment->exchange_rate,$voucher_payment->currency_symbol,$voucher_payment->currency);
            $pending_amount = (double)$total_invoice_total_amount - (double)$total_registered_amount;
            // dd($pending_amount);
            $pending_amount = currency_format($pending_amount,$voucher_payment->currency_symbol,$voucher_payment->currency);

            $status = $voucher_payment->is_paid == 1 ? $voucher_payment->is_partial_paid ? 'Partially Paid' : 'Paid' : 'Not Paid';
            $invoice_number = 'TIM/'.\Carbon\Carbon::parse($voucher_payment->created_at)->format('Y').'/'.str_pad($voucher_payment->id, 3, '0', STR_PAD_LEFT) ;
            $search = array(
                "{{name}}","{{order_number}}","{{amount_registered}}","{{total_registered}}","{{total_invoice_total}}","{{pending_amount}}","{{app_name}}","{{status}}"
            );
            $replace = array(
                $name,$invoice_number,$amount_registered ,$total_registered ,$total_invoice_total ,$pending_amount,env('APP_NAME'),$status);

            $content = str_replace($search,$replace,$content);
            dispatch(new \App\Jobs\SendAmountRegisteredEmailJob($email,$subject,$content,$voucher_payment->invoicepdf));
            Alert::success(__('Success'), __('Payment registered successfully!'))->persistent('Close')->autoclose(5000);
            return redirect()->route('admin.voucher.orders.payment.detail', $payment_id)->with(session()->flash('alert-success',__('Payment registered successfully!')));
        }
        Alert::error(__('Error'), __('Invalid Payment!'))->persistent('Close')->autoclose(5000);
        return redirect()->route('admin.voucher.orders.payment.detail', $payment_id)->with(session()->flash('alert-error',__('Invalid Payment!')));
    }
    public function refundPayment($payment_id, Request $request)
    {
        try {
            $payment_id = Hashids::decode($payment_id)[0];
        } catch (\Throwable $th) {
            //throw $th;
        }

        $voucher_payment = VoucherPayment::where('id', $payment_id)->first();
        if($voucher_payment)
        {
            $voucher_payment->refunded_at = Carbon::now();
            $voucher_payment->save();

            Alert::success(__('Success'), __('Payment refunded successfully!'))->persistent('Close')->autoclose(5000);
            return redirect()->route('admin.voucher.orders.payment.detail', $payment_id);
        }
        Alert::error(__('Error'), __('Invalid Payment!'))->persistent('Close')->autoclose(5000);
        return redirect()->route('admin.voucher.orders.payment.detail', $payment_id)->with(session()->flash('alert-error',__('Invalid Payment!')));
    }

    public function invoicePDF($voucher_payment_id){
        try {
            $voucher_payment_id = Hashids::decode($voucher_payment_id)[0];
        } catch (\Throwable $th) {
            //throw $th;
        }
        $data = VoucherPayment::where('id',$voucher_payment_id)->first();
        return redirect($data->invoice_pdf_asset);
    }
}
