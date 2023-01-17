<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Quotation;
use App\Models\Invoice;
use App\Models\QuotationOrderLine;
use App\Models\QuotationOrderLineTax;
use App\Models\Contact;
use \Carbon\Carbon;
use Carbon\CarbonPeriod;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Auth;
use DB;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware(['auth','verified']);
    }
    /**
     * Show Admin Dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // dd($_SERVER);
        return view('admin.dashboard');
    }


    /**
     * sales_dashboard
     *
     * @return void
     */
    public function sales_dashboard(Request $request)
    {
        if(!auth()->user()->can('Sales Dashboard'))
        access_denied();
        // Count Data
        $data = $this->salesData($request);
        if ($request->ajax()) {
            return $data;
        }
        return view('admin.sales.dashboard', $data);
    }
    public function salesData($request)
    {
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

        $currency = $request->currency ? $request->currency : (isset($data['currencies']['€']) ? $data['currencies']['€'] : '');
        $data['quotation_count'] = Quotation::where(function($query){
            $query->where('status','!=',1);
            $query->where('status','!=',2);
        })->where('currency', $currency)->get()->count();
        $data['salesorders_count'] = Quotation::where(function($query){
            $query->where('status',  1 );
            $query->orWhere('status',  2 );
        })->where('currency', $currency)->get()->count();
        $data['customer_count'] = Contact::where('status',1)->where('type',2)->get()->count();
        $data['reseller_count'] = Contact::where('status',1)->where('type',3)->get()->count();
        $data['guest_count'] = Contact::where('type',4)->get()->count();
       
        $data['order_to_invoice'] = Quotation::where('currency', $currency)->whereDoesntHave('invoices')->with(
                    'customer',
                    'order_lines.product',
                    'order_lines.variation',
                    'order_lines.quotation_taxes',
                    'optional_products',
                    'optional_products.product',
                    'optional_products.variation',
                    'other_info',
                    'other_info.sales_person',
                    'other_info.sales_team'
                    )->get()->count();

        // Customers Graph
        $data['customers'] = Contact::join('contact_countries','contact_countries.id', 'contacts.country_id')
            ->where('contacts.status',1)->pluck('contact_countries.country_code')->toArray();

            // Last 10 days sales
        $data['last_10_sales'] = Quotation::where(function($query){
            $query->where('status',  1 );
            $query->orWhere('status',  2 );
        })->where('currency', $currency)->orderBy('created_at','desc')->take(10)->get();
        foreach($data['last_10_sales'] as $sale){
            $sale->orderid = 'S'.str_pad($sale->id, 5, '0', STR_PAD_LEFT);
            $sale->date = \Carbon\Carbon::parse($sale->created_at)->format('Y/m/d');
            $sale->customer_name = $sale->customer->name;
            $sale->total = currency_format($sale->total,'','',1);
            // $sale->total = $sale->total;
        }
        $sale_quotations = Quotation::where('currency', $currency)->get();
        $data['total_taxes'] = 0;
        $data['all_quotation_total'] = 0;
        $data['all_quotation_untaxed_total'] = 0;
        foreach($sale_quotations as $s_quotation){
            foreach($s_quotation->order_lines as $o){
                $subtotal = $o->qty * $o->unit_price * $s_quotation->exchange_rate;
                $data['all_quotation_total'] += $subtotal;
                $data['all_quotation_untaxed_total'] += $subtotal;
                $taxes = QuotationOrderLineTax::with('tax')->where('quotation_order_line_id',$o->id)->get();

                foreach($taxes as $o_tax)
                {
                    if($o_tax->tax != null){
                        switch($o_tax->tax->computation)
                        {
                            case 0:
                                $data['total_taxes'] += $o_tax->tax->amount;
                                $data['all_quotation_total'] += $o_tax->tax->amount;
                                break;
                            case 1:
                                $data['total_taxes'] += $subtotal * $o_tax->tax->amount / 100;
                                $data['all_quotation_total'] += $subtotal * $o_tax->tax->amount / 100;
                                break;
                        }
                    }
                }
                $data['total_taxes'] += $subtotal * $s_quotation->vat_percentage / 100;
                $data['all_quotation_total'] += $subtotal * $s_quotation->vat_percentage / 100;
            }
        }

        $data['total_taxes'] = currency_format($data['total_taxes'],'','',1);
        $data['all_quotation_total'] = currency_format($data['all_quotation_total'],'','',1);
        $data['all_quotation_untaxed_total'] = currency_format($data['all_quotation_untaxed_total'],'','',1);

        $data['total_invoiced_amount'] = Invoice::where('refunded_at',null)->whereHas('quotation', function($query) use($currency){
                                            $query->where('currency', $currency);
                                        })->first()
                                        ?
                                        currency_format(Invoice::where('refunded_at',null)->whereHas('quotation', function($query) use($currency){
                                            $query->where('currency', $currency);
                                        })->first()->totalinvoicedamountcurrency,'','',1)
                                        :
                                        '0.00';
        $data['total_refunded_amount'] = Invoice::where('refunded_at','!=',null)->whereHas('quotation', function($query) use($currency){
                                            $query->where('currency', $currency);
                                        })->first()
                                        ?
                                        currency_format(Invoice::where('refunded_at','!=',null)->whereHas('quotation', function($query) use($currency){
                                            $query->where('currency', $currency);
                                        })->first()->totalrefundedamountcurrency,'','',1)
                                        :
                                        '0.00';
        $fromDate = Carbon::now()->subMonth(5)->startOfMonth()->toDateString();
        $tillDate = Carbon::now()->endOfMonth()->toDateString();
        $period = CarbonPeriod::create(Carbon::now()->subMonth(4)->startOfMonth()->format('Y-m-d'), Carbon::now()->endOfMonth()->format('Y-m-d'));
        $dates = array();
        foreach ($period as $date)
        {
            if(!in_array($date->format('m-Y'), $dates))
            {
                array_push($dates, $date->format('m-Y'));
            }
        }
        $data['monthly_sales'] = Quotation::select([
                DB::raw('count(id) as `sales`'),
                DB::raw("DATE_FORMAT(created_at, '%m-%Y') new_date"),
                DB::raw("DATE_FORMAT(created_at, '%d-%m-%Y') full_date"),
                DB::raw('YEAR(created_at) year,MONTH(created_at) month')
            ])
            ->where('currency', $currency)->whereBetween('created_at',[$fromDate,$tillDate])
            ->groupBy('month','year')->orderBy('new_date','asc')->get()->toArray();

        $data['monthly_sales_dougnut'] = array();
        foreach($data['monthly_sales'] as $ind => $m_sale)
        {
            $chart_data[$m_sale['new_date']] = $m_sale['sales'];
        }
        $temp_monthly_data_array = array();
        foreach($dates as $date)
        {
            $item = (object)array();
            if(isset($chart_data[$date]))
            {
                $item->new_date = $date;
                $item->sales = $chart_data[$date];
            }
            else
            {
                $item->new_date = $date;
                $item->sales = 0;
            }
            array_push($temp_monthly_data_array, $item);
        }
        $data['monthly_sales'] = $temp_monthly_data_array;
        foreach($data['monthly_sales'] as $m_sale)
        {
            array_push($data['monthly_sales_dougnut'], (object)array('label'=>$m_sale->new_date, 'value'=>$m_sale->sales));
        }
        // Last 5 Months Sale End
        // Customer Map Graph Start
        $types = array(1,2);
        $customers = Contact::with('contact_countries','contact_addresses','contact_addresses.contact_countries')->whereIn('type', $types)->get();
        $data['customer_map_stats'] = array();
        foreach($customers as $customer)
        {
            if(isset($customer->contact_countries) && $customer->contact_countries)
            {

                $latlng = array(floatval($customer->contact_countries->latitude),floatval($customer->contact_countries->longitude));
                $marker = (object)array();
                array_push($data['customer_map_stats'], (object)array('latLng'=>$latlng,'name'=>$customer->contact_countries->name));
            }
            if($customer->contact_addresses != null)
            {
                foreach($customer->contact_addresses as $contact_address)
                {
                    if(isset($contact_address->contact_countries) && $contact_address->contact_countries)
                    {
                        $latlng = array(floatval($contact_address->contact_countries->latitude),floatval($contact_address->contact_countries->longitude));
                        $marker = (object)array();
                        array_push($data['customer_map_stats'], (object)array('latLng'=>$latlng,'name'=>$customer->contact_countries->name));
                    }
                }
            }
        }

        // Top 10 Selling Products
        $data['top_10_sale_products'] = QuotationOrderLine::join('quotations','quotations.id','quotation_order_lines.quotation_id')
                                        ->join('products','products.id','quotation_order_lines.product_id')
                                        ->select(
                                            DB::raw('sum(quotation_order_lines.qty) as sales'),
                                            DB::raw("CONCAT(sum(quotation_order_lines.qty),' - ',products.product_name) AS product_name")
                                        )
                                        ->where('quotations.currency', $currency)
                                        ->groupBy('products.product_name')
                                        ->orderBy('sales','desc')
                                        ->get()->toArray();

        $data['top_10_sale_products_sale_count'] = QuotationOrderLine::join('quotations','quotations.id','quotation_order_lines.quotation_id')
                                        ->join('products','products.id','quotation_order_lines.product_id')
                                        ->where('quotations.currency', $currency)
                                        ->select(
                                            DB::raw('sum(quotation_order_lines.qty) as sales')
                                        )
                                        ->get()->toArray();

        return $data;
    }


    public function generateSalesReportExcel(Request $request)
    {
        $data = $this->salesData($request);

        $sheet_array[] = array();
        $sheet_array[] = [
            'Sales Orders Report',
        ];
        $sheet_array[] = ['Metrics', 'Value'];
        $sheet_array[] = [
            'Metrics' => 'Quotations' ,
            'Value' => $data['quotation_count']
        ];
        $sheet_array[] = [
            'Metrics' => 'Sales Orders' ,
            'Value' => $data['salesorders_count']
        ];
        $sheet_array[] = [
            'Metrics' => 'Orders to Invoice' ,
            'Value' => $data['order_to_invoice']
        ];
        $sheet_array[] = [
            'Metrics' => 'Resellers' ,
            'Value' => $data['reseller_count']
        ];
        $sheet_array[] = [
            'Metrics' => 'Guests' ,
            'Value' => $data['guest_count']
        ];
        $sheet_array[] = [
            'Metrics' => 'Customers' ,
            'Value' => $data['customer_count']
        ];
        $sheet_array[] = [
            'Metrics' => 'Taxes' ,
            'Value' => $data['total_taxes'].' '.$request->currency
        ];
        $sheet_array[] = [
            'Metrics' => 'Total Amount' ,
            'Value' => $data['all_quotation_total'].' '.$request->currency
        ];
        $sheet_array[] = [
            'Metrics' => 'Untaxed Total Amount' ,
            'Value' => $data['all_quotation_untaxed_total'].' '.$request->currency
        ];
        $sheet_array[] = [
            'Metrics' => 'Total Amount Invoiced' ,
            'Value' => $data['total_invoiced_amount'].' '.$request->currency
        ];
        $sheet_array[] = [
            'Metrics' => 'Total Refunded Amount' ,
            'Value' => $data['total_refunded_amount'].' '.$request->currency
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
        $old_file = public_path().'/storage/sales/SalesReport.xlsx';
        if (file_exists($old_file)) {
            unlink($old_file);
        }
        ob_clean();
        $writer = new Xlsx($spreadsheet);
        // header('Content-Type: application/vnd.ms-excel');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="SalesReport.xlsx"');
        $writer->save('php://output');
        return public_path('storage/sales/SalesReport.xlsx');
    }
}
