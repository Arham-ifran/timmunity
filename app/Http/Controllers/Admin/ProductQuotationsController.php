<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\Interfaces\PartialViewsRepositoryInterface;
use Alert;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Contact;
use App\Models\ContactAddress;
use App\Models\ContactTag;
use App\Models\Customer;
use App\Models\PaymentTerm;
use App\Models\ProductAttachedAttributeValue;
use App\Models\ProductPriceList;
use App\Models\ProductPriceListRule;
use App\Models\Products;
use App\Models\ProductVariation;
use App\Models\Quotation;
use App\Models\QuotationOptionalProduct;
use App\Models\QuotationOrderLine;
use App\Models\QuotationOrderLineTax;
use App\Models\QuotationOtherInfo;
use App\Models\QuotationOtherInfoTag;
use App\Models\QuotationTextTemplate;
use App\Models\SalesTeam;
use App\Models\Tax;
use App\Models\Followers;
use App\Models\ActivityAttachments;
use App\Models\ActivityMessages;
use App\Models\ActivityLogNotes;
use App\Models\ActivityTypes;
use App\Models\ScheduleActivities;
use App\Models\ContactSalesPurchase;
use App\Models\Invoice;
use App\Models\InvoiceOrderLine;
use App\Models\License;
use App\Models\EmailTemplate;
use App\Models\InvoicePaymentHistory;
use App\Models\QuotationOrderLineVoucher;
use App\Models\ContactCountry;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Jobs\SendOrderEmailJob;
use App\Jobs\PaymentSuccessEmailJob;
use App\Jobs\SendLicenseEmailJob;
use Yajra\DataTables\DataTables;
use Auth;
use Carbon\Carbon;
use Hashids;
use PDF;
use Form;
use File;
use App\Http\Traits\PaymentTrait;
use App\Classes\GrabzItClient;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductQuotationsController extends Controller
{
    use PaymentTrait;
    /**
     * @var PartialViewsRepositories.
     */
    protected $quotationRepository;
    /**
     * PartialViewsRepositories Constructor.
     *
     * @param PartialViewsRepositories $quotationRepository
     */
    public function __construct(PartialViewsRepositoryInterface $quotationRepository)
    {
        $this->quotationRepository = $quotationRepository;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(!auth()->user()->can('Quotations Listing'))
        access_denied();

        $data = [];

        $data = [];
        if ($request->ajax()) {
            $data = Quotation::with(
                'customer',
                'order_lines',
                'order_lines.product',
                'order_lines.variation',
                'order_lines.quotation_taxes',
                'optional_products',
                'optional_products.product',
                'optional_products.variation',
                'other_info',
                'other_info.sales_person',
                'other_info.sales_team'
            )->where(function($query){
                $query->where('status','!=',1);
                $query->where('status','!=',2);
            })->orderBy('created_at','desc');
            if(isset($request->currency)){
                $data->where('currency', $request->currency);
            }
            if(isset($request->quotation_number)){
                $quotation_number = trim($request->quotation_number,"S");
                $quotation_number = trim($request->quotation_number,"s");
                $quotation_number = ltrim( $quotation_number, "0");
                $data->where('id', $quotation_number);
            }
            if(isset($request->country_id)){
                $data->whereHas('customer', function($query) use($request){
                    $query->where('country_id',$request->country_id);
                });
            }
            if(isset($request->customer_name_email)){
                $data->whereHas('customer', function($query) use($request){
                    $query->where('name','LIKE','%'.$request->customer_name_email.'%');
                    $query->orWhere('email','LIKE','%'.$request->customer_name_email.'%');
                });
            }
            if(isset($request->product_id) && $request->product_id != null && $request->product_id != ''){
                $data->whereHas('order_lines', function($query) use($request){
                    $query->where('product_id', $request->product_id);
                    if(isset($request->variation_id) && $request->variation_id != null && $request->variation_id != ''){
                        $query->where('variation_id', $request->variation_id);
                    }
                });
            }
            $data->orderBy('id','asc')->get();
            $data = $data->get();
            foreach($data as $ind => $d){
                // dd($data);
                if(isset($request->invoice_status) && $request->invoice_status != ''){
                    if($request->invoice_status == 4 ){
                        if(!$d->is_refunded ){
                            $data->forget($ind);
                        }
                    }
                    switch ($request->invoice_status) {
                        case 0:
                            # Not Created
                            if(count($d->invoices) > 0)
                            {
                                $data->forget($ind);
                            }
                            break;
                        case 1:
                            # Partially Paid
                            if(currency_format($d->total*$d->exchange_rate,'','',1) == currency_format($d->invoicedamount,'','',1)){
                                $data->forget($ind);
                            }
                            elseif($d->invoicedamount != 0 && currency_format($d->total*$d->exchange_rate,'','',1) > currency_format($d->invoicedamount,'','',1)){

                            }else{
                                $data->forget($ind);
                            }
                            break;
                        case 2:
                            # Fully Paid
                            if(currency_format($d->total*$d->exchange_rate,'','',1) == currency_format($d->invoicedamount,'','',1)){
                            }
                            elseif($d->invoicedamount != 0 && currency_format($d->total*$d->exchange_rate,'','',1) > currency_format($d->invoicedamount,'','',1)){
                                $data->forget($ind);
                            }else{
                                $data->forget($ind);
                            }
                            break;
                        case 3:
                            # Un-Paid
                            if(count($d->invoices) == 0){
                                $data->forget($ind);
                            }
                            if(currency_format($d->total*$d->exchange_rate,'','',1) == currency_format($d->invoicedamount,'','',1)){
                                $data->forget($ind);
                            }
                            elseif($d->invoicedamount != 0 && currency_format($d->total*$d->exchange_rate,'','',1) > currency_format($d->invoicedamount,'','',1)){
                                $data->forget($ind);
                            }
                            break;
                        default:
                            # code...
                            break;
                    }
                }
                if(isset($request->amount)&& $request->amount != ''){
                    if(isset($request->currency) && $request->currency != '')
                    {
                        if( number_format($d->total * $d->exchange_rate,2) != $request->amount )
                        {
                            $data->forget($ind);
                        }
                    }
                    else
                    {

                        if( $d->total != $request->amount )
                        {
                            $data->forget($ind);
                        }
                    }
                }
            }
            $datatable = Datatables::of($data);
            $datatable->addColumn('ordernumber', function ($row) {
                return 'S'.str_pad($row->id, 5, '0', STR_PAD_LEFT);
                return auth()->user()->can('View Quotation') ? '<a href="' .route('admin.quotations.show',Hashids::encode($row->id)). '">S'.str_pad($row->id, 5, '0', STR_PAD_LEFT).'</a>': 'S'.str_pad($row->id, 5, '0', STR_PAD_LEFT);
            });
            $datatable->addColumn('link', function ($row) {
                return route('admin.quotations.show',Hashids::encode($row->id));
            });
            $datatable->addColumn('creationdate', function ($row) {
                return \Carbon\Carbon::parse($row->created_at)->format('d-M-Y');
            });
            $datatable->addColumn('deliverydate', function ($row) {
                return $row->other_info == null ? '' :\Carbon\Carbon::parse($row->other_info->delivery_date)->format('d-M-Y');
            });
            $datatable->addColumn('expecteddate', function ($row) {
                return \Carbon\Carbon::parse($row->created_at)->addDays($row->payment_due_day)->format('d-M-Y');
            });
            $datatable->addColumn('customer', function ($row) {
                return $row->customer->name;
            });
            $datatable->addColumn('salesperson', function ($row) {
                return $row->other_info == null ? '' : @$row->other_info->sales_person->firstname.' '.@$row->other_info->sales_person->lastname;
            });
            $datatable->addColumn('total', function ($row) {
                // return number_format(floatval(str_replace(",","",$row->total)) * floatval($row->exchange_rate), 2).' '.$row->currency_symbol;
                return currency_format(@$row->total * $row->exchange_rate ,$row->currency_symbol,$row->currency);
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
            $datatable->addColumn('invoicestatus', function ($row) {
                if(count($row->invoices) == 0){
                    return '<span class="tagged warning">'.__('Not Created').'</span>';
                }
                if(count($row->invoices) > 0){
                    if($row->is_refunded == true){
                        return '<span class="tagged danger">'.__('Refunded').'</span>';
                    
                    }elseif($row->total == $row->invoicedamount){
                        return '<span class="tagged success">'.__('Fully Invoiced').'</span>';
                    }
                    elseif($row->invoicedamount != 0 && $row->total > $row->invoicedamount){
                        return '<span class="tagged quote">'.__('Partially Invoiced').'</span>';
                    }else{
                        return '<span class="tagged danger">'.__('Not Paid').'</span>';
                    }
                }


            });
            $datatable->addColumn('action', function ($row) {
                $actions = '';
                if (auth()->user()->hasAnyPermission(['Edit Quotation','View Quotation','Delete Quotation']))
                {
                    $actions = '<div style="display:inline-flex">';
                    $actions .= auth()->user()->can('Edit Quotation') ? '&nbsp;<a class="btn btn-primary btn-icon" href="' . url("admin/sales-management/quotations/". Hashids::encode($row->id) . "/edit") . '" title='.__('Edit').'><i class="fa fa-pencil"></i></a>' : '';
                    $actions.= auth()->user()->can('View Quotation') ? '&nbsp;<a class="btn btn-warning btn-icon" href="' . route('admin.quotations.show',Hashids::encode($row->id)) . '" title='.__('View').'><i class="fa fa-eye"></i></a>' : '';
                    if(auth()->user()->can('Delete Quotation')) {
                        $actions .= '&nbsp;' . Form::open([
                            'method' => 'DELETE',
                            'url' => [route('admin.quotations.destroy',Hashids::encode($row->id))],
                            'style' => 'display:inline'
                        ]);

                        $actions .= Form::button('<i class="fa fa-trash fa-fw" title='.__('Delete').'></i>', ['onclick' => 'deleteAlert(this)', 'class' => 'delete-form-btn btn btn-default btn-icon']);
                        $actions .= Form::submit('Delete', ['class' => 'hidden deleteSubmit']);

                        $actions .= Form::close();
                    }
                    $actions .= '</div>';
                }
                return $actions;
            });
            $datatable = $datatable->rawColumns(['ordernumber','status','invoicestatus','action']);
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
        $data['currencies'] = Quotation::groupBy('currency')->where(function($query){
            $query->where('status','!=',1);
            $query->where('status','!=',2);
        })->pluck('currency')->toArray();
        $data['breadcrum'] = __('Quotations');
        $data['countries'] = ContactCountry::all();
        return view('admin.sales.quotation.index', $data);
    }

    /**
     * Display a listing of the Sales Order.
     *
     */
    public function sales_order_listing(Request $request)
    {
        if(!auth()->user()->can('Orders Listing'))
        access_denied();

        $data = [];
        if ($request->ajax()) {
            $data = Quotation::with(
                        'customer',
                        'order_lines',
                        'order_lines.product',
                        'order_lines.variation',
                        'order_lines.quotation_taxes',
                        'optional_products',
                        'optional_products.product',
                        'optional_products.variation',
                        'other_info',
                        'other_info.sales_person',
                        'other_info.sales_team'
                        )->where(function($query){
                            $query->where('status',1);
                            $query->orWhere('status',2);
                        })->orderBy('id','desc');
            if(isset($request->currency)){
                $data->where('currency', $request->currency);
            }
            if(isset($request->quotation_number)){
                $quotation_number = trim($request->quotation_number,"S");
                $quotation_number = trim($request->quotation_number,"s");
                $quotation_number = ltrim( $quotation_number, "0");
                $data->where('id', $quotation_number);
            }
            if(isset($request->country_id)){
                $data->whereHas('customer', function($query) use($request){
                    $query->where('country_id',$request->country_id);
                });
            }
            if(isset($request->payment)){
                $data->whereHas('invoices.invoice_payment_history', function($query) use($request){
                    $query->where('method','LIKE','%'.$request->payment.'%');
                });
            }
            if(isset($request->customer_name_email)){
                $data->whereHas('customer', function($query) use($request){
                    $query->where('name','LIKE','%'.$request->customer_name_email.'%');
                    $query->orWhere('email','LIKE','%'.$request->customer_name_email.'%');
                });
            }
            if(isset($request->product_id) && $request->product_id != null && $request->product_id != ''){
                $data->whereHas('order_lines', function($query) use($request){
                    $query->where('product_id', $request->product_id);
                    if(isset($request->variation_id) && $request->variation_id != null && $request->variation_id != ''){
                        $query->where('variation_id', $request->variation_id);
                    }
                });
            }
            $data = $data->get();
            foreach($data as $ind => $d){
                // dd($data);
                if(isset($request->invoice_status) && $request->invoice_status != ''){
                    if($request->invoice_status == 4 ){
                        if(!$d->is_refunded ){
                            $data->forget($ind);
                        }
                    }
                    switch ($request->invoice_status) {
                        case 0:
                            if($d->is_refunded ){
                                $data->forget($ind);
                            }else{
                                if(count($d->invoices) > 0)
                                {
                                    $data->forget($ind);
                                }
                            }
                            break;
                        case 1:
                            # Partially Paid
                            if($d->is_refunded ){
                                $data->forget($ind);
                            }else{
                                
                                if(currency_format($d->total*$d->exchange_rate,'','',1) == currency_format($d->invoicedamount,'','',1)){
                                    $data->forget($ind);
                                }
                                elseif($d->invoicedamount != 0 && currency_format($d->total*$d->exchange_rate,'','',1) > currency_format($d->invoicedamount,'','',1)){

                                }else{
                                    $data->forget($ind);
                                }
                            }
                            break;
                        case 2:
                            # Fully Paid
                            if($d->is_refunded ){
                                $data->forget($ind);
                            }else{
                                
                                if(currency_format($d->total*$d->exchange_rate,'','',1) == currency_format($d->invoicedamount,'','',1)){
                                }
                                elseif($d->invoicedamount != 0 && currency_format($d->total*$d->exchange_rate,'','',1) > currency_format($d->invoicedamount,'','',1)){
                                    $data->forget($ind);
                                }else{
                                    $data->forget($ind);
                                }
                            }
                            break;
                        case 3:
                            # Un-Paid
                            if($d->is_refunded ){
                                $data->forget($ind);
                            }else{
                                
                                if(count($d->invoices) == 0){

                                    $data->forget($ind);
                                }
                                if(currency_format($d->total*$d->exchange_rate,'','',1) == currency_format($d->invoicedamount,'','',1)){
                                    $data->forget($ind);
                                }
                                elseif($d->invoicedamount != 0 && currency_format($d->total*$d->exchange_rate,'','',1) > currency_format($d->invoicedamount,'','',1)){
                                    $data->forget($ind);
                                }
                            }
                            break;
                        default:
                            # code...
                            break;
                    }
                }
                if(isset($request->amount)&& $request->amount != ''){
                    if(isset($request->currency) && $request->currency != '')
                    {
                        if( currency_format($d->total * $d->exchange_rate,'','',1) != $request->amount )
                        {
                            $data->forget($ind);
                        }
                    }
                    else
                    {

                        if( $d->total != $request->amount )
                        {
                            $data->forget($ind);
                        }
                    }
                }
            }
            $datatable = Datatables::of($data);
            $datatable->editColumn('ordernumber', function ($row) {
                return 'S'.str_pad($row->id, 5, '0', STR_PAD_LEFT);
                return auth()->user()->can('View Quotation') ? '<a href="' .route('admin.quotations.show',Hashids::encode($row->id)). '">S'.str_pad($row->id, 5, '0', STR_PAD_LEFT).'</a>': 'S'.str_pad($row->id, 5, '0', STR_PAD_LEFT);
            });
            $datatable->addColumn('creationdate', function ($row) {
                return \Carbon\Carbon::parse($row->created_at)->format('d-M-Y');
            });
            $datatable->addColumn('deliverydate', function ($row) {
                return \Carbon\Carbon::parse($row->other_info->delivery_date)->format('d-M-Y');
            });
            $datatable->addColumn('expecteddate', function ($row) {
                return \Carbon\Carbon::parse($row->created_at)->addDays($row->payment_due_day)->format('d-M-Y');
            });
            $datatable->addColumn('customer', function ($row) {
                return $row->customer->name;
            });
            $datatable->addColumn('salesperson', function ($row) {
                return @$row->other_info->sales_person->firstname.' '.@$row->other_info->sales_person->lastname;
            });
            $datatable->addColumn('total', function ($row) {
                // return number_format($row->total * $row->exchange_rate, 2).' '.$row->currency_symbol;
                return currency_format(@$row->total * $row->exchange_rate ,$row->currency_symbol,$row->currency);
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
            $datatable->addColumn('invoicestatus', function ($row) {
                if(count($row->invoices) == 0){
                    return '<span class="tagged warning">'.__('Not Created').'</span>';
                }
                if(count($row->invoices) > 0){
                    if($row->is_refunded){
                        return '<span class="tagged danger">'.__('Refunded').'</span>';
                    
                    }elseif(currency_format($row->total * $row->exchange_rate,'','',1) == currency_format($row->invoicedamount,'','',1)){
                        return '<span class="tagged success">'.__('Fully Invoiced').'</span>';
                    }
                    elseif($row->invoicedamount != 0 && currency_format($row->total * $row->exchange_rate,'','',1) > currency_format($row->invoicedamount,'','',1)){
                        return '<span class="tagged quote">'.__('Partially Invoiced').'</span>';
                    }else{
                        return '<span class="tagged danger">'.__('Not Paid').'</span>';
                    }
                }


            });
            $datatable->addColumn('action', function ($row) {
                 $actions = '';
                if (auth()->user()->hasAnyPermission(['Edit Quotation','View Quotation','Delete Quotation']))
                {
                    $actions = '<div style="display:inline-flex">';
                    $actions .= auth()->user()->can('Edit Quotation') ? '&nbsp;<a class="btn btn-primary btn-icon" href="' . url("admin/sales-management/quotations/". Hashids::encode($row->id) . "/edit") . '" title='.__('Edit').'><i class="fa fa-pencil"></i></a>' : '';
                    $actions.= auth()->user()->can('View Quotation') ? '&nbsp;<a class="btn btn-warning btn-icon" href="' . route('admin.quotations.show',Hashids::encode($row->id)) . '" title='.__('View').'><i class="fa fa-eye"></i></a>' : '';
                    if(auth()->user()->can('Delete Quotation')) {
                        $actions .= '&nbsp;' . Form::open([
                            'method' => 'DELETE',
                            'url' => [route('admin.quotations.destroy',Hashids::encode($row->id))],
                            'style' => 'display:inline'
                        ]);

                        $actions .= Form::button('<i class="fa fa-trash fa-fw" title='.__('Delete').'></i>', ['onclick' => 'deleteAlert(this)', 'class' => 'delete-form-btn btn btn-default btn-icon']);
                        $actions .= Form::submit('Delete', ['class' => 'hidden deleteSubmit']);

                        $actions .= Form::close();
                    }
                    $actions .= '&nbsp;<a title="Vouchers" class="" href="'.route("admin.quotation.voucher.list", Hashids::encode($row->id)).'">';
                        $actions .= '<button class="btn btn-primary ">';
                            $actions .= '<i class="fa fa-gift" aria-hidden="true"></i>';
                        $actions .= '</button>';
                    $actions .= '</a>';
                    $actions .= '</div>';
                }
                return $actions;
            });
            $datatable = $datatable->rawColumns(['ordernumber','status','invoicestatus','action']);
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
        $data['breadcrum'] = __('Sales Orders');
        $data['sales_order'] = true;
        $data['countries'] = ContactCountry::all();
        $data['currencies'] = Quotation::groupBy('currency')->where(function($query){
            $query->where('status',1);
            $query->orWhere('status',2);
        })->pluck('currency')->toArray();
        return view('admin.sales.quotation.index', $data);
    }

    /**
     * Display a listing of the Sales Order.
     *
     */
    public function order_to_invoice_listing(Request $request)
    {
        $data = [];
        if ($request->ajax()) {
            $data = Quotation::whereDoesntHave('invoices')->with(
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
                        );
            if(isset($request->currency)){
                $data->where('currency', $request->currency);
            }
            if(isset($request->currency)){
                $data->where('currency', $request->currency);
            }
            if(isset($request->quotation_number)){
                $quotation_number = trim($request->quotation_number,"S");
                $quotation_number = trim($request->quotation_number,"s");
                $quotation_number = ltrim( $quotation_number, "0");
                $data->where('id', $quotation_number);
            }
            if(isset($request->country_id)){
                $data->whereHas('customer', function($query) use($request){
                    $query->where('country_id',$request->country_id);
                });
            }
            if(isset($request->customer_name_email)){
                $data->whereHas('customer', function($query) use($request){
                    $query->where('name','LIKE','%'.$request->customer_name_email.'%');
                    $query->orWhere('email','LIKE','%'.$request->customer_name_email.'%');
                });
            }
            if(isset($request->product_id) && $request->product_id != null && $request->product_id != ''){
                $data->whereHas('order_lines', function($query) use($request){
                    $query->where('product_id', $request->product_id);
                    if(isset($request->variation_id) && $request->variation_id != null && $request->variation_id != ''){
                        $query->where('variation_id', $request->variation_id);
                    }
                });
            }
            $data = $data->get();
            foreach($data as $ind => $d){
                // dd($data);
                if(isset($request->invoice_status) && $request->invoice_status != ''){
                    switch ($request->invoice_status) {
                        case 0:
                            if(count($d->invoices) > 0)
                            {
                                $data->forget($ind);
                            }
                            break;
                        case 1:
                            # Partially Paid
                            if(number_format($d->total,2) == number_format($d->invoicedamount,2)){
                                $data->forget($ind);
                            }
                            elseif($d->invoicedamount != 0 && number_format($d->total,2) > number_format($d->invoicedamount,2)){

                            }else{
                                $data->forget($ind);
                            }
                            break;
                        case 2:
                            # Fully Paid
                            if(number_format($d->total,2) == number_format($d->invoicedamount,2)){
                            }
                            elseif($d->invoicedamount != 0 && number_format($d->total,2) > number_format($d->invoicedamount,2)){
                                $data->forget($ind);
                            }else{
                                $data->forget($ind);
                            }
                            break;
                        case 3:
                            # Un-Paid
                            if(count($d->invoices) == 0){

                                $data->forget($ind);
                            }
                            if(number_format($d->total,2) == number_format($d->invoicedamount,2)){
                                $data->forget($ind);
                            }
                            elseif($d->invoicedamount != 0 && number_format($d->total,2) > number_format($d->invoicedamount,2)){
                                $data->forget($ind);
                            }
                            break;
                        default:
                            # code...
                            break;
                    }
                }
                if(isset($request->amount)&& $request->amount != ''){
                    if(isset($request->currency) && $request->currency != '')
                    {
                        if( number_format($d->total * $d->exchange_rate,2) != $request->amount )
                        {
                            $data->forget($ind);
                        }
                    }
                    else
                    {

                        if( $d->total != $request->amount )
                        {
                            $data->forget($ind);
                        }
                    }
                }
            }
            $datatable = Datatables::of($data);
            $datatable = Datatables::of($data);
            $datatable->addColumn('ordernumber', function ($row) {
                return '<a href="' .route('admin.quotations.show',Hashids::encode($row->id)). '">S'.str_pad($row->id, 5, '0', STR_PAD_LEFT).'</a>';
            });
            $datatable->addColumn('creationdate', function ($row) {
                return \Carbon\Carbon::parse($row->created_at)->format('d-M-Y');
            });
            $datatable->addColumn('deliverydate', function ($row) {
                return \Carbon\Carbon::parse($row->other_info->delivery_date)->format('d-M-Y');
            });
            $datatable->addColumn('expecteddate', function ($row) {
                return \Carbon\Carbon::parse($row->created_at)->addDays($row->payment_due_day)->format('d-M-Y');
            });
            $datatable->addColumn('customer', function ($row) {
                return $row->customer->name;
            });
            $datatable->addColumn('salesperson', function ($row) {
                return @$row->other_info->sales_person->firstname.' '.@$row->other_info->sales_person->lastname;
            });
            $datatable->addColumn('total', function ($row) {
                return currency_format(@$row->total * $row->exchange_rate ,$row->currency_symbol,$row->currency);
                // return number_format($row->total * $row->exchange_rate, 2).' '.$row->currency_symbol;
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
            $datatable->addColumn('invoicestatus', function ($row) {
                if($row->total == $row->invoicedamount){
                    return '<span class="tagged success">'.__('Fully Invoiced').'</span>';
                }
                elseif($row->invoicedamount != 0 && $row->total > $row->invoicedamount){
                    return '<span class="tagged quote">'.__('Partially Invoiced').'</span>';
                }
                else{
                    return '<span class="tagged warning">'.__('To Invoice').'</span>';
                }
            });
            $datatable->addColumn('action', function ($row) {
                $actions = '<div style="display:inline-flex">';

                $actions .= '&nbsp;<a class="btn btn-primary btn-icon" href="' . url("admin/sales-management/quotations/". Hashids::encode($row->id) . "/edit") . '" title='.__('Edit').'><i class="fa fa-pencil"></i></a>&nbsp;<a class="btn btn-warning btn-icon" href="' . route('admin.quotations.show',Hashids::encode($row->id)) . '" title='.__('View').'><i class="fa fa-eye"></i></a>';

                $actions .= '&nbsp;' . Form::open([
                    'method' => 'DELETE',
                    'url' => [route('admin.quotations.destroy',Hashids::encode($row->id))],
                    'style' => 'display:inline'
                ]);

                $actions .= Form::button('<i class="fa fa-trash fa-fw" title='.__('Delete').'></i>', ['onclick' => 'deleteAlert(this)', 'class' => 'delete-form-btn btn btn-default btn-icon']);
                $actions .= Form::submit('Delete', ['class' => 'hidden deleteSubmit']);

                $actions .= Form::close();
                $actions .= '</div>';
                return $actions;
            });
            $datatable = $datatable->rawColumns(['ordernumber','status','invoicestatus','action']);
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
        $data['countries'] = ContactCountry::all();
        $data['breadcrum'] = __('Orders To Invoice');
        $data['order_to_invoice'] = true;
        $data['currencies'] = Quotation::groupBy('currency')->whereDoesntHave('invoices')->pluck('currency')->toArray();
        return view('admin.sales.quotation.index', $data);
    }

    public function vouchersList($quotation_id, Request $request)
    {
        $data = [];
        $data['quotation_id'] = $quotation_id;

        if ($request->ajax()) {
            $quotation_id = Hashids::decode($quotation_id);
            // dd($quotation_id);
            $data = QuotationOrderLineVoucher::where('quotation_id',$quotation_id)->get();
            $datatable = Datatables::of($data);
            $datatable->editColumn('voucher_code', function ($row) {
                return $row->voucher_code;
            });
            $datatable->addColumn('product', function ($row) {
                return $row->order_line->product->product_name.' '.@$row->order_line->variation->variation_name  ;
            });
            $datatable->addColumn('customer', function ($row) {
                return $row->customer ? $row->customer->name.' ('.$row->customer->email.')' : '' ;
            });
            $datatable->editColumn('status', function ($row) {
                $status = $row->redeemed_at != null ?  '<span class="badge  bg-success">'.__('Redeemed At').' '.Carbon::parse($row->redeemed_at)->format('d-M-Y').'</span>' : '';
                $status = $status == '' ? ($row->status == 1 ? '<span class="badge  bg-green">'.__('Active').'</span>' : '<span class="badge  bg-danger">'.__('In-Active').'</span>') : $status;
                return  $status;
            });
            $datatable->addColumn('action', function ($row)  use($request){
                $actions = '' ;
                if($row->redeemed_at != null)
                {
                    $actions .= '<div style="display:inline-flex">';
                        $actions .= '&nbsp;' . Form::open([
                            'method' => 'GET',
                            'url' => ['#.'],
                            'style' => 'display:inline'
                        ]);

                        // $actions .= Form::button('<i class="fa fa-times fa-fw" title="Reject Voucher Order"></i>', ['type' => 'submit','class' => 'status-action-btn btn btn-default btn-icon']);
                        $actions .= Form::button(__('Deactivate'), ['type' => 'submit','class' => 'status-action-btn btn btn-danger btn-icon','disabled'=>'disabled']);
                        $actions .= Form::submit('Deactivate', ['class' => 'hidden deleteSubmit']);

                        $actions .= Form::close();
                        $actions .= '&nbsp;' . Form::open([
                            'method' => 'GET',
                            'url' => ['#.'],
                            'style' => 'display:inline'
                        ]);

                        // $actions .= Form::button('<i class="fa fa-times fa-fw" title="Reject Voucher Order"></i>', ['type' => 'submit','class' => 'status-action-btn btn btn-default btn-icon']);
                        $actions .= Form::button(__('Accept'), ['type' => 'submit','class' => 'status-action-btn btn btn-success btn-icon','disabled'=>'disabled']);
                        $actions .= Form::submit('Accept', ['class' => 'hidden deleteSubmit']);

                        $actions .= Form::close();
                    $actions .= '</div>';
                }
                elseif($row->status == 0)
                {
                    $actions .= '<div style="display:inline-flex">';
                        $actions .= '&nbsp;' . Form::open([
                            'method' => 'GET',
                            'url' => [route('admin.quotation.voucher.change-status',[ Hashids::encode( $row->id ), 1 ] )],
                            'style' => 'display:inline'
                        ]);

                        // $actions .= Form::button('<i class="fa fa-check fa-fw" title="Approve Voucher Order"></i>', ['type' => 'submit','class' => 'status-action-btn btn btn-default btn-icon']);
                        $actions .= Form::button(__('Approve'), ['type' => 'submit','class' => 'status-action-btn btn btn-success btn-icon']);
                        $actions .= Form::submit('Approve', ['class' => 'hidden deleteSubmit']);

                        $actions .= Form::close();
                    $actions .= '</div>';
                }
                elseif($row->status == 1)
                {
                    $actions .= '<div style="display:inline-flex">';
                        $actions .= '&nbsp;' . Form::open([
                            'method' => 'GET',
                            'url' => [route('admin.quotation.voucher.change-status',[ Hashids::encode( $row->id ), 0 ] )],
                            'style' => 'display:inline'
                        ]);

                        // $actions .= Form::button('<i class="fa fa-times fa-fw" title="Reject Voucher Order"></i>', ['type' => 'submit','class' => 'status-action-btn btn btn-default btn-icon']);
                        $actions .= Form::button(__('Deactivate'), ['type' => 'submit','class' => 'status-action-btn btn btn-danger btn-icon']);
                        $actions .= Form::submit('Deactivate', ['class' => 'hidden deleteSubmit']);

                        $actions .= Form::close();
                    $actions .= '</div>';
                }
                return $actions;
            });
            $datatable = $datatable->rawColumns(['status','action']);
            return $datatable->make(true);
        }
        return view('admin.sales.quotation.vouchers', $data);
    }
    public function changeVoucherStatus($voucher_id, $status)
    {
        $voucher_id = Hashids::decode($voucher_id);
        QuotationOrderLineVoucher::where('id', $voucher_id)->update(['status'=>$status]);
        Alert::success(__('Success'), __('Status updated successfully!'))->persistent('Close')->autoclose(5000);
        return redirect()->back();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!auth()->user()->can('Create Quotation'))
        access_denied();
        $data = [];

        $data['action'] = 'Add';
        $data['payment_term'] = PaymentTerm::all();
        // $data['customer'] = Contact::where('status',1)->whereIn('type',[0,2,3,4])->get();
        $data['customer'] = Contact::where('status',1)->whereIn('type',[0,2,4])->get();
        $data['salespersons'] = Admin::where('is_active',1)->get();
        $data['salesteams'] = SalesTeam::all();
        $data['price_lists'] = ProductPriceList::whereNotIn('name',['KSS TIMmunity Customers','KSS TIMmunity Guest'])->whereNull('parent_id')->where('is_active',1)->get();
        $data['contact_tags'] = ContactTag::all();
        $productList = Products::with([
                                'generalInformation',
                                'customer_taxes',
                                // 'variations',
                                'variations' => function ($query) {
                                    $query->where('is_active', 1);
                                },
                                'variations.variation_details'
                            ])->where('project_id',NULL)->get();
        $data['products'] = [];
        $data['customer_taxes'] = Tax::where('applicable_on',0)->where('is_active',1)->get();
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
                    $variation_name = $prod->product_name;
                    $product_variation_detail_count = count( $prod_variation->variation_details );
                    $variation_price = isset($prod->generalInformation->sales_price) ? $prod->generalInformation->sales_price : 0;
                    if($prod_variation->variation_sales_price == null){
                        foreach( $prod_variation->variation_details as  $ind => $prod_variation_detail)
                        {
                            $pro_attached_attr_val = ProductAttachedAttributeValue::where('product_attached_atribute_id',$prod_variation_detail->product_attached_attribute_id)
                                                ->where( 'value', $prod_variation_detail->attribute_value )->first();
                            $variation_price += $pro_attached_attr_val ? $pro_attached_attr_val->extra_price : 0;
                            if( $ind == 0 )     // First element
                            {
                                $variation_name .= ' ( '.$prod_variation_detail->attribute_value;
                            }
                            elseif( $ind < $product_variation_detail_count - 1)     // In between first and last element
                            {
                                $variation_name .= ' - '.$prod_variation_detail->attribute_value;
                            }
                            else    //  Last Element
                            {
                                $variation_name .= ' - '.$prod_variation_detail->attribute_value.' )';
                            }
                        }
                    }
                    else
                    {
                        $variation_price = $prod_variation->variation_sales_price;
                    }

                    $store['product_id'] = $prod->id;
                    $store['variation_id'] = $prod_variation->id;
                    // $store['name'] = $variation_name;
                    $store['name'] = $prod->product_name.' '.$prod_variation->variation_name ;
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
        return view('admin.sales.quotation.quotation_form')->with($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();
        $id = Hashids::decode($input['id']);
        $quotation_order_lines =  QuotationOrderLine::where( 'quotation_id', $id)->get();
        if (isset($input['action']) && $input['action'] == 'Edit') {
            if(isset($input['customer_id'])){
                $quotation_data['customer_id'] = $input['customer_id'];
            }
            if(isset($input['expires_at'])){
                $quotation_data['expires_at'] =  $input['expires_at'];
            }
            if(isset($input['pricelist_id'])){
                $quotation_data['pricelist_id'] =  $input['pricelist_id'];
            }
            if(isset($input['invoice_address'])){
                $quotation_data['invoice_address'] =  $input['invoice_address'];
            }
            if(isset($input['delivery_address'])){
                $quotation_data['delivery_address'] =  $input['delivery_address'];
            }
            $quotation_data['payment_terms'] =  $input['payment_terms'];
            if(isset($input['payment_due_date'])){
                $quotation_data['payment_due_day'] =  $input['payment_due_date'];
            }
            $quotation_data['terms_and_conditions'] =  $input['terms_and_conditions'];
            $quotation_data['status'] =  (int)$input['quotation_status'];     // Quotation
            // if($input['quotation_status'] && ($input['quotation_status'] == 1 || $input['quotation_status'] == 2))
            // dd($input['quotation_status']);
            // if($input['quotation_status'] && $input['quotation_status'] == 2 )
            // {
            //     if(count($quotation_order_lines) > 0) {
            //         $all_license_generated = $this->attachLicenses($id);
            //     }else{
            //         Alert::error(__('Failure'), __('Quotation must have atleast one product added!'))->persistent('Close')->autoclose(5000);
            //         return redirect()->back();
            //     }
            // }
            $model = Quotation::findOrFail($id)[0];
            $model->update($quotation_data);
            $model->vat_percentage = $input['vat_percentage'];
            $model->save();

            $quotation_order_lines_ids = explode(',',$input['quotation_order_lines']);
            $quotation_optional_product_ids = explode(',',$input['optional_products']);

            QuotationOrderLine::where('quotation_id',$id)->whereNotIn('id',$quotation_order_lines_ids)->delete();
            QuotationOptionalProduct::where('quotation_id',$id)->whereNotIn('id',$quotation_optional_product_ids)->delete();

            $model_info = QuotationOtherInfo::where('quotation_id',$id)->first();
                $model_info->salesperson_id = $input['otherinfo']['sales_person'];
                $model_info->sales_team_id = $input['otherinfo']['sales_team'];
                $model_info->customer_reference = $input['otherinfo']['custom_reference'];
                $model_info->online_signature = isset($input['otherinfo']['online_signature']) ? 1 : 0;
                $model_info->online_payment = isset($input['otherinfo']['online_payment']) ? 1 : 0;
                $model_info->delivery_date = $input['otherinfo']['delivery_date_reference'];
            $model_info->save();

            if(isset($input['otherinfo']['tags']) && $input['otherinfo']['tags'] !=null){
                $model->tags()->sync($input['otherinfo']['tags']);
            }else{
                $model->tags()->sync([]);
            }

            if(isset($input['text_template']['sale_quotation'])){
                $new_sale_quotation = QuotationTextTemplate::where('quotation_id',$id)->where('type',0)->first();
                if($new_sale_quotation){
                    $new_sale_quotation->text = $input['text_template']['sale_quotation'];
                    $new_sale_quotation->save();
                }else{
                    $new_sale_quotation = new QuotationTextTemplate;
                    $new_sale_quotation->quotation_id = $model->id;
                    $new_sale_quotation->type = 0;
                    $new_sale_quotation->text = $input['text_template']['sale_quotation'];
                    $new_sale_quotation->save();
                }

            }else{
                QuotationTextTemplate::where('quotation_id',$id)->where('type',0)->delete();
            }

            if(isset($input['text_template']['sale_confirmation'])){
                $new_sale_quotation = QuotationTextTemplate::where('quotation_id',$id)->where('type',1)->first();
                if($new_sale_quotation){
                    $new_sale_quotation->text = $input['text_template']['sale_confirmation'];
                    $new_sale_quotation->save();
                }else{
                    $new_sale_quotation = new QuotationTextTemplate;
                    $new_sale_quotation->quotation_id = $model->id;
                    $new_sale_quotation->type = 1;
                    $new_sale_quotation->text = $input['text_template']['sale_confirmation'];
                    $new_sale_quotation->save();
                }
            }else{
                QuotationTextTemplate::where('quotation_id',$id)->where('type',0)->where('type',1)->delete();
            }

            if(isset($input['text_template']['performa_invoice'])){
                $new_sale_quotation = QuotationTextTemplate::where('quotation_id',$id)->where('type',2)->first();
                if($new_sale_quotation){
                    $new_sale_quotation->text = $input['text_template']['performa_invoice'];
                    $new_sale_quotation->save();
                }else{
                    $new_sale_quotation = new QuotationTextTemplate;
                    $new_sale_quotation->quotation_id = $model->id;
                    $new_sale_quotation->type = 2;
                    $new_sale_quotation->text = $input['text_template']['performa_invoice'];
                    $new_sale_quotation->save();
                }
            }else{
                QuotationTextTemplate::where('quotation_id',$id)->where('type',0)->where('type',2)->delete();
            }

        } else {
            $quotation_data['customer_id'] = $input['customer_id'];
            $quotation_data['pricelist_id'] =  isset($input['pricelist_id']) ? $input['pricelist_id'] : null;
            $quotation_data['expires_at'] =  $input['expires_at'];
            $quotation_data['invoice_address'] =  isset($input['invoice_address']) ? $input['invoice_address'] : null;;
            $quotation_data['delivery_address'] =  isset($input['delivery_address']) ? $input['delivery_address'] : null;
            $quotation_data['payment_terms'] =  $input['payment_terms'];
            $quotation_data['payment_due_day'] =  $input['payment_due_date'];
            $quotation_data['terms_and_conditions'] =  $input['terms_and_conditions'];
            $quotation_data['invoice_status'] =  0; //Sales Order
            $quotation_data['status'] =  (int)$input['quotation_status'];     // Quotation Status

            $model = new Quotation();
            $model->fill($quotation_data)->save();
            $model->vat_percentage = $input['vat_percentage'];
            $model->currency_symbol = "€";
            $model->currency = "EUR";
            $model->exchange_rate = 1;
            $model->vat_label = $input['vat_label'];
            $model->save();

            $quotation_other_info['quotation_id'] = $model->id;
            $quotation_other_info['salesperson_id'] = $input['otherinfo']['sales_person'];
            $quotation_other_info['sales_team_id '] = $input['otherinfo']['sales_team'];
            $quotation_other_info['customer_reference'] = $input['otherinfo']['custom_reference'];
            $quotation_other_info['online_signature'] = isset($input['otherinfo']['online_signature']) ? 1 : 0;
            $quotation_other_info['online_payment'] = isset($input['otherinfo']['online_payment']) ? 1 : 0;
            $quotation_other_info['delivery_date'] = $input['otherinfo']['delivery_date_reference'];

            $model_info = new QuotationOtherInfo();
                $model_info->quotation_id = $model->id;
                $model_info->salesperson_id = $input['otherinfo']['sales_person'];
                $model_info->sales_team_id = ( $input['otherinfo']['sales_team'] != null && $input['otherinfo']['sales_team'] != 0 && $input['otherinfo']['sales_team'] != '' ) ? $input['otherinfo']['sales_team'] : 2;
                $model_info->customer_reference = $input['otherinfo']['custom_reference'];
                $model_info->online_signature = isset($input['otherinfo']['online_signature']) ? 1 : 0;
                $model_info->online_payment = isset($input['otherinfo']['online_payment']) ? 1 : 0;
                $model_info->delivery_date = $input['otherinfo']['delivery_date_reference'];
            $model_info->save();
            if(isset($input['otherinfo']['tags']) && $input['otherinfo']['tags'] !=null){
                foreach ($input['otherinfo']['tags'] as $key => $value) {
                    $tag_data['quotation_id'] = $model->id;
                    $tag_data['tag_id'] = (int)$value;
                    $model_tag = new QuotationOtherInfoTag();
                    $model_tag->tag_id = (int)$value;
                    $model_tag->quotation_id = (int)$model->id;
                    $model_tag->save();
                }
            }
            if(isset($input['text_template']['sale_quotation'])){
                $new_sale_quotation = new QuotationTextTemplate;
                $new_sale_quotation->quotation_id = $model->id;
                $new_sale_quotation->type = 0;
                $new_sale_quotation->text = $input['text_template']['sale_quotation'];
                $new_sale_quotation->save();
            }
            if(isset($input['text_template']['sale_confirmation'])){
                $new_sale_quotation = new QuotationTextTemplate;
                $new_sale_quotation->quotation_id = $model->id;
                $new_sale_quotation->type = 1;
                $new_sale_quotation->text = $input['text_template']['sale_confirmation'];
                $new_sale_quotation->save();
            }
            if(isset($input['text_template']['performa_invoice'])){
                $new_sale_quotation = new QuotationTextTemplate;
                $new_sale_quotation->quotation_id = $model->id;
                $new_sale_quotation->type = 2;
                $new_sale_quotation->text = $input['text_template']['performa_invoice'];
                $new_sale_quotation->save();
            }

        }


        $quotation_order_lines_ids = explode(',',$input['quotation_order_lines']);
        QuotationOrderLine::whereIn( 'id', $quotation_order_lines_ids )->update( [ "quotation_id" => $model->id] );

        // If the Status is Set to Sales Order

        $quotation_optional_product_ids = explode(',',$input['optional_products']);
        QuotationOptionalProduct::whereIn( 'id', $quotation_optional_product_ids )->update( [ "quotation_id" => $model->id] );


        if (isset($input['action']) && $input['action'] == 'Edit')
        {
            if($input['quotation_status'] && $input['quotation_status'] == 2 )
            {
                // Transformation of Order Placed Email Template
                $name = $model->customer->name;
                $email = $model->customer->email;
                $order_number = "S".str_pad($model->id, 5, '0', STR_PAD_LEFT);
                $quotation_pdf = $this->generate_quotation_pdf_save($model->id);
                $quotation = $model;
                $email_template = EmailTemplate::where('type','quotation_order_placed')->first();
                $lang = app()->getLocale();
                $email_template = transformEmailTemplateModel($email_template,$lang);
                $content = $email_template['content'];
                $subject = $email_template['subject'];
                $search = array("{{name}}","{{order_number}}","{{app_name}}");
                $replace = array($name,$order_number,env('APP_NAME'));
                $content = str_replace($search,$replace,$content);
                dispatch(new \App\Jobs\SendOrderEmailJob($email,$subject,$content,$quotation_pdf));
            }
            Alert::success(__('Success'), __('Quotation has been updated successfully!'))->persistent('Close')->autoclose(5000);
        }
        else
        {

            Alert::success(__('Success'), __('Quotation has been added successfully!'))->persistent('Close')->autoclose(5000);
        }
        if( isset( $input['redirect_url'] ) )
        {
            return redirect($input['redirect_url']);
        }
        else{
            return redirect()->route('admin.quotations.index');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        if(!auth()->user()->can('View Quotation'))
        access_denied();

        $data = [];

        $id = Hashids::decode($id)[0];
        $data['action'] = 'Edit';
        $data['payment_term'] = PaymentTerm::all();
        $data['customer'] = Contact::where('status',1)->whereIn('type',[0,2,3,4])->get();
        $data['salespersons'] = Admin::where('is_active',1)->get();
        $data['salesteams'] = SalesTeam::all();
        $data['price_lists'] = ProductPriceList::all();
        $data['contact_tags'] = ContactTag::all();

        $data['model'] = Quotation::with(
                'invoices',
                'customer',
                'customer.contact_addresses',
                'customer.contact_addresses.contact_countries',
                'pricelist',
                'order_lines',
                'order_lines.product',
                'order_lines.variation',
                'order_lines.quotation_taxes',
                'order_lines.quotation_taxes.tax',
                'order_lines.quotation_taxes.tax',
                'optional_products',
                'optional_products.product',
                'optional_products.variation',
                'other_info',
                'other_info.sales_person',
                'other_info.sales_team',
                'other_info.tags',
                'payment_term_detail',
                'invoice_address_detail',
                'delivery_address_detail',
                'text_templates'
            )->where('id', $id)->first();
        // dd($data['model']->pricelist);
        $data['paymentPaid'] = false;
        if(@$data['model']->transaction_id != null){
            // $data['paymentPaid'] = $this->getPaymentStatus($data['model']->transaction_id);
        }
        $data['model']->pdf_quotation_path = $this->generate_quotation_pdf_save($id);
        // return $this->generate_quotation_pdf_save($id);
        // Code For Activities Section
        $log_uid = Auth::user()->id;
        $log_user_name = Auth::user()->firstname .' '. Auth::user()->lastname;
        $start_date = $data['model']->start_date;
        $end_date = $data['model']->end_date;
        $logged_in_follower_ids = Followers::where('contact_id', $log_uid)->where('quotation_id', $id)->where('module_type', 2)->where('follower_type', 2)->pluck('follower_id')->toArray();
        if (Contact::whereIn('id', $logged_in_follower_ids)->where('admin_id', $log_uid)->exists()) {

           $data['is_following'] = 1;
        }
        else {

            $data['is_following'] = 0;
        }
        $data['followers'] = $this->quotationRepository->follower_list($id,$log_uid, $module_type = 2);
        $data['send_messages'] = ActivityMessages::with('activity_message_users','activity_attachments')->where('quotation_id',$id)->orderBy('id','desc')->get();
        $attachments = ActivityAttachments::where('quotation_id', $id)->orderBy('send_msg_id','desc')->orderBy('log_note_id', 'desc')->orderBy('schedule_activity_id', 'desc')->get();
        $data['log_notes'] = ActivityLogNotes::with('log_note_users','activity_attachments')->where('quotation_id',$id)->orderBy('id','desc')->get();
        $recipients = Contact::where('admin_id','<>', Auth::user()->id)->orWhere('admin_id', null)->where('status', 1)->get();
        $schedule_users = Admin::where('is_active',1)->where('is_archive','<>', 1)->get();
        $schedule_activity_types = ActivityTypes::where('status',1)->get();
        $schedule_activities = ScheduleActivities::with('activity_types','schedule_by_users','assign_to_users')->where('quotation_id', $id)->where('status', 0)->orderBy('due_date','asc')->get();
        $scheduled_done_activities = ScheduleActivities::with('activity_types','schedule_by_users','assign_to_users','activity_attachments')->where('quotation_id', $id)->where('status', 1)->orderBy('id','desc')->get();
        $data['diffInDays'] = Carbon::parse($start_date)->diffInDays(Carbon::parse($end_date));
        $data['diffInMonths'] = Carbon::parse($start_date)->floatdiffInMonths(Carbon::parse($end_date));
        $data['send_messages_view'] = $this->quotationRepository->sendMsgs($id, $log_uid, $module ='quotations', $log_user_name, $recipients, $module_type = 2,$log_uid);
        $data['log_notes_view'] = $this->quotationRepository->logNotes($id, $log_uid, $module ='quotations', $log_user_name);
        $data['schedual_activities_view'] = $this->quotationRepository->schedualActivities($id, $log_uid, $module ='quotations', $schedule_users, $schedule_activity_types, $log_uid, $module_type = 2);
        $data['notes_tab_partial_view'] = $this->quotationRepository->notesTabPartialView($data['log_notes']);
        $data['send_message_tab_partial_view'] = $this->quotationRepository->sendMsgTabPartialView($data['send_messages']);
        $data['schedual_activity_tab_partial_view'] = $this->quotationRepository->schedualActivityTabPartialView($schedule_activities, $scheduled_done_activities, $module ='quotations');
        $data['attachments_partial_view'] = $this->quotationRepository->attachmentsPartialView($attachments);

        return view('admin.sales.quotation.quotation_detail')->with($data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if(!auth()->user()->can('Edit Quotation'))
        access_denied();

        $data = [];

        $id = Hashids::decode($id);
        $data['action'] = 'Edit';
        $data['payment_term'] = PaymentTerm::all();
        // $data['customer'] = Contact::where('status',1)->whereIn('type',[0,2,3,4])->get();
        $data['customer'] = Contact::where('status',1)->whereIn('type',[0,2,4])->get();
        $data['salespersons'] = Admin::where('is_active',1)->get();
        $data['salesteams'] = SalesTeam::all();
        $data['price_lists'] = ProductPriceList::whereNotIn('name',['KSS TIMmunity Customers','KSS TIMmunity Guest'])->whereNull('parent_id')->where('is_active',1)->get();
        $data['contact_tags'] = ContactTag::all();
        $data['customer_taxes'] = Tax::where('applicable_on',0)->where('is_active',1)->get();

        $productList = Products::with( 'generalInformation', 'customer_taxes', 'variations', 'variations.variation_details' )->where('project_id',NULL)->get();
        $data['products'] = [];
        $data['customer_taxes'] = Tax::where('applicable_on',0)->where('is_active',1)->get();
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
                    $variation_name = $prod->product_name;
                    $product_variation_detail_count = count( $prod_variation->variation_details );
                    $variation_price = isset($prod->generalInformation->sales_price) ? $prod->generalInformation->sales_price : 0;
                    if($prod_variation->variation_sales_price == null)
                    {
                        foreach( $prod_variation->variation_details as  $ind => $prod_variation_detail)
                        {
                            $pro_attached_attr_val = ProductAttachedAttributeValue::where('product_attached_atribute_id',$prod_variation_detail->product_attached_attribute_id)
                                                ->where( 'value', $prod_variation_detail->attribute_value )->first();
                            $variation_price += $pro_attached_attr_val->extra_price;
                            if( $ind == 0 )     // First element
                            {
                                $variation_name .= ' ( '.$prod_variation_detail->attribute_value;
                            }
                            elseif( $ind < $product_variation_detail_count - 1)     // In between first and last element
                            {
                                $variation_name .= ' - '.$prod_variation_detail->attribute_value;
                            }
                            else    //  Last Element
                            {
                                $variation_name .= ' - '.$prod_variation_detail->attribute_value.' )';
                            }
                        }
                    }
                    else
                    {
                        $variation_price = $prod_variation->variation_sales_price;
                    }

                    $store['product_id'] = $prod->id;
                    $store['variation_id'] = $prod_variation->id;
                    // $store['name'] = $variation_name;
                    $store['name'] = $prod->product_name.' '.$prod_variation->variation_name ;
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

        $data['model'] = Quotation::with(
                'invoices',
                'customer',
                'customer.contact_addresses',
                'customer.contact_addresses.contact_countries',
                'customer.contact_countries',
                'pricelist',
                'order_lines',
                'order_lines.product',
                'order_lines.variation',
                'order_lines.quotation_taxes',
                'order_lines.quotation_taxes.tax',
                'optional_products',
                'optional_products.product',
                'optional_products.variation',
                'other_info',
                'other_info.sales_person',
                'other_info.sales_team',
                'other_info.tags',
                'text_templates'
            )->where('id', $id)->first();
        // if($data['model']->status == 1){
        //     Alert::success(__('Warning'), __('Cannot Edit the Sales Order'))->persistent('Close')->autoclose(5000);
        //     return redirect()->route('admin.quotations.index');
        // }
        $data['model']->pdf_quotation_path = $this->generate_quotation_pdf_save($data['model']->id);
        // Code For Activities Section
        $log_uid = Auth::user()->id;
        $log_user_name = Auth::user()->firstname .' '. Auth::user()->lastname;
        $start_date = $data['model']->start_date;
        $end_date = $data['model']->end_date;
        $logged_in_follower_ids = Followers::where('contact_id', $log_uid)->where('quotation_id', $id)->where('module_type', 2)->where('follower_type', 2)->pluck('follower_id')->toArray();
        if (Contact::whereIn('id', $logged_in_follower_ids)->where('admin_id', $log_uid)->exists()) {

           $data['is_following'] = 1;
        }
        else {

            $data['is_following'] = 0;
        }
        $data['followers'] = $this->quotationRepository->follower_list($id,$log_uid, $module_type = 2);
        $data['send_messages'] = ActivityMessages::with('activity_message_users','activity_attachments')->where('quotation_id',$id)->orderBy('id','desc')->get();
        $attachments = ActivityAttachments::where('quotation_id', $id)->orderBy('send_msg_id','desc')->orderBy('log_note_id', 'desc')->orderBy('schedule_activity_id', 'desc')->get();
        $data['log_notes'] = ActivityLogNotes::with('log_note_users','activity_attachments')->where('quotation_id',$id)->orderBy('id','desc')->get();
        $recipients = Contact::where('admin_id','<>', Auth::user()->id)->orWhere('admin_id', null)->where('status', 1)->get();
        $schedule_users = Admin::where('is_active',1)->where('is_archive','<>', 1)->get();
        $schedule_activity_types = ActivityTypes::where('status',1)->get();
        $schedule_activities = ScheduleActivities::with('activity_types','schedule_by_users','assign_to_users')->where('quotation_id', $id)->where('status', 0)->orderBy('due_date','asc')->get();
        $scheduled_done_activities = ScheduleActivities::with('activity_types','schedule_by_users','assign_to_users','activity_attachments')->where('quotation_id', $id)->where('status', 1)->orderBy('id','desc')->get();
        $data['diffInDays'] = Carbon::parse($start_date)->diffInDays(Carbon::parse($end_date));
        $data['diffInMonths'] = Carbon::parse($start_date)->floatdiffInMonths(Carbon::parse($end_date));
        $data['send_messages_view'] = $this->quotationRepository->sendMsgs($id, $log_uid, $module ='quotations', $log_user_name, $recipients, $module_type = 2,$log_uid);
        $data['log_notes_view'] = $this->quotationRepository->logNotes($id, $log_uid, $module ='quotations', $log_user_name);
        $data['schedual_activities_view'] = $this->quotationRepository->schedualActivities($id, $log_uid, $module ='quotations', $schedule_users, $schedule_activity_types, $log_uid, $module_type = 2);
        $data['notes_tab_partial_view'] = $this->quotationRepository->notesTabPartialView($data['log_notes']);
        $data['send_message_tab_partial_view'] = $this->quotationRepository->sendMsgTabPartialView($data['send_messages']);
        $data['schedual_activity_tab_partial_view'] = $this->quotationRepository->schedualActivityTabPartialView($schedule_activities, $scheduled_done_activities, $module ='quotations');
        $data['attachments_partial_view'] = $this->quotationRepository->attachmentsPartialView($attachments);
        return view('admin.sales.quotation.quotation_form')->with($data);
    }

    public function generate_quotation_pdf_save($quotation_id){
        $data['model'] = Quotation::with(
            'customer',
            'customer.contact_addresses',
            'customer.contact_addresses.contact_countries',
            'pricelist',
            'order_lines',
            'order_lines.product',
            'order_lines.variation',
            'order_lines.quotation_taxes',
            'order_lines.quotation_taxes.tax',
            'optional_products',
            'optional_products.product',
            'optional_products.variation',
            'other_info',
            'other_info.sales_person',
            'other_info.sales_team',
            'other_info.tags',
            'text_templates'
        )->where('id', $quotation_id)->first();
        $html = view('admin.sales.pdf.quotation')->with($data)->render();
        $upload_path = public_path() . '/storage/quotations/' ;
        $fileName =  'S'.str_pad($quotation_id, 5, '0', STR_PAD_LEFT). '_inv.' . 'pdf' ;
        $pdf = PDF::loadView('admin.sales.pdf.quotation', $data);
        $upload_path = public_path() . '/storage/quotations/' ;
        $fileName =  'S'.str_pad($quotation_id, 5, '0', STR_PAD_LEFT). '.' . 'pdf' ;

        if (File::exists($upload_path . $fileName)) {
            unlink($upload_path.$fileName);
        }
        if (!File::exists(public_path() . '/storage/quotations/')) {
            File::makeDirectory($upload_path, 0777, true);
        }

        $pdf->save($upload_path . $fileName);
        return $upload_path . $fileName;
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(!auth()->user()->can('Delete Quotation'))
        access_denied();

        $id = Hashids::decode($id)[0];
        Quotation::where('id', $id)->delete();

        Alert::success(__('Success'), __('Quotation has been deleted successfully!'))->persistent('Close')->autoclose(5000);

        return redirect()->route('admin.quotations.index');
    }

    /**
     * clone the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function duplicate($id)
    {
        if(!auth()->user()->can('Duplicate Quotation'))
        access_denied();

        $quotation = Quotation::where('id', $id)->first();

        // Replicate the Quotation
        $quotation_clone = $quotation->replicate();
        $quotation_clone->save();

        // Replicate the Quotation Other Info
        $quotation_clone->other_info = $quotation->other_info->replicate();
        $quotation_clone->other_info->quotation_id = $quotation_clone->id;
        $quotation_clone->other_info->save();

        // If the Quotation have other info tags
        if($quotation->other_info->tags != null)
        {
            // Replicate the Quotation Other Info tags
            foreach($quotation->other_info->tags as $tag){
                $quotation_clone->other_info->tags = $tag->replicate();
                $quotation_clone->other_info->tags->quotation_id = $quotation_clone->id;
                $quotation_clone->other_info->tags->save();

            }
        }

        //Replicate the Text Templates
        if($quotation->text_templates != null){
            foreach($quotation->text_templates as $tt){
                $quotation_clone->text_templates = $tt->replicate();
                $quotation_clone->text_templates->quotation_id = $quotation_clone->id;
                $quotation_clone->text_templates->save();
            }
        }

        //Replicate the Optional Products
        if($quotation->optional_products != null){
            foreach($quotation->optional_products as $op){
                $quotation_clone->optional_products = $op->replicate();
                $quotation_clone->optional_products->quotation_id = $quotation_clone->id;
                $quotation_clone->optional_products->save();
            }
        }


        //Replicate the Order Line Products
        if($quotation->order_lines != null){
            foreach($quotation->order_lines as $quotation_order_line){
                $quotation_clone->order_lines = $quotation_order_line->replicate();
                $quotation_clone->order_lines->quotation_id = $quotation_clone->id;
                $quotation_clone->order_lines->save();

                if($quotation_order_line->quotation_taxes != null){
                    foreach($quotation_order_line->quotation_taxes as $quotation_order_line_tax){
                        $quotation_clone->order_lines->quotation_taxes = $quotation_order_line_tax->replicate();
                        $quotation_clone->order_lines->quotation_taxes->quotation_order_line_id = $quotation_clone->order_lines->id;
                        $quotation_clone->order_lines->quotation_taxes->save();
                    }
                }
            }
        }

        Alert::success(__('Success'), __('Quotation has been cloned successfully!'))->persistent('Close')->autoclose(5000);
        return redirect()->route('admin.quotations.show',Hashids::encode($quotation_clone->id));
    }

    /**
     * Save the Order Line Products with quotation_id 0.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
        */

    public function save_order_line_options(Request $request)
    {
        $input = $request->all();

        $product_json = '';
        if( $input['product_id'] != null )
        {
            $product = Products::where('id', $input['product_id'])->first();
            if( $input['variation_id'] != null )
            {
                $product_varaitions = ProductVariation::with('variation_details')->where('id', $input['variation_id'] )->first();
                $product->variation = $product_varaitions;
            }else{
                $product->variation = [];
            }
            $product_json = (string)$product;
        }

        $new_order_line = new QuotationOrderLine;
            $new_order_line->quotation_id = 0;
            $new_order_line->product_id = $input['product_id'];
            $new_order_line->variation_id = $input['variation_id'];
            $new_order_line->description = $input['description'];
            $new_order_line->qty = $input['qty'];
            $new_order_line->lead_time = $input['lead_time'];
            $new_order_line->kss =null;
            $new_order_line->unit_price = $input['unit_price'];
            $new_order_line->taxes = null;
            $new_order_line->section = $input['section'];
            $new_order_line->terms_conditions = null;
            $new_order_line->notes = $input['notes'];
            $new_order_line->product_json = $product_json;
        $new_order_line->save();

        if( isset($input['taxes']) )
        {
            foreach( $input['taxes'] as $t )
            {
                $new_order_line_tax= new QuotationOrderLineTax;
                    $new_order_line_tax->quotation_order_line_id = $new_order_line->id;
                    $new_order_line_tax->tax_id = $t;
                $new_order_line_tax->save();
            }
        }

        $order_line_ids = $request->order_line_ids;
        array_push($order_line_ids, $new_order_line->id);

        $data = $this->calculate_total_costs_order_line($order_line_ids, $input['vat_percentage']);
        $data['order_line_id'] = $new_order_line->id;
        return $data;
     }
    /**
     * Edit the Order Line Products with quotation_id 0.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update_order_line_options(Request $request)
    {
        $input = $request->all();

        $product_json = '';
        if( isset($input['product_id']) ){
            if( $input['product_id'] != null )
            {
                $product = Products::where('id', $input['product_id'])->first();
                if( $input['variation_id'] != null )
                {
                    $product_varaitions = ProductVariation::with('variation_details')->where('id', $input['variation_id'] )->first();
                    $product->variation = $product_varaitions;
                }else{
                    $product->variation = [];
                }
                $product_json = (string)$product;
            }
        }
        $order_line = QuotationOrderLine::where('id', (int)$input['order_line_id'])->first();
            $order_line->product_id = isset($input['product_id']) ? $input['product_id'] : null ;
            $order_line->variation_id = isset($input['variation_id']) ? $input['variation_id'] : null;
            $order_line->description = isset($input['description']) ? $input['description'] : null;
            $order_line->qty = isset($input['qty']) ? $input['qty'] : null;
            $order_line->delivered_qty = isset($input['delivered_qty']) ? $input['delivered_qty'] : 0;
            $order_line->invoiced_qty = isset($input['invoiced_qty']) ? $input['invoiced_qty'] : 0;
            $order_line->unit_price = isset($input['unit_price']) ? $input['unit_price'] : null;
            $order_line->section = isset($input['section']) ? $input['section'] : null;
            $order_line->notes = isset($input['notes']) ? $input['notes'] : null;
            $order_line->product_json = $product_json;
        $order_line->save();


        QuotationOrderLineTax::where('quotation_order_line_id', $input['order_line_id'])->delete();
        if( isset($input['taxes']) )
        {
            // $order_line->taxes()->sync($input['taxes']);
            foreach( $input['taxes'] as $t )
            {

                $order_line_tax= new QuotationOrderLineTax;
                    $order_line_tax->quotation_order_line_id = $order_line->id;
                    $order_line_tax->tax_id = $t;
                $order_line_tax->save();
            }
        }

        $order_line_ids = $request->order_line_ids;
        $data = $this->calculate_total_costs_order_line($order_line_ids,$input['vat_percentage']);
        $data['order_line_id'] = $order_line->id;
        return $data;
     }

     /**
     * Save optional product with quotation_id 0.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */

     public function save_optional_products(Request $request)
     {
        $input = $request->all();
        $product_json = '';
        if( $input['product_id'] != null )
        {
            $product = Products::where('id', $input['product_id'])->first();
            if( $input['variation_id'] != null )
            {
                $product_varaitions = ProductVariation::with('variation_details')->where('id', $input['variation_id'] )->first();
                $product->variation = $product_varaitions;
            }else{
                $product->variation = [];
            }
            $product_json = (string)$product;
        }

        $new_optional_product = new QuotationOptionalProduct;
            $new_optional_product->quotation_id = 0;
            $new_optional_product->product_id = $input['product_id'];
            $new_optional_product->variation_id = $input['variation_id'];
            $new_optional_product->description = $input['description'];
            $new_optional_product->qty = $input['qty'];
            $new_optional_product->unit_price = $input['unit_price'];
            $new_optional_product->product_json = $product_json;
        $new_optional_product->save();


        return $new_optional_product->id;
     }

    public function order_line_options(Request $request)
    {
        $productList = Products::with([
            'generalInformation',
            'customer_taxes',
            'variations' => function ($query) {
                $query->where('is_active', 1);
            },
            'variations.variation_details' ])->where('project_id',NULL)->where('is_active',1)->get();
        $product_attached_attribute_values = ProductAttachedAttributeValue::all();
        $products = [];
        $customer_taxes = Tax::where('applicable_on',0)->where('is_active',1)->get();
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
                $products[] = $store;
            }
            // If the product is variable product
            else
            {
                foreach( $prod->variations as $prod_variation )
                {
                    $variation_name = $prod->product_name;
                    $product_variation_detail_count = count( $prod_variation->variation_details );
                    $variation_price = isset($prod->generalInformation->sales_price) ? $prod->generalInformation->sales_price : 0;

                    if($prod_variation->variation_sales_price == null)
                    {
                        foreach( $prod_variation->variation_details as  $ind => $prod_variation_detail)
                        {
                            $pro_attached_attr_val = ProductAttachedAttributeValue::where('product_attached_atribute_id',$prod_variation_detail->product_attached_attribute_id)
                                                ->where( 'value', $prod_variation_detail->attribute_value )->first();
                            $variation_price += $pro_attached_attr_val->extra_price;
                            if( $ind == 0 )     // First element
                            {
                                $variation_name .= ' ( '.$prod_variation_detail->attribute_value;
                            }
                            elseif( $ind < $product_variation_detail_count - 1)     // In between first and last element
                            {
                                $variation_name .= ' - '.$prod_variation_detail->attribute_value;
                            }
                            else    //  Last Element
                            {
                                $variation_name .= ' - '.$prod_variation_detail->attribute_value.' )';
                            }
                        }
                    }
                    else
                    {
                        $variation_price = $prod_variation->variation_sales_price;
                    }

                    $store['product_id'] = $prod->id;
                    $store['variation_id'] = $prod_variation->id;
                    // $store['name'] = $variation_name;
                    $store['name'] = $prod->product_name.' '.$prod_variation->variation_name;
                    $store['price'] = $variation_price;
                    $taxes = isset($prod->customer_taxes[0]) ? $prod->customer_taxes : [];
                    $store['taxes'] = [];
                    foreach( $taxes as $t )
                    {
                        $store['taxes'][] = $t->id;
                    }

                    $store['taxes'] = json_encode( $store['taxes'] );

                    $products[] = $store;
                }
            }
        }
        if( isset( $request->type ) )
        {
            if( $request->type == 'optional_product' )
            {
                $html = view('admin.sales.quotation.modal-box.add-optional-product',[ 'products' => $products ,  'customer_taxes' => $customer_taxes])
                    ->render();
            }else{
                $html = view('admin.sales.quotation.modal-box.add-product',[ 'products' => $products ,  'customer_taxes' => $customer_taxes])
                    ->render();
            }
        }else{
            $html = view('admin.sales.quotation.modal-box.add-product',[ 'products' => $products ,  'customer_taxes' => $customer_taxes])
                ->render();
        }

        return response()->json([
            'html' => $html,
            'products' => $products,
        ]);
    }

    /**
     * Get the contact addresses.
     *
     * @param  int  $contact_id
     * @param  int  $type   1: invoice_address 2: deliver_address
     * @return \Illuminate\Http\Response
     */
    public function get_contact_addresses($contact_id, $type)
    {
        $data['contact_addresses'] = ContactAddress::join('contact_countries','contact_addresses.country_id','contact_countries.id')
                    ->select('contact_addresses.id','contact_addresses.contact_name','contact_addresses.street_1','contact_addresses.city','contact_countries.name as country_name','contact_countries.is_default_vat', 'contact_countries.vat_in_percentage')
                    ->where( 'contact_addresses.contact_id', $contact_id )
                    ->where('contact_addresses.type',$type)->get();
        foreach( $data['contact_addresses'] as $index => $contact_address)
        {
            $data['contact_addresses'][$index]->street_1 = translation( $contact_address->id,5,app()->getLocale(),'street_1', $contact_address->street_1);
            $data['contact_addresses'][$index]->street_2 = $contact_address->street_2 != null ? translation( $contact_address->id,5,app()->getLocale(),'street_2', $contact_address->street_2) : null;
            $data['contact_addresses'][$index]->city = translation( $contact_address->id,5,app()->getLocale(),'city', $contact_address->city);
            $data['contact_addresses'][$index]->default_vat_percentage  =  \App\Models\SiteSettings::first()->defualt_vat;
        }
        $data['sales_info'] = ContactSalesPurchase::where('contact_id', $contact_id)->first();
        $data['contact'] = Contact::join('contact_countries','contacts.country_id','contact_countries.id')
                    ->select('contact_countries.is_default_vat', 'contact_countries.vat_in_percentage','contact_countries.vat_label')
                    ->where( 'contacts.id', $contact_id )->first();
        $data['contact']->default_vat_percentage  =  \App\Models\SiteSettings::first()->defualt_vat;
        $data['contact']->vat_label  = $data['contact']->vat_label == null ? "VAT" : $data['contact']->vat_label ;
        return $data;
    }

    /**
     * Get tax details
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function get_tax_details(Request $request)
    {
        $tax_ids = $request->tax_ids;
        $taxes = [];
        if($tax_ids!=null){
            $taxes = Tax::whereIn('id', $tax_ids)->get();

        }
        return $taxes;

    }

    /**
     * Delete specified order line
     *
     * @param int $id
     *
     */

    public function delete_optional_order_line($id, Request $request){
        $order_line = QuotationOptionalProduct::where('id',$id)->first();
        if($order_line){
            QuotationOptionalProduct::where('id',$id)->delete();
            $data['status'] = 'true';
            return $data;
        }
        $data['status'] = 'false';
        $data['message'] = __('Order line not found.');
        return $data;
    }
    public function delete_order_line($id, Request $request){
        $order_line = QuotationOrderLine::where('id',$id)->first();
        if($order_line){
            if($order_line->invoiced_qty > 0){
                $data['status'] = 'false';
                $data['message'] = __('Cannot remove the order line. Some items have been invoiced.');
                return $data;
            }
            QuotationOrderLine::where('id',$id)->delete();
            $order_line_ids = $request->order_line_ids;

            $data['untaxed_total'] = 0;
            $data['total_tax'] = 0;

            if($order_line_ids != null){
                if(count($order_line_ids) > 0){
                    $pos = array_search($id, $order_line_ids);
                    unset($order_line_ids[$pos]);
                    $data = $this->calculate_total_costs_order_line($order_line_ids);
                }
            }
            $data['status'] = 'true';
            return $data;
        }
        $data['status'] = 'false';
        $data['message'] = __('Order line not found.');
        return $data;
    }

    /**
     * Calculate the subtotal/tax amount and grand total of order line
     *
     * @param array $order_line_ids
     *
     */
    public function calculate_total_costs_order_line($order_line_ids, $vat_percentage = 0)
    {
        $order_lines = QuotationOrderLine::with('quotation_taxes','quotation_taxes.tax')->whereIn('id', $order_line_ids)->get();

        $total_tax = 0;
        $untaxed_total = 0;
        $grand_total = 0;

        foreach($order_lines as $order_line)
        {
            $subtotal = $order_line->qty * $order_line->unit_price;
            $untaxed_total += $subtotal;
            $total = $subtotal;
            foreach($order_line->quotation_taxes as $o_tax)
            {
                switch($o_tax->tax->computation)
                {
                    case 0:
                        $total_tax += $o_tax->tax->amount;
                        $total += $o_tax->tax->amount;
                        break;
                    case 1:
                        $total_tax += $subtotal * $o_tax->tax->amount / 100;
                        $total += $subtotal * $o_tax->tax->amount / 100;
                        break;
                }
            }
        }

        $data['untaxed_total'] = currency_format($untaxed_total,'','',1);
        $data['total_tax'] = currency_format($total_tax + $untaxed_total * $vat_percentage / 100,'','',1);
        $data['total'] = currency_format($untaxed_total + ($total_tax + $untaxed_total * $vat_percentage / 100),'','',1);
        return $data;
    }

    /**
     *  Set Quotation Status
     *
     *  @param int $quotation_id
     *  @param int $status 0: Quotation 1: Sales Order 2: Locked 3: Quotation Sent 4: Cancelled';
     *
     */
    public function set_quotation_status($quotation_id, $status)
    {
        $status = $status;
        Quotation::where('id', $quotation_id)->update([ 'status' => $status ]);
        return 'true';
    }

    /**
     * Send Quotation / Pro-Forma Quotation Email and change the status to Quotation Sent
     *
     * @param int $quotation_id
     * @param int $type 0: Quotation 1: Pro-Forma Email
     * @param Request $request ( receipients, subject, details, files)
     */
    public function send_email( Request $request )
    {

        $input = $request->all();
        if(isset($input['send_email']['email_recipients']) && gettype($input['send_email']['email_recipients']) != 'array'){
            $input['send_email']['email_recipients'] = explode(',',$input['send_email']['email_recipients']);
        }
        else {
             Alert::warning(__('Warning'), __('Please must be select any recipient first.'))->persistent('Close')->autoclose(5000);
             return redirect()->back();
        }
        $quotation = Quotation::where('id',$input['send_email']['id'])->first();
        if($quotation->status != 1){
            $quotation->status = 3;
        }
        $quotation->save();

        $quotation_path = public_path() . '/storage/quotations/' ;
        $quotation_path .=  'S'.str_pad($input['send_email']['id'], 5, '0', STR_PAD_LEFT). '_inv.' . 'pdf';


        $attachments_list = [];

        if($request->hasfile('email_attachement'))
        {
            $upload_path = public_path() . '/storage/uploads/quotation/email/attachments/';
            if (!File::exists(public_path() . '/storage/uploads/quotation/email/attachments/')) {
                File::makeDirectory($upload_path, 0777, true);
            }
            $files = $request->file('email_attachement');
            foreach( $files as $ind => $file)
            {
                // $file = $image;
                $file_name = $file->getClientOriginalName();
                $type = $file->getClientOriginalExtension();
                $real_path = $file->getRealPath();
                $size = $file->getSize();
                $size_mbs = ($size / 1024) / 1024;
                $mime_type = $file->getMimeType();

                $file_temp_name = 'quotatison-'.$ind.'-' . time() . '.' . $type;

                $extension = $file->getClientOriginalExtension();
                $path   = $file->storeAs('quotations', $file_temp_name);
                $attachments = storage_path('app/'.$path);
                array_push($attachments_list,$attachments);
            }
        }
         // Transformation of Order Placed Email Template
        $email = $quotation->customer->email;
        $updated_content = $input['send_email']['email_body'];
        $updated_subject = $input['send_email']['email_subject'];
        EmailTemplate::where('type','send_quotation_order')->update([
        'subject' => $updated_subject ] );
        $quotation_path = $this->generate_quotation_pdf_save($input['send_email']['id']);
        array_push($attachments_list,$quotation_path);
        $email_template = EmailTemplate::where('type','send_quotation_order')->first();
        $lang = app()->getLocale();
        $email_template = transformEmailTemplateModel($email_template,$lang);
        $content = $email_template['content'];
        $subject = $email_template['subject'];
        $search = array("{{email_body_content}}","{{app_name}}");
        $replace = array($updated_content,env('APP_NAME'));
        $content = str_replace($search,$replace,$content);
        dispatch(new \App\Jobs\SendQuotationEmailJob($email,$subject,$content,$attachments_list));
        Alert::success(__('Success'), __('Quotation has been sent successfully!'))->persistent('Close')->autoclose(5000);
        // return redirect()->route('admin.quotations.edit',Hashids::encode($input['send_email']['id']));
        return redirect()->back();
    }
    /**
     * Change Quotation Status
     *
     * @param Request $request ( id, status )
     */
    public function change_status( Request $request )
    {
        $quotation = Quotation::where('id', $request->id)->first();
        $quotation->status = $request->status;
        $quotation->save();
        $all_license_generated = true;
        $licenses = [];
        if($request->status== 1)
        {

            // $all_license_generated = $this->attachLicenses($request->id);

            // if($all_license_generated)
            //     Alert::success(__('Success'), __('Status updated successfully!'))->persistent('Close')->autoclose(5000);
            // else
            //     Alert::warning(__('Warning'), __('Some Licenses are not available'))->persistent('Close')->autoclose(5000);
        }
        elseif($request->status == 2 )
        {
            // Transformation of Order Placed Email Template
            $name = $quotation->customer->name;
            $email = $quotation->customer->email;
            $order_number = "S".str_pad($quotation->id, 5, '0', STR_PAD_LEFT);
            $quotation_pdf = $this->generate_quotation_pdf_save($quotation->id);
            $quotation = $quotation;
            $email_template = EmailTemplate::where('type','quotation_order_placed')->first();
            $lang = app()->getLocale();
            $email_template = transformEmailTemplateModel($email_template,$lang);
            $content = $email_template['content'];
            $subject = $email_template['subject'];
            $search = array("{{name}}","{{order_number}}","{{app_name}}");
            $replace = array($name,$order_number,env('APP_NAME'));
            $content = str_replace($search,$replace,$content);
            dispatch(new \App\Jobs\SendOrderEmailJob($email,$subject,$content,$quotation_pdf));
        }
        else{
            Alert::success(__('Success'), __('Status updated successfully!'))->persistent('Close')->autoclose(5000);
        }

        return redirect()->route('admin.quotations.show',Hashids::encode($request->id));
    }

    /**
     * Update Prices based on the selected pricelist
     * @param Request $request ( pricelist_id - Int , orderline_ids - Array)
     *
     */
    public function update_prices(Request $request)
    {
        $user_country_id = Contact::where('id',$request->customer_id)->first()->country_id;
        $pricelist_id = $request->pricelist_id;
        $orderlineids = $request->orderlineids == null ? [] : $request->orderlineids ;
        $pricelist_query = ProductPriceList::with('rules');
        if($user_country_id && $pricelist_id != 1 ){
            $pricelist_query->leftjoin('product_pricelist_configurations','product_pricelist_configurations.pricelist_id','product_pricelists.id');
            $pricelist_query->leftjoin('contact_country_groups','product_pricelist_configurations.country_group_id','contact_country_groups.id');
            $pricelist_query->leftjoin('contact_countries_contact_countries_groups','contact_countries_contact_countries_groups.country_group_id','contact_country_groups.id');
            $pricelist_query->where(function($q) use($user_country_id){
                $q->whereHas('configuration',function($q1) use($user_country_id){
                    $q1->where('country_id', $user_country_id);
                    $q1->orWhere('country_id', null);
                });
                $q->orWhere('contact_countries_contact_countries_groups.country_id',$user_country_id);
            });
        }
        $pricelist_query->where('product_pricelists.id', $pricelist_id);
        $pricelist_query->select('product_pricelists.*');
        $pricelist = $pricelist_query->first();
        $orderlines = QuotationOrderLine::with('product','product.generalInformation','variation_details', 'variation_details.attached_attribute')->whereIn('id', $orderlineids)->get();
        if($pricelist){
            // Iterate through the given order lines
            foreach( $orderlines as $orderline )
            {
                // if the order line consists of variation
                if( $orderline->variation_id != null )
                {
                    // PriceList rule with the @pricelist_id and @orderline->variation_id
                    $pricelistrule = ProductPricelistRule::where('apply_on',3)->where('pricelist_id', $pricelist->id)->where('variation_id', $orderline->variation_id)->orderBy('id','desc')->first();
                    // If the above price list is not available find product specific rule
                    if( !$pricelistrule )
                    {
                        // PriceList rule with the @pricelist_id and @orderline->product_id
                        $pricelistrule = ProductPricelistRule::where('apply_on',2)->where('pricelist_id', $pricelist->id)->where('product_id', $orderline->product_id)->orderBy('id','desc')->first();
                    }

                    // If the above price list is not available find product category rule
                    if( !$pricelistrule )
                    {
                        // PriceList rule with the @pricelist_id and @orderline->product->generalInformation->eccomerce_category
                        $pricelistrule = ProductPricelistRule::where('apply_on',1)->where('pricelist_id', $pricelist->id)->where('category_id', $orderline->product->generalInformation->eccomerce_category)->orderBy('id','desc')->first();
                    }

                    // If the above price list is not available find all product rule
                    if( !$pricelistrule )
                    {
                        // PriceList rule with the @pricelist_id and All Products
                        $pricelistrule = ProductPricelistRule::where('apply_on',0)->where('pricelist_id', $pricelist->id)->orderBy('id','desc')->first();
                    }
                    // If there is any applicable pricelist rule for the orderline
                    if( $pricelistrule )
                    {

                        // If Price Computation is Fixed Price change the unit price of order line to the amount given
                        if( $pricelistrule->price_computation == 0 )
                        {
                            $startDate = \Carbon\Carbon::parse($pricelist->start_date);
                            $endDate = \Carbon\Carbon::parse($pricelist->end_date);
                            $check = \Carbon\Carbon::now()->between($startDate,$endDate);
                            if(
                                ( $orderline->qty >= $pricelistrule->min_qty )
                                && $check == 1
                            ){
                                QuotationOrderLine::where('id', $orderline->id)->update( [ 'unit_price' => $pricelistrule->fixed_value ] );
                            }
                        }
                        // Else if the Price Computation is percentage deduce the percentage and replace the unit price
                        elseif( $pricelistrule->price_computation == 1 )
                        {
                            $startDate = \Carbon\Carbon::parse($pricelist->start_date)->subDay();
                            $endDate = \Carbon\Carbon::parse($pricelist->end_date)->addDay();
                            $check = \Carbon\Carbon::now()->between($startDate,$endDate);

                            if(
                                ( $orderline->qty >= $pricelistrule->min_qty )
                                && $check == 1
                            ){
                                $original_price = $orderline->product->generalInformation->sales_price;
                                if($orderline->variation->variation_sales_price == null ){
                                    foreach($orderline->variation_details as $variation_detail)
                                    {
                                        $attribute_values = $variation_detail->attached_attribute->attributeValue;
                                        foreach( $attribute_values as $av ){
                                            if($av->value_id == $variation_detail->attribute_value_id )
                                            {
                                                // Add extra price for variation if any
                                                $original_price += $av->extra_price;
                                            }
                                        }
                                    }
                                }
                                else
                                {
                                    $original_price = $orderline->variation->variation_sales_price;
                                }

                                $new_price = $original_price - ( $original_price * $pricelistrule->percentage_value / 100 );
                                QuotationOrderLine::where('id', $orderline->id)->update( [ 'unit_price' => $new_price ] );
                            }
                        }
                    }else{
                        $original_price = $orderline->product->generalInformation->sales_price;
                        if($orderline->variation->variation_sales_price == null ){
                            foreach($orderline->variation_details as $variation_detail)
                            {
                                $attribute_values = $variation_detail->attached_attribute->attributeValue;
                                foreach( $attribute_values as $av ){
                                    if($av->value_id == $variation_detail->attribute_value_id )
                                    {
                                        // Add extra price for variation if any
                                        $original_price += $av->extra_price;
                                    }
                                }
                            }
                        }
                        else{
                            $original_price = $orderline->varaition->variation_sales_price;
                        }
                        QuotationOrderLine::where('id', $orderline->id)->update( [ 'unit_price' => $original_price ] );
                    }
                }
                // if the order line consists of product
                elseif( $orderline->product_id != null )
                {
                    // PriceList rule with the @pricelist_id and @orderline->product_id
                    $pricelistrule = ProductPricelistRule::where('apply_on',2)->where('pricelist_id', $pricelist->id)->where('product_id', $orderline->product_id)->orderBy('id','desc')->first();
                    // If the above price list is not available find product category rule
                    if( !$pricelistrule )
                    {
                        // PriceList rule with the @pricelist_id and @orderline->product->generalInformation->eccomerce_category
                        $pricelistrule = ProductPricelistRule::where('apply_on',1)->where('pricelist_id', $pricelist->id)->where('category_id', $orderline->product->generalInformation->eccomerce_category)->orderBy('id','desc')->first();
                    }

                    // If the above price list is not available find all product rule
                    if( !$pricelistrule )
                    {
                        // PriceList rule with the @pricelist_id and All Products
                        $pricelistrule = ProductPricelistRule::where('apply_on',0)->where('pricelist_id', $pricelist->id)->orderBy('id','desc')->first();
                    }

                    // If there is any applicable pricelist rule for the orderline
                    if( $pricelistrule )
                    {
                        // If Price Computation is Fixed Price change the unit price of order line to the amount given
                        if( $pricelistrule->price_computation == 0 )
                        {
                            $startDate = \Carbon\Carbon::parse($pricelist->start_date)->subDay();
                            $endDate = \Carbon\Carbon::parse($pricelist->end_date)->addDay();
                            $check = \Carbon\Carbon::now()->between($startDate,$endDate);
                            if(
                                ( $orderline->qty >= $pricelistrule->min_qty )
                                && $check == 1
                            ){
                                QuotationOrderLine::where('id', $orderline->id)->update( [ 'unit_price' => $pricelistrule->fixed_value ] );
                            }
                        }
                        // Else if the Price Computation is percentage deduce the percentage and replace the unit price
                        elseif( $pricelistrule->price_computation == 1 )
                        {
                            $startDate = \Carbon\Carbon::parse($pricelist->start_date)->subDay();
                            $endDate = \Carbon\Carbon::parse($pricelist->end_date)->addDay();
                            $check = \Carbon\Carbon::now()->between($startDate,$endDate);
                            if(
                                ( $orderline->qty >= $pricelistrule->min_qty )
                                && $check == 1
                            ){
                                $original_price = $orderline->product->generalInformation->sales_price;
                                if($orderline->variation->variation_sales_price == null ){
                                    foreach($orderline->variation_details as $variation_detail)
                                    {
                                        $attribute_values = $variation_detail->attached_attribute->attributeValue;
                                        foreach( $attribute_values as $av ){
                                            if($av->value_id == $variation_detail->attribute_value_id )
                                            {
                                                // Add extra price for variation if any
                                                $original_price += $av->extra_price;
                                            }
                                        }
                                    }
                                }
                                else{
                                    $original_price = $orderline->variation->variation_sales_price;
                                }
                                $new_price = $original_price - ( $original_price * $pricelistrule->percentage_value / 100 );
                                QuotationOrderLine::where('id', $orderline->id)->update( [ 'unit_price' => $new_price ] );
                            }
                        }
                    }else{
                        $original_price = $orderline->product->generalInformation->sales_price;
                        if($orderline->variation->variation_sales_price == null ){
                            foreach($orderline->variation_details as $variation_detail)
                            {
                                $attribute_values = $variation_detail->attached_attribute->attributeValue;
                                foreach( $attribute_values as $av ){
                                    if($av->value_id == $variation_detail->attribute_value_id )
                                    {
                                        // Add extra price for variation if any
                                        $original_price += $av->extra_price;
                                    }
                                }
                            }
                        }
                        else{
                            $original_price = $orderline->variation->variation_sales_price;
                        }
                        QuotationOrderLine::where('id', $orderline->id)->update( [ 'unit_price' => $original_price ] );
                    }
                }
            }
        }
        else
        {
            foreach( $orderlines as $orderline )
            {
                $original_price = $orderline->product->generalInformation->sales_price;
                if($orderline->variation->variation_sales_price == null ){
                    foreach($orderline->variation_details as $variation_detail)
                    {
                        $attribute_values = $variation_detail->attached_attribute->attributeValue;
                        foreach( $attribute_values as $av ){
                            if($av->value_id == $variation_detail->attribute_value_id )
                            {
                                // Add extra price for variation if any
                                $original_price += $av->extra_price;
                            }
                        }
                    }
                }
                else{
                    $original_price = $orderline->variation->variation_sales_price;
                }
                $new_price = $original_price ;
                QuotationOrderLine::where('id', $orderline->id)->update( [ 'unit_price' => $new_price ] );
            }
        }
        $orderlines = QuotationOrderLine::with('product','product.generalInformation','variation')->whereIn('id', $orderlineids)->get();
        return $orderlines;
    }
    public function attachLicencesPost($quotation_id)
    {
        try {
            $quotation_id = Hashids::decode($quotation_id)[0];
        } catch (\Throwable $th) {
            return redirect()->route('admin.dashboard');
        }
        $all_license_generated = $this->attachLicenses($quotation_id);
        if($all_license_generated)
            Alert::success(__('Success'), __('Status updated successfully!'))->persistent('Close')->autoclose(5000);
        else
            Alert::warning(__('Warning'), __('Some Licenses are not available'))->persistent('Close')->autoclose(5000);
        return redirect()->back();
    }
    public function attachVouchersPost($quotation_id)
    {
        try {
            $quotation_id = Hashids::decode($quotation_id)[0];
        } catch (\Throwable $th) {
            return redirect()->route('admin.dashboard');
        }
        $vouchers_generated = generateVouchers($quotation_id);
        Alert::success(__('Success'), __('Status updated successfully and vouchers attached!'))->persistent('Close')->autoclose(5000);

        return redirect()->back();
    }

    /**
     * Generate Paymnet Link
     *
     */
    public function generate_payment_link($quotation_id){
        try {
            $quotation_id = Hashids::decode($quotation_id)[0];
        } catch (\Throwable $th) {
            return redirect()->route('admin.dashboard');
        }
        $quotation = Quotation::where('id', $quotation_id)->first();
        $check_invoice = Invoice::where('quotation_id', $quotation_id)->where(function($query){
            $query->where('is_paid',1);
            $query->orWhere('is_partially_paid',1);
        })->first();
        if($check_invoice)
        {
            return redirect()->back()->with(session()->flash('alert-warning','Payment initiated manually'));
        }

        $payment = $this->generatePaymentDetails($quotation, route('admin.quotation.payment.redirect', Hashids::encode($quotation->id)));
        if($payment['success']){
            $quotation->transaction_id = $payment['payment']->id;
            $quotation->save();

            return redirect($payment['payment']->getCheckoutUrl());
        }else{
            return redirect()->back()->with(session()->flash('alert-warning',$payment['message']));
        }
    }
    public function payment_link($quotation_id)
    {
        try {
            $quotation_id = Hashids::decode($quotation_id)[0];
        } catch (\Throwable $th) {
            Alert::warning(__('Warning'), __('Invalid Order Reference'))->persistent('Close')->autoclose(5000);
            return redirect()->route('frontside.home.index');
        }

        $quotation = Quotation::where('id', $quotation_id)->first();
        if($quotation)
        {
            $check_invoice = Invoice::where('quotation_id', $quotation_id)->where(function($query){
                $query->where('is_paid',1);
                $query->orWhere('is_partially_paid',1);
            })->first();
            if($check_invoice)
            {
                Alert::warning(__('Warning'), __('Payment has been initiated manually. You cannot pay online now.'))->persistent('Close')->autoclose(5000);
                return redirect()->route('frontside.home.index');
            }

        }
        else
        {
            Alert::warning(__('Warning'), __('Invalid Order Reference'))->persistent('Close')->autoclose(5000);
            return redirect()->route('frontside.home.index');
        }
    }

    public function paymentRedirect($quotation_id)
    {
        try {
            $quotation_id = Hashids::decode($quotation_id)[0];
        } catch (\Throwable $th) {
            //throw $th;
        }
        $data['quotation'] = Quotation::where('id', $quotation_id)->first();
        if($data['quotation']){
            $payment = $this->getMolliePaymentDetail($data['quotation']->transaction_id);
            if($data['quotation']->invoice_status == 1 || !$payment['payment']->isPaid()){
                Alert::warning(__('Failure'), __('Payment not completed successfully'))->persistent('Close')->autoclose(5000);
                return redirect()->route('admin.quotations.show',Hashids::encode($quotation_id))->with(session()->flash('alert-warning', __('Something went wrong. Try again later')));
            }
            $data['quotation']->invoice_status = 1;
            $data['quotation']->status = 1;
            $data['quotation']->save();
            $vouchers_generated = generateVouchers($quotation_id);
            // dd($vouchers_generated);
            Alert::success(__('Success'), __('Status updated successfully and vouchers attached!'))->persistent('Close')->autoclose(5000);


            // Transformation of Payment Success Email Template
            $order_number = "S".str_pad($data['quotation']->id, 5, '0', STR_PAD_LEFT);
            $quotation = $data['quotation'];
            $name = $quotation->customer->name;
            $email = $quotation->customer->email;
            $transaction_id = $data['quotation']->transaction_id;
            $email_template = EmailTemplate::where('type','payment_success')->first();
            $lang = app()->getLocale();
            $email_template = transformEmailTemplateModel($email_template,$lang);
            $content = $email_template['content'];
            $subject = $email_template['subject'];
            $search = array("{{name}}","{{order_number}}","{{transaction_id}}","{{app_name}}");
            $replace = array($name,$order_number,$transaction_id,env('APP_NAME'));
            $content = str_replace($search,$replace,$content);
            dispatch(new \App\Jobs\PaymentSuccessEmailJob($email,$subject,$content,''));


            $data['invoice'] = $this->create_invoice($quotation_id);


            $payment_history = new InvoicePaymentHistory;
            $payment_history->invoice_id = $data['invoice']->id;
            $payment_history->transaction_id = $data['quotation']->transaction_id;
            $payment_history->method = "Online Payment";
            $payment_history->amount = str_replace(",","",$data['quotation']->total * $data['quotation']->exchange_rate);
            $payment_history->save();

            return redirect()->route('admin.quotations.show',Hashids::encode($quotation_id))->with(session()->flash('alert-success', __('The payment against this order has been confirmed.')));
        }
        else
        {
            return redirect()->route('frontside.home.index');
        }
    }

    public function create_invoice($quotation_id)
    {
        $quotation = Quotation::with(
                    'order_lines',
                    'order_lines.quotation_taxes',
                    'order_lines.product',
                    'order_lines.product.sales',
                    'order_lines.variation'
                )
                ->where('id', $quotation_id)->first();
        $new_invoice = null;
        // Make a new Invoice for the quotation
        $check_invoice = Invoice::where('quotation_id', $quotation_id)->where(function($query){
            $query->where('is_paid',0);
            $query->where('is_partially_paid',0);
        })->first();
        $invoice_total = 0;

        if($check_invoice){
            $new_invoice = $check_invoice;
                $new_invoice->status = 1;   // Draft
                $new_invoice->is_paid = 1;   // Not Paid
                $new_invoice->is_partially_paid = 0;   // Not Paid
                $new_invoice->invoice_total = 0;   // Will be updated below
                $new_invoice->amount_paid = 0;   // Will be updated on register payment
            $new_invoice->save();
             // Loop through all quotation Order Lines
            foreach($quotation->order_lines as $order_line){
                // If order line has product
                if( $order_line->product_id != null  ){
                    // Invoice Order Line Total
                    $invoice_order_line_total = $order_line->invoicetotal;
                    // Total Invoice Total
                    $invoice_total += $invoice_order_line_total ;

                }
            }
        }else{
            $new_invoice = new Invoice;
                $new_invoice->quotation_id = $quotation_id;
                $new_invoice->status = 1;   // Draft
                $new_invoice->is_paid = 1;   // Not Paid
                $new_invoice->is_partially_paid = 0;   // Not Paid
                $new_invoice->invoice_total = 0;   // Will be updated below
                $new_invoice->amount_paid = 0;   // Will be updated on register payment
            $new_invoice->save();

             // Loop through all quotation Order Lines
            foreach($quotation->order_lines as $order_line){
                // If order line has product
                if( $order_line->product_id != null  ){
                    // Invoice Order Line Total
                    $invoice_order_line_total = $order_line->invoicetotal;
                    // Total Invoice Total
                    $invoice_total += $invoice_order_line_total ;
                    // Product Quantity
                    $qty = 0;

                    $qty = $order_line->qty;
                    // Update the Invoiced Quantity of the Quotation Order Line
                    QuotationOrderLine::where('id', $order_line->id)->update(['invoiced_qty'=>$qty]);

                    // Create new invoice order line attached with the newly created invoice
                    $new_invoice_order_line = new InvoiceOrderLine;
                        $new_invoice_order_line->invoice_id = $new_invoice->id;     // id of the invoice created
                        $new_invoice_order_line->quotation_order_line_id = $order_line->id; // id of quotation order line
                        $new_invoice_order_line->invoiced_qty = $qty;   // Quantity of products invoiced
                        $new_invoice_order_line->amount = $invoice_order_line_total;    //  Total Amount of the Product * quantity
                    $new_invoice_order_line->save();

                }
            }
        }


        if($invoice_total > 0){
            // Update the Invoice Total in the invoice table
            $new_invoice->invoice_total = $invoice_total;
            $new_invoice->amount_paid = $invoice_total;
            $new_invoice->save();
            return $new_invoice;
        }
        return 'false';
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
    public function get_pdf($id)
    {
        try {
            $quotation_id = Hashids::decode($id)[0];
        } catch (\Throwable $th) {
            return redirect()->route('admin.dashboard');
        }
        $data['link'] = $this->generate_quotation_pdf_save($quotation_id);
        return $data;
    }

}
