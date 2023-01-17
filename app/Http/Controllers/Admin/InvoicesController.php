<?php

namespace App\Http\Controllers\Admin;

use Alert;
use App\Http\Controllers\Controller;
use App\Models\Products;
use App\Models\Quotation;
use App\Models\QuotationOrderLine;
use App\Models\Tax;
use App\Models\Invoice;
use App\Models\License;
use App\Models\EmailTemplate;
use App\Models\InvoiceOrderLine;
use App\Models\SalesSettings;
use App\Models\InvoicePaymentHistory;
use Hashids;
use Yajra\DataTables\DataTables;
use PDF;
use File;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Traits\PaymentTrait;
class InvoicesController extends Controller
{
    use PaymentTrait;
    /**
     * Display a listing of the resource.
     *
     */
    public function index($quotation_id, Request $request)
    {
        if(!auth()->user()->can('Quotation Invoices'))
        access_denied();

        $data = [];
        $quotation_id = Hashids::decode($quotation_id)[0];
        if ($request->ajax()) {
            $data = Invoice::with(
                                'quotation',
                                'invoice_order_lines'
                            )
                            ->where('quotation_id', $quotation_id)->orderBy('created_at','desc')
                            ->get();
            $datatable = Datatables::of($data);
            $datatable->addColumn('invoicenumber', function ($row) {
                $text = '/';
                // if($row->status == 1 ){
                    // $text = 'INV/'. str_pad($row->id, 5, '0', STR_PAD_LEFT);
                    $text = 'TIM/'.\Carbon\Carbon::parse($row->created_at)->format('Y').'/'.str_pad($row->id, 3, '0', STR_PAD_LEFT) ;
                // }
                return auth()->user()->can('View Quotation Invoice') ? '<a href="' .route('admin.quotation.invoice.show',Hashids::encode($row->id)). '">'.$text.'</a>' : $text;
            });
            $datatable->editColumn('customer', function ($row) {
                return $row->quotation->customer->name;
            });
            $datatable->editColumn('invoicedate', function ($row) {
                return \Carbon\Carbon::parse($row->created_at)->format('d-M-Y');
            });
            $datatable->editColumn('duedate', function ($row) {
                return \Carbon\Carbon::parse($row->created_at)->format('d-M-Y');
            });
            $datatable->editColumn('total', function ($row) {
                // return  number_format($row->total* $row->quotation->exchange_rate,2 ).' '.$row->quotation->currency;
                return  currency_format($row->total* $row->quotation->exchange_rate,$row->quotation->currency_symbol,$row->quotation->currency);
            });
            $datatable->editColumn('taxexcluded', function ($row) {
                // return  number_format(($row->total - $row->totaltax) * $row->quotation->exchange_rate,2 ).' '.$row->quotation->currency;
                return  currency_format(($row->total- $row->totaltax)* $row->quotation->exchange_rate,$row->quotation->currency_symbol,$row->quotation->currency);
            });
            $datatable->editColumn('status', function ($row) {
                    switch($row->status){
                        case(0):
                            return '<span class="tagged quote">'.__("Draft").'</span>';
                            break;
                        case(1):
                            return '<span class="tagged success">'.__("Confirmed").'</span>';
                            break;
                        case(2):
                            return '<span class="tagged danger">'.__("Cancelled").'</span>';
                            break;
                    }
            });
            $datatable->editColumn('paymentstatus', function ($row) {
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
            $datatable->addColumn('action', function ($row) {
                $actions = '<div style="display:inline-flex">';
                $actions .= auth()->user()->can('View Quotation Invoice') ? '&nbsp;<a class="btn btn-warning btn-icon" href="' . route('admin.quotation.invoice.show',Hashids::encode($row->id)) .'" title='.__('View').'><i class="fa fa-eye"></i></a>' : '';
                $actions .= '</div>';
                return $actions;
            });
            $datatable = $datatable->rawColumns(['paymentstatus','status','action','invoicenumber']);
            return $datatable->make(true);
        }
        $data['quotation_id'] = $quotation_id;

        $data['ajaxroute'] = route('admin.quotation.invoice.index',Hashids::encode($quotation_id));
        return view('admin.sales.invoices.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if(!auth()->user()->can('View Quotation Invoice'))
        access_denied();

        $data = [];

        $id = Hashids::decode($id)[0];
        $data['model'] = Invoice::with(
            'invoice_order_lines',
            'invoice_order_lines.quotation_order_line',
            'invoice_order_lines.quotation_order_line.product',
            'invoice_order_lines.quotation_order_line.variation',
            'invoice_order_lines.quotation_order_line.quotation_taxes',
            'invoice_order_lines.quotation_order_line.quotation_taxes.tax',
            'quotation',
            'quotation.customer',
            'quotation.customer.contact_addresses',
            'quotation.customer.contact_addresses.contact_countries',
            'quotation.pricelist',
            'quotation.other_info',
            'quotation.other_info.sales_person',
            'quotation.other_info.sales_team',
            'quotation.other_info.tags',
            'quotation.payment_term_detail',
            'quotation.invoice_address_detail',
            'quotation.delivery_address_detail',

            )->where('id', $id)->first();
        // $html = view('admin.sales.pdf.invoice')->with($data);
        // return $data['model']->invoice_pdf_link;
       return view('admin.sales.invoices.invoice_detail')->with($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

    }
    /**
     * Create Invoice of Quotation
     *
     *
     */
    public function create_invoice($quotation_id, Request $request)
    {
        if(!auth()->user()->can('Create Quotation Invoice'))
        access_denied();

        $quotation_id = Hashids::decode($quotation_id)[0];
        $quotation = Quotation::with('order_lines','order_lines.quotation_taxes','order_lines.product','order_lines.product.sales','order_lines.variation')
                ->where('id', $quotation_id)->first();


        // Make a new Invoice for the quotation
        $new_invoice = new Invoice;
            $new_invoice->quotation_id = $quotation_id;
            $new_invoice->status = 0;   // Draft
            $new_invoice->is_paid = 0;   // Not Paid
            $new_invoice->is_partially_paid = 0;   // Not Paid
            $new_invoice->invoice_total = 0;   // Will be updated below
            $new_invoice->amount_paid = 0;   // Will be updated on register payment
        $new_invoice->save();

        $invoice_total = 0;
        // Loop through all quotation Order Lines
        foreach($quotation->order_lines as $order_line){
            // $quotation_order_line = QuotationOrderLine::where('id', $order_line->id)->first();
            // If order line has product
            if( $order_line->product_id != null && $order_line->qty > $order_line->invoiced_qty ){
                // Invoice Order Line Total
                $invoice_order_line_total = $order_line->invoicetotal;
                // Total Invoice Total
                $invoice_total += $invoice_order_line_total ;
                // Product Quantity
                $qty = 0;

                // If the invoicing policy of product is ordered quantities
                if($order_line->product->sales->invoice_policy == 0){
                    $qty = $order_line->qty;
                    // Update the Invoiced Quantity of the Quotation Order Line
                    QuotationOrderLine::where('id', $order_line->id)->update(['invoiced_qty'=>$qty]);
                }
                // If the invoicing policy of product is delivered quantities
                elseif($order_line->product->sales->invoice_policy == 1){
                    // Delivered Quantity - Invoiced Quantity
                    $qty = $order_line->delivered_qty - $order_line->invoiced_qty;
                    // Update the Invoiced Quantity of the Quotation Order Line
                    QuotationOrderLine::where('id', $order_line->id)->update(['invoiced_qty'=>$order_line->delivered_qty]);
                }
                // Create new invoice order line attached with the newly created invoice
                $new_invoice_order_line = new InvoiceOrderLine;
                    $new_invoice_order_line->invoice_id = $new_invoice->id;     // id of the invoice created
                    $new_invoice_order_line->quotation_order_line_id = $order_line->id; // id of quotation order line
                    $new_invoice_order_line->invoiced_qty = $qty;   // Quantity of products invoiced
                    $new_invoice_order_line->amount = $invoice_order_line_total;    //  Total Amount of the Product * quantity
                $new_invoice_order_line->save();

            }
        }
        if($invoice_total > 0){
            // Update the Invoice Total in the invoice table
            $new_invoice->invoice_total = $invoice_total;
            $new_invoice->save();

            Alert::success('Success', 'Invoice created successfully!')->persistent('Close')->autoclose(5000);
            return redirect()->route('admin.quotation.invoice.index',Hashids::encode($quotation_id));
        }else{
            Invoice::where('id',$new_invoice->id)->delete();
            InvoiceOrderLine::where('invoice_id',$new_invoice->id)->delete();

            Alert::warning('Sorry', 'Nothing to Invoice!')->persistent('Close')->autoclose(5000);
            return redirect()->back();
        }



    }

    /**
     * Change Invoice Status
     * @param $invoice_id
     * @param $status
     *
     */
    public function change_invoice_status($invoice_id, $status)
    {
        $invoice_id = Hashids::decode($invoice_id)[0];
        if($status == 0 || $status == 1 || $status == 2){
            switch ($status) {
                case 0:
                    $invoice = Invoice::where('id', $invoice_id)->first();

                    $order_number = "S".str_pad($invoice->quotation_id, 5, '0', STR_PAD_LEFT);
                    $name = $invoice->quotation->customer->name;
                    $email = $invoice->quotation->customer->email;
                    $email_template = EmailTemplate::where('type','order_invoice_draft')->first();
                    $lang = app()->getLocale();
                    $email_template = transformEmailTemplateModel($email_template,$lang);
                    $content = $email_template['content'];
                    $subject = $email_template['subject'];
                    $search = array("{{name}}","{{order_number}}","{{app_name}}");
                    $replace = array($name,$order_number,env('APP_NAME'));
                    $content = str_replace($search,$replace,$content);
                    // echo($content);
                    dispatch(new \App\Jobs\SendOrderDraftEmailJob($email,$subject,$content,));

                    Invoice::where('id', $invoice_id)->update(["is_paid" => 0,"is_partially_paid" => 0, 'amount_paid' => 0]);
                    Alert::success('Success', 'Invoice moved to draft')->persistent('Close')->autoclose(5000);
                    break;
                case 1:
                    $invoice = Invoice::where('id', $invoice_id)->first();

                    $order_number = "S".str_pad($invoice->quotation_id, 5, '0', STR_PAD_LEFT);
                    $name = $invoice->quotation->customer->name;
                    $email = $invoice->quotation->customer->email;
                    $link = $this->generate_payment_link($invoice->quotation_id);
                    if($link['success'] == false || !$invoice){
                        Alert::success('Error', 'Something went wrong. Try again in a moment')->persistent('Close')->autoclose(5000);
                        return redirect()->back();
                    }
                    $sales_settings = SalesSettings::where('variable_name','orders_online_payment')->first();
                    $online_payment = $sales_settings ? $sales_settings->variable_value : 0;

                    $link = $link['link'];
                    $amount = currency_format($invoice->total* $invoice->quotation->exchange_rate,$invoice->quotation->currency_symbol,$invoice->quotation->currency);
                    $quotation_pdf = str_replace('https:','http:',$invoice->invoice_pdf);
                    $email_template = '';
                    if($online_payment == 1){
                        $email_template = EmailTemplate::where('type','order_invoice_generated')->first();
                    }else{
                        $email_template = EmailTemplate::where('type','order_invoice_generated_offline_payment')->first();
                    }
                    $lang = app()->getLocale();
                    $email_template = transformEmailTemplateModel($email_template,$lang);
                    $content = $email_template['content'];
                    $subject = $email_template['subject'];
                    $search = array("{{name}}","{{order_number}}","{{link}}","{{contact_link}}","{{amount}}","{{app_name}}");
                    $replace = array($name,$order_number,$link,route('frontside.contact.index'),$amount,env('APP_NAME'));
                    $content = str_replace($search,$replace,$content);
                    dispatch(new \App\Jobs\SendOrderInvoiceEmailJob($email,$subject,$content,$quotation_pdf));
                    Alert::success('Success', 'Invoice confirmed successfully!')->persistent('Close')->autoclose(5000);
                    break;
                case 2:
                    Alert::success('Success', 'Invoice cancelled successfully!')->persistent('Close')->autoclose(5000);
                    break;
                }
                Invoice::where('id', $invoice_id)->update(["status" => $status]);
        }else{
            Alert::success('Error', 'Cannot change the status. Status Invalid')->persistent('Close')->autoclose(5000);
        }
        return redirect()->route('admin.quotation.invoice.show',Hashids::encode($invoice_id));
    }

    /**
     * Refund Payment
     *
     */
    public function refund_payment(Request $request)
    {
        $invoice_id = Hashids::decode($request->invoice_id)[0];
        $invoice = Invoice::where('id', $invoice_id)->first();
        // $this->cancelPaymentLink($invoice->quotation->transaction_id);

        if($invoice)
        {
            $invoice->refund_reason = $request->refund_reason;
            $invoice->refunded_at = Carbon::now();
            $invoice->save();

            $email_template = EmailTemplate::where('type','invoice_amount_refunded')->first();

            $order_number = "S".str_pad($invoice->quotation_id, 5, '0', STR_PAD_LEFT);
            $name = $invoice->quotation->customer->name;
            $email = $invoice->quotation->customer->email;

            $lang = app()->getLocale();
            $email_template = transformEmailTemplateModel($email_template,$lang);
            $content = $email_template['content'];
            $subject = $email_template['subject'];
            $amount = currency_format(@$invoice->amount_paid,$invoice->quotation->currency_symbol,$invoice->quotation->currency);
            $quotation_pdf = str_replace('https:','http:',$invoice->invoice_pdf);
            $search = array(
                "{{name}}","{{order_number}}","{{app_name}}","{{amount}}"
            );
            $replace = array(
                $name,$order_number,env('APP_NAME'),$amount);

            $content = str_replace($search,$replace,$content);
            dispatch(new \App\Jobs\SendOrderInvoiceEmailJob($email,$subject,$content,$quotation_pdf));
            // dispatch(new \App\Jobs\SendAmountRefundedEmailJob($email,$subject,$content));

            Alert::success('Success', 'Amount refunded successfully')->persistent('Close')->autoclose(5000);
        }else{
            Alert::success('Error', 'Invalid invoice Reference')->persistent('Close')->autoclose(5000);
        }
        return redirect()->back();
    }
    /**
     * Register Payment
     *
     */
    public function register_payment(Request $request)
    {
        $invoice_id = Hashids::decode($request->invoice_id)[0];
        $amount = $request->registered_amount;
        $invoice = Invoice::where('id', $invoice_id)->first();
        // $this->cancelPaymentLink($invoice->quotation->transaction_id);

        if($invoice)
        {
            $generate_vouchers = ($invoice->is_partially_paid == 0 && $invoice->is_paid == 0) ? true : false;
            // If the amount paid is less than the amount registered mark a partial payment
            $invoice->is_partially_paid = ( (currency_format( ( $amount + $invoice->amount_paid ),'','',1 ) )  < currency_format( $invoice->total * $invoice->quotation->exchange_rate,'','',1 ) ) ? 1 : 0;
            $invoice->is_paid = 1;
            $invoice->amount_paid = $invoice->amount_paid + $amount;
            $invoice->save();

            $order_number = "S".str_pad($invoice->quotation_id, 5, '0', STR_PAD_LEFT);
            $name = $invoice->quotation->customer->name;
            $email = $invoice->quotation->customer->email;

            $amount_registered  = currency_format($amount,$invoice->quotation->currency_symbol,$invoice->quotation->currency);
            $total_registered  = currency_format($invoice->amount_paid,$invoice->quotation->currency_symbol,$invoice->quotation->currency);
            // $total_invoice_total  = $invoice->quotation->currency_symbol.''.currency_format($invoice->total* $invoice->quotation->exchange_rate,'','',1).' '.$invoice->quotation->currency;
            $total_invoice_total  = currency_format($invoice->total* $invoice->quotation->exchange_rate,$invoice->quotation->currency_symbol,$invoice->quotation->currency);
            $pending_amount  = ($invoice->total * $invoice->quotation->exchange_rate) - $invoice->amount_paid;
            $pending_amount  = currency_format($pending_amount,$invoice->quotation->currency_symbol,$invoice->quotation->currency);

            $status  = $invoice->is_partially_paid == 1 ? __('Partially Paid') : ($invoice->is_paid == 1 ? __('Fully Paid') : '') ;
            $quotation_pdf = str_replace('https:','http:',$invoice->invoice_pdf);

            $payment_history = new InvoicePaymentHistory;
            $payment_history->invoice_id = $invoice->id;
            $payment_history->method = $request->method;
            $payment_history->amount = $request->registered_amount;
            $payment_history->save();

            $email_template = EmailTemplate::where('type','invoice_amount_registered')->first();

            $lang = app()->getLocale();
            $email_template = transformEmailTemplateModel($email_template,$lang);
            $content = $email_template['content'];
            $subject = $email_template['subject'];

            $search = array(
                "{{name}}","{{order_number}}","{{amount_registered}}","{{total_registered}}","{{total_invoice_total}}","{{pending_amount}}","{{app_name}}","{{status}}"
            );
            $replace = array(
                $name,$order_number,$amount_registered ,$total_registered ,$total_invoice_total ,$pending_amount,env('APP_NAME'),$status);

            $content = str_replace($search,$replace,$content);
            dispatch(new \App\Jobs\SendAmountRegisteredEmailJob($email,$subject,$content,$quotation_pdf));

            if($generate_vouchers){
                $vouchers_attached = generateVouchers($invoice->quotation_id);
            }
            Alert::success('Success', 'Amount registered successfully')->persistent('Close')->autoclose(5000);
        }else{
            Alert::success('Error', 'Invalid invoice')->persistent('Close')->autoclose(5000);
        }
        return redirect()->back();
    }

    public function generate_payment_link($quotation_id){
        $quotation = Quotation::where('id', $quotation_id)->first();
        // $payment = $this->generatePaymentDetails($quotation, route('admin.quotation.payment.redirect', Hashids::encode($quotation->id)));
        $payment = $this->generatePaymentDetails($quotation, route('frontside.payment.redirect', Hashids::encode($quotation->id)));
        if($payment['success']){
            $quotation->transaction_id = $payment['payment']->id;
            $quotation->save();

            return [
                'success'=> true,
                // 'link'=> $payment['payment']->getCheckoutUrl()
                'link'=> route('paymentPay',$payment['payment']->id)
            ];
        }else{
            return [
                'success'=> true,
                'link'=> '',
            ];
        }
    }

    public function showAllInvoices(Request $request){
        if(!auth()->user()->can('Quotation Invoices'))
        access_denied();

        $data = [];
        if ($request->ajax()) {
            $data = Invoice::with(
                                'quotation',
                                'invoice_order_lines'
                            )
                            ->orderBy('created_at','desc')
                            ->get();
            $datatable = Datatables::of($data);
            $datatable->editColumn('invoicenumber', function ($row) {
                $text = '/';
                // if($row->status == 1 ){
                    // $text = 'INV/'. str_pad($row->id, 5, '0', STR_PAD_LEFT);
                    $text = 'TIM/'.\Carbon\Carbon::parse($row->created_at)->format('Y').'/'.str_pad($row->id, 3, '0', STR_PAD_LEFT) ;
                // }
                return auth()->user()->can('View Quotation Invoice') ? '<a href="' .route('admin.quotation.invoice.show',Hashids::encode($row->id)). '">'.$text.'</a>' : $text;
            });
            $datatable->editColumn('customer', function ($row) {
                return $row->quotation->customer->name;
            });
            $datatable->editColumn('invoicedate', function ($row) {
                return \Carbon\Carbon::parse($row->created_at)->format('d-M-Y');
            });
            $datatable->editColumn('duedate', function ($row) {
                return \Carbon\Carbon::parse($row->created_at)->format('d-M-Y');
            });
            $datatable->editColumn('total', function ($row) {
                // return  number_format($row->total* $row->quotation->exchange_rate,2 ).' '.$row->quotation->currency;
                return  currency_format($row->total* $row->quotation->exchange_rate,$row->quotation->currency_symbol,$row->quotation->currency);
            });
            $datatable->editColumn('taxexcluded', function ($row) {
                // return  number_format(($row->total - $row->totaltax) * $row->quotation->exchange_rate,2 ).' '.$row->quotation->currency;
                return  currency_format(($row->total - $row->totaltax)* $row->quotation->exchange_rate,$row->quotation->currency_symbol,$row->quotation->currency);
            });
            $datatable->editColumn('status', function ($row) {
                switch($row->status){
                    case(0):
                        return '<span class="tagged quote">'.__("Draft").'</span>';
                        break;
                    case(1):
                        return '<span class="tagged success">'.__("Confirmed").'</span>';
                        break;
                    case(2):
                        return '<span class="tagged danger">'.__("Cancelled").'</span>';
                        break;
                }
            });
            $datatable->editColumn('paymentstatus', function ($row) {
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
            $datatable->addColumn('action', function ($row) {
                $actions = '<div style="display:inline-flex">';
                $actions .= auth()->user()->can('View Quotation Invoice') ? '&nbsp;<a class="btn btn-warning btn-icon" href="' . route('admin.quotation.invoice.show',Hashids::encode($row->id)) .'" title='.__('View').'><i class="fa fa-eye"></i></a>' : '';
                $actions .= '</div>';
                return $actions;
            });
            $datatable = $datatable->rawColumns(['paymentstatus','status','action','invoicenumber']);
            return $datatable->make(true);
        }


        $data['ajaxroute'] = route('admin.invoices.index');
        return view('admin.sales.invoices.index', $data);
    }

    public function attachLicenses($quotation_id)
    {
        // Get all the quotation order lines
        $all_license_generated = true;
        $quotation_order_lines =  QuotationOrderLine::where( 'quotation_id', $quotation_id)->get();
        // Iterate through all quotation orders lines, check and assign the licenses accordingly
        foreach($quotation_order_lines as $quotation_order_line)
        {
            if($quotation_order_line->product){

                $product_name = $quotation_order_line->product->product_name.' '.@$quotation_order_line->variation->variation_name;
                $licenses[$product_name] = [];
                // Count for the Licenses added for the QuotationOrderLine
                $check_license_count = License::where('quotation_order_line_id',$quotation_order_line->id)->count();
                // Licenses Count for the item added
                $licenses_count = License::where('product_id', $quotation_order_line->product->id);
                if(!empty($quotation_order_line->variation)){
                    $licenses_count->where('variation_id',$quotation_order_line->variation->id);
                }
                $licenses_count->where('status',1);
                $licenses_count->where('is_used',0);
                $licenses_count = $licenses_count->count();
                // If available license count is is less the ordered quantity
                if( $licenses_count < ( $quotation_order_line->qty - $check_license_count ) )
                {
                    $all_license_generated = false;
                }
                else
                {
                    for($i = 0 ; $i < ( $quotation_order_line->qty - $check_license_count ); $i++)
                    {
                        $license = License::where('product_id', $quotation_order_line->product->id);
                        if(!empty($quotation_order_line->variation)){
                            $license ->where('variation_id',$quotation_order_line->variation->id);
                        }
                        $license ->where('status',1);
                        $license ->where('quotation_order_line_id',null);
                        $license ->where('voucher_id',null);
                        $license ->where('is_used',0);
                        $license ->inRandomOrder();
                        $license = $license->first();
                        if($license){
                            $license->quotation_order_line_id = $quotation_order_line->id;
                            $license->is_used = 1;
                            $license->save();
                        }
                    }
                    $licenses[$product_name][] = $quotation_order_line->licenses;

                }
            }
        }
        // Transformation of Order Placed Email Template
        $licenses_arr = [];
        if(count($licenses) > 0) {
            foreach($licenses as $product => $licences) {
                if($licences != []) {
                   $unorderd_list =  '<p style="font-size: 18px; line-height: 25px;"><span style="color: rgb(85, 85, 85); font-family: Segoe, &quot;Segoe UI&quot;, sans-serif; font-size: 17px;"><u><b>'.$product.'</b></u></span></p><ul>';
                        array_push($licenses_arr,$unorderd_list);
                        foreach($licences[0] as $license) {
                            $licenses_list = '<li>'.$license->license_key.'</li>';
                            array_push($licenses_arr,$licenses_list);
                        }
                    $unorderd_list = '</ul>';
                    array_push($licenses_arr,$unorderd_list);
                }
            }
            $licenses_html = implode(' ', $licenses_arr);
        }
        else {
            $licenses_html = "<p>There's no license</p>";
        }
        $name = $quotation_order_lines[0]->quotation->customer->name;
        $email = $quotation_order_lines[0]->quotation->customer->email;
        $order_number = "S".str_pad($quotation_order_lines[0]->quotation->id, 5, '0', STR_PAD_LEFT);
        $email_template = EmailTemplate::where('type','quotation_licenses_email')->first();
        $lang = app()->getLocale();
        $email_template = transformEmailTemplateModel($email_template,$lang);
        $content = $email_template['content'];
        $subject = $email_template['subject'];
        $search = array("{{name}}","{{order_number}}","{{licenses_list}}","{{app_name}}");
        $replace = array($name,$order_number,$licenses_html,env('APP_NAME'));
        $content = str_replace($search,$replace,$content);
        if($licenses_html != ""){
           dispatch(new \App\Jobs\SendLicenseEmailJob($email,$subject,$content));
           return $all_license_generated;
        }
        else {
           return false;
        }

    }



}
