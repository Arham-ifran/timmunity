<?php

namespace App\Http\Controllers\Manufacturers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Products;
use Yajra\DataTables\DataTables;
use App\Models\EmailTemplate;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Models\Manufacturer;
use App\Models\VoucherOrder;
use App\Models\Quotation;
use App\Models\Contact;
use App\Models\Admin;
use App\Models\SalesTeam;
use App\Models\ContactCountry;
use Hashids;
use Session;
use DB;
use Auth;
use Alert;


class SalesController extends Controller
{



    public function salesAnalysis(Request $request){

        $data = [];
        $manufacture_id = Auth::guard('manufacture')->user()->id;

        if($request->ajax())
        {

            $data_query = Quotation::with(
                'customer',
                'order_lines',
                'order_lines.product',
                'order_lines.variation',
                'order_lines.quotation_taxes',
                'optional_products',
                'optional_products',
                'optional_products.product',
                'optional_products.variation',
                'other_info',
                'other_info.sales_person',
                'other_info.sales_team'
            )->whereHas('order_lines.product.manufacturer',function($query) use($manufacture_id){
                $query->where('manufacturer_id', $manufacture_id);
            })->orderBy('created_at','desc');

            $start_date     = $request->start_date;
            $end_date       = $request->end_date;

            if(isset($request->start_date) && $request->start_date != '' ){
                $data_query->whereBetween('created_at', [Carbon::parse($request->start_date), Carbon::parse($request->end_date)->addDay()]);

            }
            if(isset($request->product_id) && $request->product_id != null && $request->product_id != ''){
                $data_query->whereHas('order_lines.product', function($query) use($request){
                    $query->where('product_id', $request->product_id);
                    if(isset($request->variation_id) && $request->variation_id != null && $request->variation_id != ''){
                        $query->where('variation_id', $request->variation_id);
                    }
                });
            }
            if(isset($request->country_id) && $request->country_id != '' && $request->country_id != null){
                $data_query->whereHas('customer', function ($query) use($request){
                    $query->where('country_id',$request->country_id);
                });
            }
            if(isset($request->customer_id) && $request->customer_id != ''){
                $data_query->where('customer_id', $request->customer_id);
            }
            if(isset($request->sales_person_id) && $request->sales_person_id != ''){
                $data_query->whereHas('other_info',function($q) use ($request){
                    $q->where('salesperson_id', $request->sales_person_id);
                });
            }
            if(isset($request->sales_team_id) && $request->sales_team_id != ''){
                $data_query->whereHas('other_info',function($q) use ($request){
                    $q->where('sales_team_id', $request->sales_team_id);
                });
            }
            if(isset($request->currency) && $request->currency != ''){
                $data_query->where('currency', $request->currency);
            }
            $data       = $data_query->get();

            $datatable = Datatables::of($data);

            $datatable->addColumn('order_no', function ($row) {
                return  'S'.str_pad($row->id, 5, '0', STR_PAD_LEFT);
            });
            $datatable->addColumn('products', function ($row) use($manufacture_id) {
                $html = '';
                foreach($row->order_lines as $order_line)
                {
                    if($order_line->product->manufacturer_id == $manufacture_id)
                    {
                        $html .= '( '.$order_line->qty.' ) '.$order_line->product->product_name.' '.@$order_line->variation->variation_name.'<br>';
                    }
                }
                return $html;
            });
            $datatable->addColumn('created_at', function ($row) {
                return date('d-M-Y', strtotime($row->created_at));
            });

            $datatable->addColumn('total', function ($row) {

                return  $row->total.' '.$row->currency_symbol;
            });
            $datatable->addColumn('status', function ($row) {

                switch($row->status){
                    case 0:
                        return '<span class="tagged quote">'.__('Quotation').'</span>';
                        break;
                    case 1:
                        return '<span class="tagged success">'.__('Sales Order').'</span>';
                        break;
                    case 2:
                        return '<span class="tagged warning">'.__('Locked').'</span>';
                        break;
                    case 3:
                        return '<span class="tagged quote">'.__('Quotation Sent').'</span>';
                        break;
                    case 4:
                        return '<span class="tagged danger">'.__('Cancelled').'</span>';
                        break;
                    default;
                }
            });

            $datatable = $datatable->rawColumns(['status','products']);
            return $datatable->make(true);
        }

        $productList = Products::with( 'generalInformation', 'customer_taxes', 'variations', 'variations.variation_details' )->where('manufacturer_id',$manufacture_id )->get();
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
                foreach( $prod->variations as $prod_variation )
                {
                    $variation_price = isset($prod->generalInformation->sales_price) ? $prod->generalInformation->sales_price : 0;

                    $store['product_id'] = $prod->id;
                    $store['variation_id'] = $prod_variation->id;
                    $store['name'] = $prod->product_name.' '.$prod_variation->variation_name;
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
        $data['customers'] = Contact::where('status',1)->whereIn('type',[2,3,4])->get();
        $data['salespersons'] = Admin::where('is_active',1)->get();
        $data['salesteams'] = SalesTeam::all();
        $data['currencies'] = Quotation::groupBy('currency')->pluck('currency')->toArray();
        if( empty($data['currencies']) || !isset($data['currencies']['€']) )
        {
            $data['currencies']['€'] = 'EUR';
        }
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
        $data['countries'] = ContactCountry::all();
        return view('manufacturers.sales.reporting')->with($data);
    }


    public function voucherOrdersOnManufacturerProduct(Request $request){

        $data = [];
        $manufacture_id = Auth::guard('manufacture')->user()->id;

        //
        if($request->ajax())
        {

            $data_query = VoucherOrder::whereHas('product.manufacturer',function($query) use($manufacture_id){
                $query->where('manufacturer_id', $manufacture_id);
            })->with(['product','reseller'])->orderBy('id','desc');
            $start_date     = $request->start_date;
            $end_date       = $request->end_date;
            if(isset($request->start_date) && $request->start_date != '' ){
                $data_query->whereBetween('created_at', [Carbon::parse($request->start_date), Carbon::parse($request->end_date)->addDay()]);

            }

            if(isset($request->start_date_update) && $request->start_date_update != '' ){
                $data_query->whereBetween('updated_at', [Carbon::parse($request->start_date_update), Carbon::parse($request->end_date_update)->addDay()]);

            }

            if(isset($request->product_id) && $request->product_id != null && $request->product_id != ''){
                $data_query->whereHas('product', function($query) use($request){
                    $query->where('product_id', $request->product_id);
                    if(isset($request->variation_id) && $request->variation_id != null && $request->variation_id != ''){
                        $query->where('variation_id', $request->variation_id);
                    }
                });
            }

            if(isset($request->manufacturer_id) && $request->manufacturer_id != null && $request->manufacturer_id != ''){
                $data_query->whereHas('product.manufacturer', function($query) use($request){
                    $query->where('manufacturer_id', $request->manufacturer_id);
                });
            }
            if(isset($request->reseller_id) && $request->reseller_id != null && $request->reseller_id != ''){

                $data_query->where('reseller_id', $request->reseller_id);
            }
            $data       = $data_query->get();

            $datatable = Datatables::of($data);

            $datatable->addColumn('order_no', function ($row) {
                // return  'S'.str_pad($row->id, 5, '0', STR_PAD_LEFT);
                return str_replace(' ','',$row->reseller->name).'-'.str_pad($row->id,3,'0',STR_PAD_LEFT).'-'.date('dmY',strtotime($row->created_at));
            });
            $datatable->addColumn('reseller', function ($row) {

                return  isset($row->reseller->name)?$row->reseller->name:'';
            });
            $datatable->addColumn('product_name', function ($row) {

                return $row->product->product_name;
            });

            $datatable->addColumn('manufacturer', function ($row) {

                return  isset($row->product->manufacturer->manufacturer_name)?$row->product->manufacturer->manufacturer_name:'';
            });
            $datatable->addColumn('date', function ($row) {

                return date('d-M-Y', strtotime($row->created_at));
            });

            $datatable->addColumn('quantity', function ($row) {

                return $row->quantity;
            });
            $datatable->addColumn('used', function ($row) {

                return $row->used_quantity;
            });

            $datatable->addColumn('remaining', function ($row) {

                return $row->remaining_quantity;
            });
            $datatable->addColumn('discount', function ($row) {

                return $row->discount_percentage;
            });
            $datatable->addColumn('unit_price', function ($row) {

                return $row->unit_price.' '.$row->currency_symbol;
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
            $datatable->addColumn('total', function ($row) {

                return $row->total_amount.' '.$row->currency_symbol ;
            });

            $datatable = $datatable->rawColumns(['status']);
            return $datatable->make(true);
        }

        $productList = Products::with( 'generalInformation', 'customer_taxes', 'variations', 'variations.variation_details' )->where('manufacturer_id',$manufacture_id )->get();
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
                foreach( $prod->variations as $prod_variation )
                {
                    $variation_price = isset($prod->generalInformation->sales_price) ? $prod->generalInformation->sales_price : 0;

                    $store['product_id'] = $prod->id;
                    $store['variation_id'] = $prod_variation->id;
                    $store['name'] = $prod->product_name.' '.$prod_variation->variation_name;
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

        $data['manufacturers'] = Manufacturer::all();
        $data['resellers'] = Contact::with('contact_countries','user')->whereHas('user')->where('type', 3)->get();
        return view('manufacturers.sales.voucher-orders')->with($data);
    }

    public function graphManufacturerData(Request $request){


    }
}
