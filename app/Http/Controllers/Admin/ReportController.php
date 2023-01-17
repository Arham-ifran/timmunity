<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Quotation;
use App\Models\Invoice;
use App\Models\QuotationOrderLineTax;
use App\Models\Contact;
use \Carbon\Carbon;
use Carbon\CarbonPeriod;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use DB;
use Session;
use App\Models\Products;
use App\Models\ContactCountry;
use Yajra\DataTables\DataTables;
use App\Models\VoucherOrder;
use App\Models\VoucherPayment;
use App\Models\Manufacturer;
use App\Models\Admin;
use App\Models\SalesTeam;
use App\Models\User;
use App\Models\Cart;
use App\Models\License;
use Hashids;

class ReportController extends Controller
{


    public function getManufacturers(Request $request){

        $data = [];

        if($request->ajax())
        {

            $data_query = VoucherOrder::whereHas('product.manufacturer')->with(['product','reseller'])->orderBy('id','desc');
            $start_date     = $request->start_date;
            $end_date       = $request->end_date;
            if(isset($request->start_date) && $request->start_date != '' ){
                $data_query->whereBetween('created_at', [Carbon::parse($request->start_date), Carbon::parse($request->end_date)->addDay()]);

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

            if(isset($request->name_email) && $request->name_email != null && $request->name_email != ''){
                $data_query->whereHas('reseller', function($query) use($request){
                    $query->where('name','LIKE','%'.$request->name_email.'%');
                    $query->orWhere('email','LIKE','%'.$request->name_email.'%');
                });
            }
            $data       = $data_query->get();

            $datatable = Datatables::of($data);
            $datatable->addColumn('product_name', function ($row) {
                return  $row->product_name;
            });
            $datatable->addColumn('order_no', function ($row) {
                if($row->reseller){
                    return str_replace(' ','',@$row->reseller->name).'-'.str_pad(@$row->id,3,'0',STR_PAD_LEFT).'-'.date('dmY',strtotime(@$row->created_at));
                }elseif($row->distributor){
                    return str_replace(' ','',@$row->distributor->name).'-'.str_pad(@$row->id,3,'0',STR_PAD_LEFT).'-'.date('dmY',strtotime(@$row->created_at));
                }
            });
            $datatable->addColumn('created_at', function ($row) {
                return date('d-M-Y', strtotime($row->created_at));
            });
            $datatable->addColumn('product_name', function ($row) {

                return $row->product->product_name;
            });
            $datatable->addColumn('total', function ($row) {

                return currency_format($row->total_amount,$row->currency_symbol, $row->currency);
            });
            $datatable->addColumn('manufacturer', function ($row) {

                return  isset($row->product->manufacturer->manufacturer_name)?$row->product->manufacturer->manufacturer_name:'';
            });

            $datatable->addColumn('reseller', function ($row) {

                return  isset($row->reseller->name)?$row->reseller->name:'';
            });
            $datatable->addColumn('status', function ($row) {

                if($row->is_active == 1){

                    $html = '<td>Active</td>';
                }else{

                    $html = '<td>InActive</td>';
                }
                return  $html;
            });

            $datatable = $datatable->rawColumns(['status','order_no']);
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


        return view('admin.reports.manufacturers')->with($data);
    }

    public function getManufacturerProduct(Request $request){


        $manufacturer_id = $request->manufacturer_id;
        $products_of_manufacturer = Products::with('generalInformation','variations','variations.variation_details')->where('manufacturer_id', $manufacturer_id)->orderBy('id','desc')->get();

        return array(
            'success'=> 'true',
            'data'=> $products_of_manufacturer,
            'message'=> __('Manufacturer Products')
        );
    }


    public function salesDashboard(Request $request)
    {
        if(!auth()->user()->can('View Sale Analysis'))
        access_denied();
       

        if ($request->ajax()) {
            $quotations_query = Quotation::join('quotation_other_info', 'quotation_other_info.quotation_id', 'quotations.id');
            $quotations_query->leftjoin('sales_teams', 'sales_teams.id', 'quotation_other_info.sales_team_id');
            $quotations_query->select('quotations.id',DB::raw("DATE_FORMAT(quotations.created_at, '%Y-%m-%d') new_date"));

            $period = null;
            $dates = array();
            if(isset($request->country_id) && $request->country_id != '' && $request->country_id != null){
                $quotations_query->whereHas('customer', function ($query) use($request){
                    $query->where('country_id',$request->country_id);
                });
            }
            if(isset($request->start_date) && $request->start_date != '' ){
                $quotations_query->whereBetween('quotations.created_at', [Carbon::parse($request->start_date), Carbon::parse($request->end_date)->addDay()]);
                $period = CarbonPeriod::create(Carbon::parse($request->start_date)->format('Y-m-d'), Carbon::parse($request->end_date)->format('Y-m-d'));
                foreach ($period as $date) {
                    array_push($dates, $date->format('Y-m-d'));
                }
            }else{
                $quotations_query->whereDate('quotations.created_at', '>', Carbon::now()->subDays(30));
                $period = CarbonPeriod::create(Carbon::now()->subDays(30)->format('Y-m-d'), Carbon::now()->format('Y-m-d'));
                foreach ($period as $date) {
                    array_push($dates, $date->format('Y-m-d'));
                }
            }
            if(isset($request->customer_id) && $request->customer_id != ''){
                $quotations_query->where('quotations.customer_id', $request->customer_id);
            }
            if(isset($request->sales_person_id) && $request->sales_person_id != ''){
                $quotations_query->where('quotation_other_info.salesperson_id', $request->sales_person_id);
            }
            if(isset($request->sales_team_id) && $request->sales_team_id != ''){
                $quotations_query->where('quotation_other_info.sales_team_id', $request->sales_team_id);
            }
            if(isset($request->currency) && $request->currency != ''){
                $quotations_query->where('currency', $request->currency);
            }
            if(isset($request->product_id) && $request->product_id != null && $request->product_id != ''){
                $quotations_query->whereHas('order_lines', function($query) use($request){
                    $query->where('product_id', $request->product_id);
                    if(isset($request->variation_id) && $request->variation_id != null && $request->variation_id != ''){
                        $query->where('variation_id', $request->variation_id);
                    }
                });
            }

            $quotations_query->orderBy('new_date','desc');
            $quotations_query->groupBy('quotations.id');
            $quotations = $quotations_query->get();
            foreach($quotations as $ind => $d){
                // dd($data);
                if(isset($request->invoice_status) && $request->invoice_status != ''){
                    switch ($request->invoice_status) {
                        case 0:
                            if(count($d->invoices) > 0)
                            {
                                $quotations->forget($ind);
                            }
                            break;
                        case 1:
                            # Partially Paid
                            if(number_format($d->total,2) == number_format($d->invoicedamount,2)){
                                $quotations->forget($ind);
                            }
                            elseif($d->invoicedamount != 0 && number_format($d->total,2) > number_format($d->invoicedamount,2)){

                            }else{
                                $quotations->forget($ind);
                            }
                            break;
                        case 2:
                            # Fully Paid
                            if(number_format($d->total,2) == number_format($d->invoicedamount,2)){
                            }
                            elseif($d->invoicedamount != 0 && number_format($d->total,2) > number_format($d->invoicedamount,2)){
                                $quotations->forget($ind);
                            }else{
                                $quotations->forget($ind);
                            }
                            break;
                        case 3:
                            # Un-Paid
                            if(count($d->invoices) == 0){

                                $quotations->forget($ind);
                            }
                            if(number_format($d->total,2) == number_format($d->invoicedamount,2)){
                                $quotations->forget($ind);
                            }
                            elseif($d->invoicedamount != 0 && number_format($d->total,2) > number_format($d->invoicedamount,2)){
                                $quotations ->forget($ind);
                            }
                            break;
                        default:
                            # code...
                            break;
                    }
                }
            }
            // Chart Data
            $chart_data = array();
            //Total Sales
            $data['total_sales'] = 0;
            //Total Tax
            $data['total_tax'] = 0;
            //Orders
            $data['no_of_orders'] = count($quotations);
            //Customers
            $data['customer_count'] = 0;
            $customer_arr = array();
            //# Lines
            $data['no_of_lines'] = 0;

            foreach($quotations as $quotation)
            {
                $q = Quotation::where('id', $quotation->id)->withCount('order_lines')->first();
                $q_total = floatval(str_replace(",","",$q->total)*$q->exchange_rate);
                $q_total_tax = 0;
                foreach($q->order_lines as $o){
                    $subtotal = $o->qty * $o->unit_price *$q->exchange_rate;
                    $taxes = QuotationOrderLineTax::with('tax')->where('quotation_order_line_id',$o->id)->get();
                    foreach($taxes as $o_tax)
                    {
                        if($o_tax->tax != null){
                            switch($o_tax->tax->computation)
                            {
                                case 0:
                                    $q_total_tax += $o_tax->tax->amount;
                                    break;
                                case 1:
                                    $q_total_tax += $subtotal * $o_tax->tax->amount / 100;
                                    break;
                            }
                        }
                    }
                    $q_total_tax += $subtotal * $q->vat_percentage / 100;
                }
                if( !isset($chart_data[$quotation->new_date]) ){
                    $chart_data[$quotation->new_date] = $q_total;
                }else{
                    $chart_data[$quotation->new_date] += $q_total;
                }
                $data['total_sales'] += $q_total;
                $data['total_tax'] += $q_total_tax;
                $data['no_of_lines'] += $q->order_lines_count;
                if(!in_array($q->customer_id, $customer_arr)){
                    $data['customer_count'] += 1;
                }
                array_push($customer_arr,$q->customer_id);

            }
            $data['sales_data'] = array();


            foreach($dates as $date )
            {
                $item = (object)array();
                if(isset($chart_data[$date]))
                {
                    $item->date = $date;
                   $item->sales = currency_format($chart_data[$date],'','',1);
                }else{
                    $item->date = $date;
                    $item->sales = 0;
                }
                array_push($data['sales_data'], $item);
            }
            return $data;
        }

        $data['customers'] = Contact::where('status',1)->whereIn('type',[2,3,4])->get();
        $data['salespersons'] = Admin::where('is_active',1)->get();
        $data['salesteams'] = SalesTeam::all();

        $period = CarbonPeriod::create(Carbon::now()->subDays(30)->format('Y-m-d'), Carbon::now()->format('Y-m-d'));
        $dates = array();
        foreach ($period as $date) {
            array_push($dates, $date->format('Y-m-d'));
        }

        // $quotations_query = Quotation::join('quotation_other_info', 'quotation_other_info.quotation_id', 'quotations.id');
        // $quotations_query->leftjoin('sales_teams', 'sales_teams.id', 'quotation_other_info.sales_team_id');
        // $quotations_query->select('quotations.customer_id','quotations.id',DB::raw("DATE_FORMAT(quotations.created_at, '%Y-%m-%d') new_date"));
        // $quotations_query->whereDate('quotations.created_at', '>', Carbon::now()->subDays(30));
        // $quotations_query->orderBy('new_date','asc');
        // $quotations_query->groupBy('quotations.id');
        // $quotations = $quotations_query->get();

        // Chart Data
        $chart_data = array();
        //Total Sales
        $data['total_sales'] = 0;
        //Total Tax
        $data['total_tax'] = 0;
        //Orders
        $data['no_of_orders'] = 0;
        //Customers
        $data['customer_count'] = 0;
        $customer_arr = array();
        //# Lines
        $data['no_of_lines'] = 0;
        // foreach($quotations as $quotation)
        // {
        //     $q = Quotation::where('id', $quotation->id)->withCount('order_lines')->first();
        //     $q_total = floatval(str_replace(",","",$q->total));
        //     $q_total_tax = floatval(str_replace(",","",$q->totaltaxcurrency));
        //     if( !isset($chart_data[$quotation->new_date]) ){
        //         $chart_data[$quotation->new_date] = $q_total;
        //     }else{
        //         $chart_data[$quotation->new_date] += $q_total;
        //     }
        //     $data['total_sales'] += $q_total;
        //     $data['total_tax'] += floatval(str_replace(",","",$q_total_tax));
        //     $data['no_of_lines'] += $q->order_lines_count;
        //     if(!in_array($q->customer_id, $customer_arr)){
        //         $data['customer_count'] += 1;
        //     }
        //     array_push($customer_arr,$q->customer_id);

        // }
        $data['sales_data'] = array();

        foreach($dates as $date )
        {
            $item = (object)array();
            if(isset($chart_data[$date]))
            {
                $item->date = $date;
                $item->sales = 0.00;
            }else{
                $item->date = $date;
                $item->sales = 0.00;
            }
            array_push($data['sales_data'], $item);
        }
        $data['currencies'] = Quotation::groupBy('currency')->pluck('currency','currency_symbol')->toArray();
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


        return view('admin.reports.sales-dashboard', $data);
    }

    public function websiteDashboard(Request $request){

        if(!auth()->user()->can('Website Dashboard'))
        access_denied();

        if ($request->ajax()) {
            $quotations_query = Quotation::join('quotation_other_info', 'quotation_other_info.quotation_id', 'quotations.id');
            $quotations_query->leftjoin('sales_teams', 'sales_teams.id', 'quotation_other_info.sales_team_id');
            $quotations_query->select('quotations.id',DB::raw("DATE_FORMAT(quotations.created_at, '%Y-%m-%d') new_date"));
            $quotations_query->where('quotation_other_info.sales_team_id', 1)->orderBy('id','desc');

            $period = null;
            $dates = array();

            if(isset($request->start_date) && $request->start_date != '' ){
                $quotations_query->whereBetween('quotations.created_at', [Carbon::parse($request->start_date), Carbon::parse($request->end_date)->addDay()]);
                $period = CarbonPeriod::create(Carbon::parse($request->start_date)->format('Y-m-d'), Carbon::parse($request->end_date)->format('Y-m-d'));
                foreach ($period as $date) {
                    array_push($dates, $date->format('Y-m-d'));
                }
            }else{
                $quotations_query->whereDate('quotations.created_at', '>', Carbon::now()->subDays(30));
                $period = CarbonPeriod::create(Carbon::now()->subDays(30)->format('Y-m-d'), Carbon::now()->format('Y-m-d'));
                foreach ($period as $date) {
                    array_push($dates, $date->format('Y-m-d'));
                }
            }
            if(isset($request->currency) && $request->currency != ''){
                $quotations_query->where('quotations.currency', $request->currency);
            }
            if(isset($request->customer_id) && $request->customer_id != ''){
                $quotations_query->where('quotations.customer_id', $request->customer_id);
            }
            if(isset($request->sales_person_id) && $request->sales_person_id != ''){
                $quotations_query->where('quotation_other_info.salesperson_id', $request->sales_person_id);
            }


            $quotations_query->orderBy('new_date','asc');
            $quotations_query->groupBy('quotations.id');
            $quotations = $quotations_query->get();

            // Chart Data
            $chart_data = array();
            //Total Sales
            $data['total_sales'] = 0;
            //Total Tax
            $data['total_tax'] = 0;
            //Orders
            $data['no_of_orders'] = count($quotations);
            //Customers
            $data['customer_count'] = 0;
            $customer_arr = array();
            //# Lines
            $data['no_of_lines'] = 0;

            foreach($quotations as $quotation)
            {
                $q = Quotation::where('id', $quotation->id)->withCount('order_lines')->first();

                $q_total = floatval(str_replace(",","",$q->total)*$q->exchange_rate);
                $q_total_tax = 0;
                foreach($q->order_lines as $o){
                    $subtotal = $o->qty * $o->unit_price *$q->exchange_rate;
                    $taxes = QuotationOrderLineTax::with('tax')->where('quotation_order_line_id',$o->id)->get();
                    foreach($taxes as $o_tax)
                    {
                        if($o_tax->tax != null){
                            switch($o_tax->tax->computation)
                            {
                                case 0:
                                    $q_total_tax += $o_tax->tax->amount;
                                    break;
                                case 1:
                                    $q_total_tax += $subtotal * $o_tax->tax->amount / 100;
                                    break;
                            }
                        }
                    }
                    $q_total_tax += $subtotal * $q->vat_percentage / 100;
                }
                if( !isset($chart_data[$quotation->new_date]) ){
                    $chart_data[$quotation->new_date] = $q_total;
                }else{
                    $chart_data[$quotation->new_date] += $q_total;
                }
                $data['total_sales'] += floatval(str_replace(",","",$q_total));
                $data['total_tax'] += floatval(str_replace(",","",$q_total_tax));

                $data['no_of_lines'] += $q->order_lines_count;
                if(!in_array($q->customer_id, $customer_arr)){
                    $data['customer_count'] += 1;
                }
                array_push($customer_arr,$q->customer_id);

            }
            $data['sales_data'] = array();


            foreach($dates as $date )
            {
                $item = (object)array();
                if(isset($chart_data[$date]))
                {
                    $item->date = $date;
                   $item->sales = currency_format($chart_data[$date],'','',1);
                }else{
                    $item->date = $date;
                    $item->sales = 0;
                }
                array_push($data['sales_data'], $item);
            }
            return $data;
        }

        $data['customers'] = Contact::where('status',1)->whereIn('type',[2,3])->get();
        $data['salespersons'] = Admin::where('is_active',1)->get();
        $data['salesteams'] = SalesTeam::all();


        $period = CarbonPeriod::create(Carbon::now()->subDays(30)->format('Y-m-d'), Carbon::now()->format('Y-m-d'));
        $dates = array();
        foreach ($period as $date) {
            array_push($dates, $date->format('Y-m-d'));
        }

        // $quotations_query = Quotation::join('quotation_other_info', 'quotation_other_info.quotation_id', 'quotations.id');
        // $quotations_query->leftjoin('sales_teams', 'sales_teams.id', 'quotation_other_info.sales_team_id');
        // $quotations_query->select('quotations.customer_id','quotations.id',DB::raw("DATE_FORMAT(quotations.created_at, '%Y-%m-%d') new_date"));
        // $quotations_query->whereDate('quotations.created_at', '>', Carbon::now()->subDays(30));
        // $quotations_query->orderBy('new_date','asc');
        // $quotations_query->where('quotation_other_info.sales_team_id', 1);
        // $quotations_query->groupBy('quotations.id');
        // $quotations = $quotations_query->get();

        // Chart Data
        $chart_data = array();
        //Total Sales
        $data['total_sales'] = 0;
        //Total Tax
        $data['total_tax'] = 0;
        //Orders
        $data['no_of_orders'] = 0;

        //Customers
        $data['customer_count'] = 0;
        $customer_arr = array();
        //# Lines
        $data['no_of_lines'] = 0;
        // foreach($quotations as $quotation)
        // {
        //     $q = Quotation::where('id', $quotation->id)->withCount('order_lines')->first();

        //     // $q_total = floatval(str_replace(",","",$q->total));
        //     // dd(str_replace(",","",$q->all_quotations_total_currency));
        //     $q_total = floatval(str_replace(",","",floatval($q->all_quotations_total_currency))*$q->exchange_rate);
        //         $q_total_tax = floatval(str_replace(",","",$q->totaltaxcurrency)*$q->exchange_rate);
        //     if( !isset($chart_data[$quotation->new_date]) ){
        //         $chart_data[$quotation->new_date] = $q_total;
        //     }else{
        //         $chart_data[$quotation->new_date] += $q_total;
        //     }
        //     // $data['total_sales'] += floatval(str_replace(",","",$q_total));
        //     // $data['total_tax'] += floatval(str_replace(",","",$q_total_tax));
        //     // $data['no_of_lines'] += $q->order_lines_count;
        //     if(!in_array($q->customer_id, $customer_arr)){
        //         $data['customer_count'] += 1;
        //     }
        //     array_push($customer_arr,$q->customer_id);

        // }
        $data['sales_data'] = array();

        foreach($dates as $date )
        {
            $item = (object)array();
            if(isset($chart_data[$date]))
            {
                $item->date = $date;
               $item->sales = 0.00;
            }else{
                $item->date = $date;
                $item->sales = 0.00;
            }
            array_push($data['sales_data'], $item);
        }
         $data['currencies'] = Quotation::groupBy('currency')->pluck('currency','currency_symbol')->toArray();
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
        // $data['mostVisitedPages'] = \Analytics::fetchMostVisitedPages(Period::create($startDate, $endDate),10);
        // $data['userTypes'] = \Analytics::fetchUserTypes(Period::create($startDate, $endDate));
        return view('admin.reports.website-dashboard',$data);
    }

    public function orders(Request $request){

        if(!auth()->user()->can('Voucher Order Listing'))
        access_denied();
        Session::put('voucher_code', $request->voucher_code);

        if ($request->ajax()) {
            $data_query = VoucherOrder::with('reseller',
                                        'vouchers',
                                        'voucher_taxes',
                                        'product',
                                        'variation',
                                        'product.generalInformation',
                                        'product.customer_taxes',
                                        'product.customer_taxes.tax',
                                        'contact_country'
                                    )->whereHas('resellers')->orderBy('created_at','desc');

            if(isset($request->country_id) && $request->country_id != '' && $request->country_id != null){
                $data_query->whereHas('contact_country', function ($query) use($request){
                    $query->where('country_id',$request->country_id);
                });
            }
            if(isset($request->action_status) && $request->action_status != '' && $request->action_status != null){
                $data_query->where('is_active',$request->action_status);

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
            $data = $data_query->get();

            $datatable = Datatables::of($data);
            $datatable->addColumn('ids', function ($row) {
                return '';
            });
            $datatable->addColumn('order_id', function ($row) {
                return str_replace(' ','',$row->reseller->name).'-'.str_pad($row->id,3,'0',STR_PAD_LEFT).'-'.date('dmY',strtotime($row->created_at));
            });
            $datatable->addColumn('reseller', function ($row) {
                return @$row->reseller->name.'<br>'.@$row->reseller->email;
            });
            $datatable->addColumn('action', function ($row)  use($request) {
                $html = '' ;
                if (auth()->user()->hasAnyPermission(['Vouchers Listing','Vouchers Payment','Download Vouchers']))
                {
                    if(auth()->user()->can('Vouchers Listing')) {
                        if(isset($request->voucher_code)){
                            $html = '<a title='.__('Vouchers').'  target="_blank" class="" href="'.route("admin.voucher.order.vouchers", Hashids::encode($row->id)).'?code='.$request->voucher_code.'">';
                        }else{
                            $html = '<a title='.__('Vouchers').' target="_blank" class="" href="'.route("admin.voucher.order.vouchers", Hashids::encode($row->id)).'">';
                        }
                            $html .= '<button class="btn btn-secondary btn-sm">';
                                $html .= '<i class="fa fa-gift" aria-hidden="true"></i>';
                            $html .= '</button>';
                        $html .= '</a>&nbsp;';

                    }
                    if(auth()->user()->can('Download Vouchers')) {
                        $html .= '<a  target="_blank" title='.__('Download').' href="'.route('admin.voucher.order.vouchers.export', Hashids::encode($row->id)).'" class="">';
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
                return currency_format($row->total_payable,$row->currency_symbol,$row->currency);
            });
            $datatable->addColumn('paid_amount', function ($row) {
                return currency_format(($row->total_payable-$row->remaining_total),$row->currency_symbol,$row->currency);
            });
            $datatable->addColumn('pending_payment', function ($row) {
                return currency_format($row->remaining_total,$row->currency_symbol,$row->currency);
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
        // dd($data['products']);
        $data['products_voucher_order'] = Products::withCount('variations')->where('is_active', 1)->get();
        // $data['vat_percentage']   = Auth::user()->contact->contact_countries->vat_in_percentage;
        $data['countries'] = ContactCountry::all();
        $data['resellers'] = Contact::with('contact_countries','user')->whereHas('user')->where('type', 3)->get();
        $data['reseller_email'] = isset($request->reseller_email) ? $request->reseller_email : '';
        $data['default_vat'] = \App\Models\SiteSettings::first()->defualt_vat;
        $data['currencies'] = VoucherOrder::groupBy('currency')->pluck('currency')->toArray();
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

        return view('admin.reports.voucher-orders-report', $data);

    }


    public function licenseAnalysis(Request $request){


        if($request->ajax()){
            $data_query = License::with('reseller','voucher','product','variation')->orderBy('id','desc');
            if(isset($request->product_id) && $request->product_id != null )
            {
                $data_query->where('product_id',$request->product_id);
            }
            if(isset($request->variation_id) && $request->variation_id != null )
            {
                $data_query->where('variation_id',$request->variation_id);
            }
            if(isset($request->status) && $request->status != null )
            {
                $data_query->where('status',$request->status);
            }
            if(isset($request->license_key) && $request->license_key != null )
            {
                $data_query->where('license_key','LIKE','%'.$request->license_key.'%');
            }
            if(isset($request->usage_status) && $request->usage_status != null )
            {
                $data_query->where('is_used',$request->usage_status);
            }
            if(isset($request->sku) && $request->sku != null )
            {
                $data_query->whereHas('variation',function($query) use($request){
                    $query->where('sku', $request->sku);
                });
            }
            if(isset($request->customer) && $request->customer != null )
            {
                $data_query->whereHas('customer',function($query) use($request){
                    $query->where('id', $request->id);
                });
            }
            if(isset($request->reseller) && $request->reseller != null )
            {
                $data_query->whereHas('reseller',function($query) use($request){
                    $query->where('id', $request->id);
                });
            }
            if(isset($request->start_date) && $request->start_date != '' ){
                $data_query->whereBetween('updated_at', [Carbon::parse($request->start_date), Carbon::parse($request->end_date)->addDay()]);
            }
            $data = $data_query->get();

            $datatable = Datatables::of($data);
            $datatable->addColumn('ids', function ($row) {
                return '';
            });
            $datatable->editColumn('license_key', function ($row) {
                return $row->license_key;
            });
            $datatable->addColumn('product_name', function ($row) {
                $html = $row->product->product_name . ' ' . @$row->variation->variation_name. '( SKU : '.@$row->variation->sku.' )';
                return $html;
            });
            $datatable->addColumn('reseller_detail', function ($row) {
                if(@$row->voucher->voucherOrder->reseller){
                    $html = $row->voucher->voucherOrder->reseller->name . '<br>' . $row->voucher->voucherOrder->reseller->email;
                    return $html;
                }
                return '';
            });
            $datatable->addColumn('customer_detail', function ($row) {
                if($row->voucher)
                {
                    if(@$row->voucher->customer){
                        $html = $row->voucher->customer->name . '<br>' . $row->voucher->customer->email;
                        return $html;
                    }
                }
                if($row->quotation_order_line)
                {
                    if(@$row->quotation_order_line->quotation->customer){
                        $html = $row->quotation_order_line->quotation->customer->name . '<br>' . $row->quotation_order_line->quotation->customer->email;
                        return $html;
                    }
                }

                return '';
            });
            $datatable->addColumn('quotation_number', function ($row) {
                if($row->quotation_order_line){
                    $html = '<a target="_blank" href="' .route('admin.quotations.show',Hashids::encode($row->quotation_order_line->quotation_id)). '">S'.str_pad($row->quotation_order_line->quotation_id, 5, '0', STR_PAD_LEFT).'</a>';
                    return $html;
                }
                return '';
            });
            $datatable->addColumn('voucher_code', function ($row) {
                if($row->voucher){
                    $html = $row->voucher->code ;
                    $html .= '<span style="color:red" >Redeemed At: '.Carbon::parse($row->voucher->redeemed_at)->format('d-M-Y').'</span>' ;
                    return $html;
                }
                else if($row->quotation_voucher)
                {
                    $html = $row->quotation_voucher->voucher_code;
                    $html .= '<span style="color:red" >Redeemed At: '.Carbon::parse($row->quotation_voucher->redeemed_at)->format('d-M-Y').'</span>' ;
                    return $html;
                }
                return '';
            });
            $datatable->addColumn('statuss', function ($row) {
                if($row->status == 0)
                    return '<span class="badge  bg-yellow">'.__('Inactive').'</span>';
                if($row->status == 1)
                    return $html ='<span class="badge  bg-green">'.__('Active').'</span>';
                if($row->status == 2)
                    return $html ='<span class="badge badge-danger bg-red">'.__('Expired').'</span>';

            });
            $datatable->addColumn('used', function ($row) {
                if($row->is_used == 0)
                    return '<span class="badge  bg-yellow">'.__('Un-used').'</span>';
                if($row->is_used == 1)
                    return $html ='<span class="badge  bg-green">'.__('Used').'</span>';
            });

            $datatable = $datatable->rawColumns(['customer_detail','reseller_detail','quotation_number','statuss','action','used']);
            return $datatable->make(true);
        }
        // $productList = Products::with( 'generalInformation', 'customer_taxes', 'variations', 'variations.variation_details' )->where('product_type', 0)->where('is_active', 1)->get();
        $productList = Products::with( 'generalInformation', 'customer_taxes', 'variations', 'variations.variation_details' )->where('product_type', 0)->get();
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
        $data['resellers'] = User::whereHas('contact',function($query){
            $query->where('type',3);
        })->get();
        $data['customers'] = User::whereHas('contact',function($query){
            $query->whereIn('type',[0,2]);
        })->get();

        return view('admin.reports.licenses')->with($data);
    }


    public function abondedCart(Request $request){

        if(!auth()->user()->can('Website Abandoned Cart Listing'))
        access_denied();

        if ($request->ajax()) {
            $data = Cart::with('user')->where('is_checkout',0)->whereDate('created_at','<', \Carbon\Carbon::now()->subDays(5))->orderBy('id','desc')->get();
            $datatable = Datatables::of($data)->addIndexColumn();
            $datatable->editColumn('user', function ($row) {
                return ucfirst($row->user->name).'<br>'.$row->user->email;
            });
            $datatable->addColumn('cart_items', function ($row) {
                $html = '';
                foreach($row->cart_items as $index => $cart_item){
                    $html .= $cart_item->product->product_name;
                    if($cart_item->variation_id != null){
                        $html .= ' ' . @$cart_item->variation->variation_name;
                    }
                    $html .= ' ( Qty: '.$cart_item->qty.' )';
                    $html .= $index < count($row->cart_items) - 1 ? '<br>'  : '';
                }
                return $html;
            });
            $datatable = $datatable->rawColumns(['cart_items', 'user']);
            return $datatable->make(true);
        }

        $data['customers'] = Contact::where('status',1)->whereIn('type',[2,4])->get();

        return view('admin.reports.abonded-carts')->with($data);
    }


    public function exportSales(Request $request){



        $quotations_query = Quotation::join('quotation_other_info', 'quotation_other_info.quotation_id', 'quotations.id');
        $quotations_query->leftjoin('sales_teams', 'sales_teams.id', 'quotation_other_info.sales_team_id');
        $quotations_query->select('quotations.id',DB::raw("DATE_FORMAT(quotations.created_at, '%Y-%m-%d') new_date"));
        $period = null;
        $dates = array();
        if(isset($request->country_id) && $request->country_id != '' && $request->country_id != null){
            $quotations_query->whereHas('customer', function ($query) use($request){
                $query->where('country_id',$request->country_id);
            });
        }
        if(isset($request->start_date) && $request->start_date != '' ){
            $quotations_query->whereBetween('quotations.created_at', [Carbon::parse($request->start_date), Carbon::parse($request->end_date)->addDay()]);
            $period = CarbonPeriod::create(Carbon::parse($request->start_date)->format('Y-m-d'), Carbon::parse($request->end_date)->format('Y-m-d'));
            foreach ($period as $date) {
                array_push($dates, $date->format('Y-m-d'));
            }
        }else{
            $quotations_query->whereDate('quotations.created_at', '>', Carbon::now()->subDays(30));
            $period = CarbonPeriod::create(Carbon::now()->subDays(30)->format('Y-m-d'), Carbon::now()->format('Y-m-d'));

            foreach ($period as $date) {
                array_push($dates, $date->format('Y-m-d'));
            }
        }
        if(isset($request->customer_id) && $request->customer_id != ''){
            $quotations_query->where('quotations.customer_id', $request->customer_id);
        }
        if(isset($request->sales_person_id) && $request->sales_person_id != ''){
            $quotations_query->where('quotation_other_info.salesperson_id', $request->sales_person_id);
        }
        if(isset($request->sales_team_id) && $request->sales_team_id != ''){
            $quotations_query->where('quotation_other_info.sales_team_id', $request->sales_team_id);
        }
        // dd($request);
        if(isset($request->currency_id) && $request->currency_id != ''){
            $quotations_query->where('currency', $request->currency_id);
        }
        if(isset($request->product_id) && $request->product_id != null && $request->product_id != ''){
            $quotations_query->whereHas('order_lines', function($query) use($request){
                $query->where('product_id', $request->product_id);
                if(isset($request->variation_id) && $request->variation_id != null && $request->variation_id != ''){
                    $query->where('variation_id', $request->variation_id);
                }
            });
        }

        $quotations_query->orderBy('new_date','asc');
        $quotations_query->groupBy('quotations.id');
        $quotations = $quotations_query->get();

        $data['total_sales'] = 0;
        //Total Tax
        $data['total_tax'] = 0;
        //Orders
        $data['no_of_orders'] = count($quotations);

        //Customers
        $data['customer_count'] = 0;
        $customer_arr = array();
        //# Lines
        $data['no_of_lines'] = 0;
        foreach($quotations as $quotation)
        {
            $q = Quotation::where('id', $quotation->id)->withCount('order_lines')->first();
            $q_total = floatval(str_replace(",","",$q->total)*$q->exchange_rate);
            $q_total_tax = 0;
            foreach($q->order_lines as $o){
                $subtotal = $o->qty * $o->unit_price *$q->exchange_rate;
                $taxes = QuotationOrderLineTax::with('tax')->where('quotation_order_line_id',$o->id)->get();
                foreach($taxes as $o_tax)
                {
                    if($o_tax->tax != null){
                        switch($o_tax->tax->computation)
                        {
                            case 0:
                                $q_total_tax += $o_tax->tax->amount;
                                break;
                            case 1:
                                $q_total_tax += $subtotal * $o_tax->tax->amount / 100;
                                break;
                        }
                    }
                }
                $q_total_tax += $subtotal * $q->vat_percentage / 100;
            }
            if( !isset($chart_data[$quotation->new_date]) ){
                $chart_data[$quotation->new_date] = $q_total;
            }else{
                $chart_data[$quotation->new_date] += $q_total;
            }
            $data['total_sales'] += $q_total;
            $data['total_tax'] += $q_total_tax;
            $data['no_of_lines'] += $q->order_lines_count;
            if(!in_array($q->customer_id, $customer_arr)){
                $data['customer_count'] += 1;
            }
            array_push($customer_arr,$q->customer_id);

        }
        $data['sales_data'] = array();


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



        $sheet_array[] = array();
        $sheet_array[] = [
            'Sales Report',
        ];
        $sheet_array[] = ['Metrics', 'Value'];
        $sheet_array[] = [
            'Metrics' => 'Total Sales' ,
            'Value'  => currency_format($data['total_sales'],'','',1)
        ];
        $sheet_array[] = [
            'Metrics' => 'Untaxed Total' ,
            'Value' =>  currency_format($data['total_sales'] - $data['total_tax'],'','',1)
        ];
        $sheet_array[] = [
            'Metrics' => 'Total Taxes' ,
            'Value' => currency_format($data['total_tax'],'','',1)
        ];
        $sheet_array[] = [
            'Metrics' => 'Orders' ,
            'Value' => $data['no_of_orders']
        ];
        $sheet_array[] = [
            'Metrics' => 'Customers' ,
            'Value' => $data['customer_count']
        ];
        $sheet_array[] = [
            'Metrics' => 'Lines' ,
            'Value' => $data['no_of_lines']
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
        $old_file = public_path().'/storage/uploads/sales-management/Sales-Resport.xlsx';
        if (file_exists($old_file)) {
            unlink($old_file);
        }
        ob_clean();
        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="sales-resport.xlsx"');
        $writer->save("php://output");
        return public_path('storage/uploads/sales-management/Sales-Resport.xlsx');



    }


    public function exportWebsiteAnalysis(Request $request){


        $quotations_query = Quotation::join('quotation_other_info', 'quotation_other_info.quotation_id', 'quotations.id');
        $quotations_query->leftjoin('sales_teams', 'sales_teams.id', 'quotation_other_info.sales_team_id');
        $quotations_query->select('quotations.id',DB::raw("DATE_FORMAT(quotations.created_at, '%Y-%m-%d') new_date"));
        $quotations_query->where('quotation_other_info.sales_team_id', 1)->orderBy('id','desc');
        // $quotations_query->where('quotations.currency', $request->currency);

        $period = null;
        $dates = array();

        if(isset($request->start_date) && $request->start_date != '' ){
            $quotations_query->whereBetween('quotations.created_at', [Carbon::parse($request->start_date), Carbon::parse($request->end_date)->addDay()]);
            $period = CarbonPeriod::create(Carbon::parse($request->start_date)->format('Y-m-d'), Carbon::parse($request->end_date)->format('Y-m-d'));
            foreach ($period as $date) {
                array_push($dates, $date->format('Y-m-d'));
            }
        }else{
            $quotations_query->whereDate('quotations.created_at', '>', Carbon::now()->subDays(30));
            $period = CarbonPeriod::create(Carbon::now()->subDays(30)->format('Y-m-d'), Carbon::now()->format('Y-m-d'));
            foreach ($period as $date) {
                array_push($dates, $date->format('Y-m-d'));
            }
        }
        if(isset($request->currency_id) && $request->currency_id != ''){

            $quotations_query->where('currency', $request->currency_id);
        }
        if(isset($request->customer_id) && $request->customer_id != ''){
            $quotations_query->where('quotations.customer_id', $request->customer_id);
        }
        if(isset($request->sales_person_id) && $request->sales_person_id != ''){
            $quotations_query->where('quotation_other_info.salesperson_id', $request->sales_person_id);
        }
        if(isset($request->sales_team_id) && $request->sales_team_id != ''){
            $quotations_query->where('quotation_other_info.sales_team_id', $request->sales_team_id);
        }
        if(isset($request->country_id) && $request->country_id != '' && $request->country_id != null){
            $quotations_query->whereHas('customer', function ($query) use($request){
                $query->where('country_id',$request->country_id);
            });
        }
        if(isset($request->product_id) && $request->product_id != null && $request->product_id != ''){
            $quotations_query->whereHas('order_lines', function($query) use($request){
                $query->where('product_id', $request->product_id);
                if(isset($request->variation_id) && $request->variation_id != null && $request->variation_id != ''){
                    $query->where('variation_id', $request->variation_id);
                }
            });
        }


        $quotations_query->orderBy('new_date','asc');
        $quotations_query->groupBy('quotations.id');
        $quotations = $quotations_query->get();

        $data['total_sales'] = 0;
        //Total Tax
        $data['total_tax'] = 0;
        //Orders
        $data['no_of_orders'] = count($quotations);
        //Customers
        $data['customer_count'] = 0;
        $customer_arr = array();
        //# Lines
        $data['no_of_lines'] = 0;
        foreach($quotations as $quotation)
        {
            $q = Quotation::where('id', $quotation->id)->withCount('order_lines')->first();

            $q_total = floatval(str_replace(",","",$q->total)*$q->exchange_rate);
            $q_total_tax = 0;
            foreach($q->order_lines as $o){
                $subtotal = $o->qty * $o->unit_price *$q->exchange_rate;
                $taxes = QuotationOrderLineTax::with('tax')->where('quotation_order_line_id',$o->id)->get();
                foreach($taxes as $o_tax)
                {
                    if($o_tax->tax != null){
                        switch($o_tax->tax->computation)
                        {
                            case 0:
                                $q_total_tax += $o_tax->tax->amount;
                                break;
                            case 1:
                                $q_total_tax += $subtotal * $o_tax->tax->amount / 100;
                                break;
                        }
                    }
                }
                $q_total_tax += $subtotal * $q->vat_percentage / 100;
            }


            if( !isset($chart_data[$quotation->new_date]) ){
                $chart_data[$quotation->new_date] = $q_total;
            }else{
                $chart_data[$quotation->new_date] += $q_total;
            }
            $data['total_sales'] +=$q_total;

            $data['total_tax'] += $q_total_tax;

            $data['no_of_lines'] += $q->order_lines_count;
            if(!in_array($q->customer_id, $customer_arr)){
                $data['customer_count'] += 1;
            }
            array_push($customer_arr,$q->customer_id);

        }
        $data['sales_data'] = array();

        foreach($dates as $date)
        {
            $item = (object)array();
            if(isset($chart_data[$date]))
            {
                $item->date = $date;
                $item->sales = currency_format($chart_data[$date],'','',1);
            }else{
                $item->date = $date;
                $item->sales = 0;
            }
            array_push($data['sales_data'], $item);
        }
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
        $sheet_array[] = array();
        $sheet_array[] = [
            'Sales Report',
        ];
        $sheet_array[] = ['Metrics', 'Value'];
        $sheet_array[] = [
            'Metrics' => 'Total Sales' ,
            'Value'  => currency_format($data['total_sales'],'','',1)
        ];
        $sheet_array[] = [
            'Metrics' => 'Untaxed Total' ,
            'Value' =>  currency_format($data['total_sales'] - $data['total_tax'],'','',1)
        ];
        $sheet_array[] = [
            'Metrics' => 'Total Taxes' ,
            'Value' => currency_format($data['total_tax'],'','',1)
        ];
        $sheet_array[] = [
            'Metrics' => 'Orders' ,
            'Value' => $data['no_of_orders']
        ];
        $sheet_array[] = [
            'Metrics' => 'Customers' ,
            'Value' => $data['customer_count']
        ];
        $sheet_array[] = [
            'Metrics' => 'Lines' ,
            'Value' => $data['no_of_lines']
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
        $old_file = public_path().'/storage/uploads/sales-management/Website-Report-Analysis.xlsx';
        if (file_exists($old_file)) {
            unlink($old_file);
        }
        ob_clean();
        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="website-report-analysis.xlsx"');
        $writer->save("php://output");

        return public_path('storage/uploads/sales-management/Website-Report-Analysis.xlsx');


    }

    public function invoicesAnalysis(Request $request){

        if($request->ajax())
        {

            $invoice_query = Invoice::with(['invoice_order_lines','invoice_payment_history','quotation'])->orderBy('id','desc');
            $start_date     = $request->start_date;
            $end_date       = $request->end_date;

            if(isset($request->start_date) && $request->start_date != '' ){
                $invoice_query->whereBetween('created_at', [Carbon::parse($request->start_date), Carbon::parse($request->end_date)->addDay()]);

            }

            if(isset($request->start_date_update) && $request->start_date_update != '' ){
                $invoice_query->whereBetween('updated_at', [Carbon::parse($request->start_date_update), Carbon::parse($request->end_date_update)->addDay()]);

            }

            if(isset($request->quotation) && $request->quotation != null && $request->quotation != ''){

                $quotation_id   = trim($request->quotation,'Q000');

                $invoice_query->where('quotation_id', $quotation_id);
            }

            if(isset($request->customer_id) && $request->customer_id != ''){
                $invoice_query->whereHas('quotation.customer', function($query) use($request){
                    $query->where('customer_id', $request->customer_id);
                });
            }

            if(isset($request->invoice_payment) && $request->invoice_payment != null && $request->invoice_payment != ''){


                $invoice_query->whereHas('invoice_payment_history', function($query) use($request){
                    $query->where('method', $request->invoice_payment);
                });
            }
            // Invoice Status
            // <option value="Paid">Paid</option>
            // <option value="Partially Paid">Partially Paid</option>
            // <option value="Not Paid">Not Paid</option>
            // <option value="Refunded">Refunded</option>
            if(isset($request->invoice_status) && $request->invoice_status != null && $request->invoice_status != ''){

                if($request->invoice_status == 'Paid'){
                    $invoice_query->where('is_paid',1)->whereNull('refunded_at');
                }
                if($request->invoice_status == 'Not Paid'){
                    $invoice_query->where('is_paid', 0)->whereNull('refunded_at');
                }
                if($request->invoice_status == 'Partially Paid'){

                    $invoice_query->where('is_partially_paid',1)->whereNull('refunded_at');
                }
                if($request->invoice_status == 'Refunded'){
                    $invoice_query->whereNotNull('refunded_at');
                }

            }

            if(isset($request->product_id) && $request->product_id != null && $request->product_id != ''){
                $invoice_query->whereHas('quotation.order_lines.product', function($query) use($request){
                    $query->where('product_id', $request->product_id);
                    if(isset($request->variation_id) && $request->variation_id != null && $request->variation_id != ''){
                        $query->where('variation_id', $request->variation_id);
                    }
                });
            }

            if(isset($request->manufacturer_id) && $request->manufacturer_id != null && $request->manufacturer_id != ''){
                $invoice_query->whereHas('quotation.order_lines.product.manufacturer', function($query) use($request){
                    $query->where('manufacturer_id', $request->manufacturer_id);
                });
            }
            $data       = $invoice_query->get();

            $datatable = Datatables::of($data);

            $datatable->addColumn('invoice_number', function ($row) {
                $html = '';
                // return  'TIM/'.\Carbon\Carbon::parse($row->created_at)->format('Y').'/'.str_pad($row->id, 3, '0', STR_PAD_LEFT);
                $html .= '<a class="" href="'.route("admin.quotation.invoice.show", Hashids::encode($row->id)).'" target="_blank">';
                    $html .= 'TIM/'.\Carbon\Carbon::parse($row->created_at)->format('Y').'/'.str_pad($row->id, 3, '0', STR_PAD_LEFT);
                $html .=   '</a>';

                return $html;
            });
            $datatable->addColumn('order_number', function ($row) {
                return  'S'.str_pad($row->quotation->id, 5, '0', STR_PAD_LEFT);
            });
            $datatable->addColumn('created_date', function ($row) {
                return date('d-M-Y', strtotime($row->created_at));
            });
            $datatable->addColumn('update_date', function ($row) {
                return date('d-M-Y', strtotime($row->updated_at));
            });
            $datatable->addColumn('total', function ($row) {

                return currency_format($row->amount_paid, $row->quotation->currency_symbol,$row->quotation->currency);
            });
            $datatable->addColumn('status', function ($row) {
                if($row->refunded_at == null)
                {
                    if($row->is_paid == 1){
                        if($row->is_partially_paid == 1){
                            return '<span class="tagged quote">'.__("Partially Paid").'</span>';
                        }else{
                            return '<span class="tagged success">'.__("Paid").'</span>';
                        }
                    }else{
                        return '<span class="tagged danger">'.__("Not Paid").'</span>';
                    }
                }else{
                    return'<span class="tagged danger">'. __('Refunded At ').' '.\Carbon\Carbon::parse($row->refunded_at)->format('d-M-Y').'</span>';
                }
            });
            $datatable->addColumn('invoice_payment', function ($row) {
               foreach($row->invoice_payment_history as $history){

                return isset($history->method)?$history->method:'';
               }

            });
            $datatable = $datatable->rawColumns(['status','created_at','invoice_number']);
            return $datatable->make(true);
        }
        $data['customers'] = Contact::where('status',1)->whereIn('type',[2,3,4])->get();
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
        return view('admin.reports.invoices')->with($data);
    }


    public function voucherPayment(Request $request){
        if($request->ajax())
        {

            // $voucher_payment = VoucherPayment::with(['reseller','voucher_payment_references'])->orderBy('id','desc');
            $voucher_payment = VoucherPayment::join('voucher_payment_order_details','voucher_payment_order_details.voucher_payment_id','voucher_payments.id')
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
                            'voucher_payments.currency_symbol',
                            'voucher_payments.created_at'
                        )->groupBy('voucher_payments.id');

            if(isset($request->start_date) && $request->start_date != '' ){
                $voucher_payment->whereBetween('voucher_payments.created_at', [Carbon::parse($request->start_date), Carbon::parse($request->end_date)->addDay()]);

            }
            if(isset($request->order_number) && $request->order_number != '' ){
                $order_number = explode('/',$request->order_number);
                $order_number = array_reverse($order_number)[0];
                $voucher_payment->where('voucher_payments.id', $order_number);
            }
            if(isset($request->start_date_update) && $request->start_date_update != '' ){
                $voucher_payment->whereBetween('updated_at', [Carbon::parse($request->start_date_update), Carbon::parse($request->end_date_update)->addDay()]);

            }

            if(isset($request->payment_status) && $request->payment_status != '' ){

                if($request->payment_status == 1){
                    $voucher_payment->where('is_partial_paid','!=',1);
                    $voucher_payment->where('is_paid',$request->payment_status)->whereNull('refunded_at');
                }elseif($request->payment_status == 0){
                    $voucher_payment->where('is_paid',$request->payment_status)->whereNull('refunded_at');
                }elseif($request->payment_status == 3){
                    $voucher_payment->where('is_partial_paid',1);
                }
            }

            if(isset($request->product_id) && $request->product_id != null && $request->product_id != ''){
                $voucher_payment->whereHas('details.voucher_order.product', function($query) use($request){
                    $query->where('product_id', $request->product_id);
                    if(isset($request->variation_id) && $request->variation_id != null && $request->variation_id != ''){
                        $query->where('variation_id', $request->variation_id);
                    }
                });
            }

            if(isset($request->manufacturer_id) && $request->manufacturer_id != null && $request->manufacturer_id != ''){
                $voucher_payment->whereHas('details.voucher_order.product.manufacturer', function($query) use($request){
                    $query->where('manufacturer_id', $request->manufacturer_id);
                });
            }

            if(isset($request->name_email) && $request->name_email != null && $request->name_email != ''){
                $voucher_payment->whereHas('details.voucher_order.reseller', function($query) use($request){
                    $query->where('name','LIKE','%'.$request->name_email.'%');
                    $query->orWhere('email','LIKE','%'.$request->name_email.'%');
                });
            }
            if(isset($request->invoice_payment) && $request->invoice_payment != null && $request->invoice_payment != ''){


                $voucher_payment->whereHas('voucher_payment_references', function($query) use($request){
                    $query->where('method', $request->invoice_payment);
                });
            }
            $data       = $voucher_payment->orderBy('voucher_payments.id','desc');

            $datatable = Datatables::of($data);
            $datatable->addColumn('invoice_no', function ($row) {
                return 'TIM/'.\Carbon\Carbon::parse($row->created_at)->format('Y').'/'.str_pad($row->id, 3, '0', STR_PAD_LEFT);
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
                $html = currency_format($row->total_amount,$row->currency_symbol,$row->currency);
                return $html;
            });
            $datatable->addColumn('created_at', function ($row) {
                return date('d-M-Y', strtotime($row->created_at));
            });
            $datatable->addColumn('updated_at', function ($row) {
                // dd(date('d-M-Y', strtotime($row->updated_at)),$row->updated_at,Carbon::parse($row->created_at)->greaterThan(Carbon::parse($row->updated_at)));
                return $row->updated_at != null ? date('d-M-Y', strtotime($row->updated_at)) : '';
            });

            $datatable = $datatable->rawColumns(['order_no','payment_status','created_at']);
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
        return view('admin.reports.voucher-payment')->with($data);
    }

    public function distributorVoucherPayment(Request $request){
        if($request->ajax())
        {
            $voucher_payment = VoucherPayment::join('voucher_payment_order_details','voucher_payment_order_details.voucher_payment_id','voucher_payments.id')
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
                            'voucher_payments.created_at'
                        )->groupBy('voucher_payments.id');

            if(isset($request->start_date) && $request->start_date != '' ){
                $voucher_payment->whereBetween('voucher_payments.created_at', [Carbon::parse($request->start_date), Carbon::parse($request->end_date)->addDay()]);

            }
            if(isset($request->order_number) && $request->order_number != '' ){
                $order_number = explode('/',$request->order_number);
                $order_number = array_reverse($order_number)[0];
                $voucher_payment->where('voucher_payments.id', $order_number);
            }

            if(isset($request->start_date_update) && $request->start_date_update != '' ){
                $voucher_payment->whereBetween('updated_at', [Carbon::parse($request->start_date_update), Carbon::parse($request->end_date_update)->addDay()]);

            }

            if(isset($request->payment_status) && $request->payment_status != '' ){

                if($request->payment_status == 1){
                    $voucher_payment ->where('is_partial_paid','!=',1);
                    $voucher_payment->where('is_paid',$request->payment_status)->whereNull('refunded_at');
                }elseif($request->payment_status == 0){
                    $voucher_payment->where('is_paid',$request->payment_status)->whereNull('refunded_at');
                }elseif($request->payment_status == 3){
                    $voucher_payment->where('is_partial_paid',1);
                }
            }

            if(isset($request->product_id) && $request->product_id != null && $request->product_id != ''){
                $voucher_payment->whereHas('details.voucher_order.product', function($query) use($request){
                    $query->where('product_id', $request->product_id);
                    if(isset($request->variation_id) && $request->variation_id != null && $request->variation_id != ''){
                        $query->where('variation_id', $request->variation_id);
                    }
                });
            }

            if(isset($request->manufacturer_id) && $request->manufacturer_id != null && $request->manufacturer_id != ''){
                $voucher_payment->whereHas('details.voucher_order.product.manufacturer', function($query) use($request){
                    $query->where('manufacturer_id', $request->manufacturer_id);
                });
            }

            if(isset($request->name_email) && $request->name_email != null && $request->name_email != ''){
                $voucher_payment->whereHas('details.voucher_order.reseller', function($query) use($request){
                    $query->where('distributor_name','LIKE','%'.$request->name_email.'%');
                    $query->orWhere('distributor_email','LIKE','%'.$request->name_email.'%');
                });
            }
            if(isset($request->invoice_payment) && $request->invoice_payment != null && $request->invoice_payment != ''){


                $voucher_payment->whereHas('voucher_payment_references', function($query) use($request){
                    $query->where('method', $request->invoice_payment);
                });
            }
            $data       = $voucher_payment->orderBy('voucher_payments.id','desc');

            $datatable = Datatables::of($data);
            $datatable->addColumn('invoice_no', function ($row) {
                return 'TIM/'.\Carbon\Carbon::parse($row->created_at)->format('Y').'/'.str_pad($row->id, 3, '0', STR_PAD_LEFT);
            });
            $datatable->addColumn('statuss', function ($row) {
                $html = __('Un-paid');
                if($row->is_paid == 1){
                    $html = $row->is_partial_paid == 1 ? __('Partially Paid') : __('Paid') ;
                }
                $html = $row->refunded_at == null ? $html : __('Refunded At').' '.Carbon::parse($row->refunded_at)->format('d-M-Y');
                return $html;
            });
            $datatable->addColumn('distributor', function ($row) {
                // $reseller = $row->details[0]->voucher_order->reseller ;
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
                $html = currency_format($row->total_amount,$row->currency_symbol,$row->currency);
                return $html;
            });
            $datatable->addColumn('created_at', function ($row) {
                return date('d-M-Y', strtotime($row->created_at));
            });
            $datatable->addColumn('updated_at', function ($row) {
                return $row->updated_at != null ? date('d-M-Y', strtotime($row->updated_at)) : '';
                return date('d-M-Y', strtotime($row->updated_at));
            });

            $datatable = $datatable->rawColumns(['order_no','payment_status','created_at']);
            return $datatable->make(true);
        }
        $productList = Products::with( 'generalInformation', 'customer_taxes', 'variations', 'variations.variation_details' )->get();
       
        return view('admin.reports.distributor-voucher-payment');
    }


    public function marketPlaceOrdersList(Request $request)
    {
        return view('admin.reports.marketplace_orders');
    }
    public function kssLicenses(Request $request)
    {
        return view('admin.reports.kss_licenses');
    }
    public function kssVouchers(Request $request)
    {
        return view('admin.reports.kss_vouchers');
    }
    public function distributors(Request $request){
        if ($request->ajax()) {
            $data_query = VoucherOrder::whereNotNull('distributor_id')->orderBy('created_at','desc');

            if(isset($request->country_id) && $request->country_id != '' && $request->country_id != null){
                $data_query->whereHas('contact_country', function ($query) use($request){
                    $query->where('country_id',$request->country_id);
                });
            }
            if(isset($request->action_status) && $request->action_status != '' && $request->action_status != null){
                $data_query->where('is_active',$request->action_status);

            }
            if(isset($request->status)){
                $data_query->where('status', $request->status);
            }
            if(isset($request->currency)){
                $data_query->where('currency', $request->currency);
            }
            if(isset($request->name_email)){
                $data_query->whereHas('distributor', function($query) use($request){
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
            $data = $data_query->get();

            $datatable = Datatables::of($data);
            $datatable->addColumn('ids', function ($row) {
                return '';
            });
            
            $datatable->addColumn('action', function ($row)  use($request) {
                $html = '' ;
                if (auth()->user()->hasAnyPermission(['Vouchers Listing','Vouchers Payment','Download Vouchers']))
                {
                    if(auth()->user()->can('Vouchers Listing')) {
                        if(isset($request->voucher_code)){
                            $html = '<a title='.__('Vouchers').'  target="_blank" class="" href="'.route("admin.voucher.order.vouchers", Hashids::encode($row->id)).'?code='.$request->voucher_code.'">';
                        }else{
                            $html = '<a title='.__('Vouchers').' target="_blank" class="" href="'.route("admin.voucher.order.vouchers", Hashids::encode($row->id)).'">';
                        }
                            $html .= '<button class="btn btn-secondary btn-sm">';
                                $html .= '<i class="fa fa-gift" aria-hidden="true"></i>';
                            $html .= '</button>';
                        $html .= '</a>&nbsp;';

                    }
                    if(auth()->user()->can('Download Vouchers')) {
                        $html .= '<a  target="_blank" title='.__('Download').' href="'.route('admin.voucher.order.vouchers.export', Hashids::encode($row->id)).'" class="">';
                            $html .= '<button class="btn btn-success btn-sm">';
                                $html .= '<i class="fa fa-download" aria-hidden="true"></i>';
                            $html .= '</button>';
                        $html .= '</a>&nbsp;';
                    }
                }
                return $html;
            });
            $datatable->addColumn('order_id', function ($row) {
                return str_replace(' ','',$row->distributor->name).'-'.str_pad($row->id,3,'0',STR_PAD_LEFT).'-'.date('dmY',strtotime($row->created_at));
            });
            $datatable->addColumn('distributor', function ($row) {
                return @$row->distributor->name.'<br>'.@$row->distributor->email;
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
                return currency_format($row->total_payable,$row->currency_symbol,$row->currency);
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

            $datatable = $datatable->rawColumns(['product_name','distributor','active_status','active_status_action','statuss','action','status_action']);
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
        $data['products_voucher_order'] = Products::withCount('variations')->where('is_active', 1)->get();
        
        return view('admin.reports.voucher-orders-distributors', $data);

    }
}
