<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Models\License;
use App\Models\Products;
use App\Models\Voucher;
use App\Models\User;
use App\Models\ProductVariation;
use App\Imports\BulkLicenseImport;
use Maatwebsite\Excel\Facades\Excel;
use Hashids;
use Form;
use File;
use PDF;
use Carbon\Carbon;
use Alert;
use App\Models\EmailTemplate;
use App\Models\LicenseFile;
use App\Models\SiteSettings;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class LicenseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $not_data = array(
            '67DWeddfv5fxlaPLinnsa', '3bKq2pdvaeqWG6kuxZLRF', 'QnJAddmSzUWX05LVUWBLU', 'tjQFydDoJqnJPZ9ZRvouc', 'JDendUXNOrSsTyz1iiURk', 'CVEqdid2An22sFuAiZprD' , 'YN31d5bmv4lZAw12yKjDJ',
            'qwertydvrok', 'DDBoddud1GnrYVlBto7is', 'EBddvybjUNY95a3CxnKL2', 'odicCjdElVzPOYBff6kmL', 'LGWwdc0yqsTh9XlpVWe58', 'ZOK8deb4V5cyy4bfxN5Lk' , '94ORdvLxtAciHode5YVJX',
            'SVjn7dwlZnwQQ2Mm9tGAr', 'd10vdItglNZtm8rhdQULs', 'nRAdObfiYgdaUa1lhuqUD', '10JdrBPVOhpq7zTwT5bLq', '7jIdHkNnUaGgQ4lApt3OY', 'NI5dK3Cf23Ub9Sz1VYWtA' , 'PMdG0rOJX4UiVZC0m4VyF',
            'gZdAVjvQjMsxMxDwnyCcx', 'o0r5ZsntyopuXqsOQCqT', 'LIBSn5wfeW9PGft5QFyA', '6gC4D2MLyoBtVWf8J5nc', 'jqJXf59e0hrwtQYL0Yav', 'qW1tDAIt6rOnigkgtEqa' , '6iOBrlenvfYJr4aclluk',
            'mOIkMCneV0lFUPp0CXCu', 'BxZUf2bvIZbK7IzXI9Dh', 'ndmdmv9i05sBohJ2Brar', 'UoBxzuVlPMLIdDC40FfC', '1iCpOGeDXc39hOvWYu5s', 'oImniuAeENHAIRWpaEN5' , '2OfVdFteDI4zmyxrAWAl',
            'KkV8bo461jCLEKsW5Low', 'Maqut6wmQq1EcfNu0Src', 'ZbZipO77cRgvcvLJceoE', 'dAr0Zc4pkrtgF9yYcfmE', 'fTXTE42mUlWlZqJJUpY2', 'AEGqmuTtOaSSPCxmufWw' , 'DJDRQ1yN69QOL405Yfwq',
            'DfnxiuHMpZnO9En8Emm8', 'krSlcVHLVOBAbZguZjBq', '3drwe700pBTHuDfQGs42', 'DDBodud1GnrYVlBto7is', 'Ur20DpVy6ilE22WIusqB', '417fFuIrtyoEvB39qdul' , 'uc6hXYEdJR7GcIJ3ZX4x',
            '2Q9DmX9UdYifvaszF6q9', '7jIdHkNnUaGgQ4lApt3OY', 'NI5dK3Cf23Ub9Sz1VYWtA', 'PMdG0rOJX4UiVZC0m4VyF', 'gZdAVjvQjMsxMxDwnyCcx', 'o0r5ZsntyopuXqsOQCqT' , 'LIBSn5wfeW9PGft5QFyA',
            '6gC4D2MLyoBtVWf8J5nc', 'jqJXf59e0hrwtQYL0Yav', 'qW1tDAIt6rOnigkgtEqa', '6iOBrlenvfYJr4aclluk', 'mOIkMCneV0lFUPp0CXCu', '3bKq2pdvaeqWG6kuxZLRF' , 'QnJAddmSzUWX05LVUWBLU'
        );
        if($request->ajax()){
            $data_query = License::whereNotIn('license_key',$not_data)->orderBy('updated_at','desc');
            if(isset($request->customer_name_email) && $request->customer_name_email != null){
                
                $data_query->where(function($query) use($request){
                    $query->where(function($q) use($request){
                        $q->whereHas('voucher.customer', function($q) use($request){
                            $q->where('name','LIKE','%'.$request->customer_name_email.'%');
                            $q->orWhere('email','LIKE','%'.$request->customer_name_email.'%');
                        });
                    });
                    $query->orWhere(function($q) use($request){
                        $q->WhereHas('quotation_order_line.quotation.customer', function($q) use($request){
                            $q->where('name','LIKE','%'.$request->customer_name_email.'%');
                            $q->orWhere('email','LIKE','%'.$request->customer_name_email.'%');
                        });
                    });
                });
                // $data_query->whereHas('voucher.customer', function($query) use($request){
                //     $query->where(function($q) use($request){
                //         $q->where('name','LIKE','%'.$request->customer_name_email.'%');
                //         $q->orWhere('email','LIKE','%'.$request->customer_name_email.'%');
                //     });
                // });
            }
            if(isset($request->voucher_code) && $request->voucher_code != null){

                $data_query->whereHas('voucher', function($query) use($request){
                    $query->where('code',$request->voucher_code);
                    // $query->orWhere('email','LIKE','%'.$request->customer_name_email.'%');
                });
            }
            if(isset($request->name_email) && $request->name_email != null){

                $data_query->whereHas('voucher.voucherOrder.reseller', function($query) use($request){
                    $query->where(function($q) use($request){
                        $q->where('name','LIKE','%'.$request->name_email.'%');
                        $q->orWhere('email','LIKE','%'.$request->name_email.'%');
                    });
                });
            }
            if(isset($request->product_id) && $request->product_id != null )
            {
                $data_query->where('product_id',$request->product_id);
            }
            if(isset($request->manufacturer_id) && $request->manufacturer_id != null )
            {
                $data_query->whereHas('product.manufacturer',function($query) use($request){
                    $query->where('manufacturer_id',$request->manufacturer_id);
                });
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
                $data_query->whereHas('voucher.customer',function($query) use($request){
                    $query->where('id', $request->id);
                });
            }
            if(isset($request->reseller) && $request->reseller != null )
            {
                $data_query->whereHas('voucher.voucherOrder.reseller',function($query) use($request){
                    $query->where('id', $request->id);
                });
            }
            if(isset($request->start_date) && $request->start_date != '' ){
                $data_query->whereBetween('updated_at', [Carbon::parse($request->start_date), Carbon::parse($request->end_date)->addDay()]);
            }
            // $data = $data_query->get();
            $data = $data_query;
            $data_ids = $data_query->pluck('id');

            $datatable = Datatables::of($data);
            $datatable->addColumn('input', function ($row) use($data_ids){
                if($row->is_used != 1) {
                    foreach($data_ids as $ind => $data_id){
                        $data_ids[$ind] = Hashids::encode($data_id);
                    }
                    return '<input type="checkbox" data-all-ids=\''.json_encode($data_ids).'\' class="selectedids" value="'.Hashids::encode($row->id).'">';
                }
            });
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
                    $html = @$row->voucher->voucherOrder->reseller->name . '<br>' . @$row->voucher->voucherOrder->reseller->email;
                    return $html;
                }
                return '';
            });
            $datatable->addColumn('customer_detail', function ($row) {
                if($row->voucher)
                {
                    if(@$row->voucher->customer){
                        $html = @$row->voucher->customer->name . '<br>' . @$row->voucher->customer->email;
                        return $html;
                    }
                }
                if($row->quotation_order_line)
                {
                    if(@$row->quotation_order_line->quotation->customer){
                        $html = @$row->quotation_order_line->quotation->customer->name . '<br>' . @$row->quotation_order_line->quotation->customer->email;
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
                else if($row->voucher){
                    $html = '<a target="_blank" href="' .route("admin.voucher.order.vouchers", Hashids::encode($row->voucher->voucherOrder->id)).'?code='.$row->voucher->code. '">';
                    $html .= str_replace(' ','',@$row->voucher->voucherOrder->reseller->name).'-'.str_pad(@$row->voucher->voucherOrder->id,3,'0',STR_PAD_LEFT).'-'.date('dmY',strtotime($row->voucher->voucherOrder->created_at));
                    $html .= '</a>';
                    return $html;
                }
                else if($row->channel_pilot_order_item){
                    $html = '<a target="_blank" href="' .route("admin.channel-pilot.marketplace.orders").'?orderIdExternal='.$row->channel_pilot_order_item->order->orderIdExternal. '">';
                    $html .= $row->channel_pilot_order_item->order->orderIdExternal.' ('.$row->channel_pilot_order_item->order->source.')';
                    $html .= '</a>';
                    return $html;
                }
                return '';
            });
            $datatable->addColumn('voucher_code', function ($row) {
                if($row->voucher){
                    $html = $row->voucher->code.'<br>' ;
                    $html .= '<span style="color:red" >Redeemed At: '.Carbon::parse($row->voucher->redeemed_at)->format('d-M-Y').'</span>' ;
                    return $html;
                }
                else if($row->quotation_voucher)
                {
                   $html = $row->quotation_voucher->voucher_code.'<br>' ;
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
            $datatable->addColumn('action', function ($row) {
                    $actions = '';
                    if (auth()->user()->hasAnyPermission(['License Active/Inactive','License Expired','License Mark As Read']))
                {
                    $actions .= '<div style="display:inline-flex">';
                    if($row->is_used != 1)
                    {
                        if($row->is_used == 0)
                        {
                            if($row->status != 0){
                                if(auth()->user()->can('License Active/Inactive')) {
                                    $actions .= '&nbsp;' . Form::open([
                                        'method' => 'POST',
                                        'url' => [route('admin.license.change.status',[Hashids::encode($row->id),0])],
                                        'style' => 'display:inline'
                                    ]);
                                    $actions .= Form::button(__('Inactive'), ['type' => 'submit','class' => 'btn btn-default bg-red btn-icon']);
                                    $actions .= Form::submit('Inactive', ['class' => 'hidden ']);
                                    $actions .= Form::close();
                                }
                            }
                            if($row->status != 1){
                                if(auth()->user()->can('License Active/Inactive')) {
                                    $actions .= '&nbsp;' . Form::open([
                                        'method' => 'POST',
                                        'url' => [route('admin.license.change.status',[Hashids::encode($row->id),1])],
                                        'style' => 'display:inline'
                                    ]);
                                    $actions .= Form::button(__('Activate'), ['type' => 'submit','class' => 'btn btn-default bg-green btn-icon']);
                                    $actions .= Form::submit('Activate', ['class' => 'hidden ']);
                                    $actions .= Form::close();
                                }
                            }

                        }
                        if($row->status != 2){
                            if(auth()->user()->can('License Expired')) {
                                $actions .= '&nbsp;' . Form::open([
                                    'method' => 'POST',
                                    'url' => [route('admin.license.change.status',[Hashids::encode($row->id),2])],
                                    'style' => 'display:inline'
                                ]);
                                $actions .= Form::button(__('Expired'), ['type' => 'submit','class' => 'btn btn-default btn-icon']);
                                $actions .= Form::submit('Expired', ['class' => 'hidden ']);

                                $actions .= Form::close();
                            }
                        }
                        if($row->is_used != 1){
                            if(auth()->user()->can('License Mark As Read')) {
                                $actions .= '&nbsp;' . Form::open([
                                    'method' => 'POST',
                                    'url' => [route('admin.license.change.status',[Hashids::encode($row->id),5])],
                                    'style' => 'display:inline'
                                ]);
                                $actions .= Form::button(__('Mark as used'), ['type' => 'submit','class' => 'btn btn-default btn-icon']);
                                $actions .= Form::submit('Mark as Used', ['class' => 'hidden ']);

                                $actions .= Form::close();
                            }
                        }
                        if($row->is_used != 0){
                            if(auth()->user()->can('License Mark As Read')) {
                                $actions .= '&nbsp;' . Form::open([
                                    'method' => 'POST',
                                    'url' => [route('admin.license.change.status',[Hashids::encode($row->id),4])],
                                    'style' => 'display:inline'
                                ]);
                                $actions .= Form::button(__('Mark as un-used'), ['type' => 'submit','class' => 'btn btn-default btn-icon']);
                                $actions .= Form::submit('Mark as Un-used', ['class' => 'hidden ']);

                                $actions .= Form::close();
                            }
                        }
                    }else{
                        $actions .= '&nbsp;' . Form::open([
                            'method' => 'POST',
                            'url' => ['#.'],
                            'style' => 'display:inline'
                        ]);
                        $actions .= Form::button(__('Mark as un-used'), ['disabled'=>'true','type' => 'submit','class' => 'btn btn-default btn-icon']);
                        $actions .= Form::submit('Mark as Un-used', ['class' => 'hidden ']);

                        $actions .= Form::close();
                        $actions .= '&nbsp;' . Form::open([
                            'method' => 'POST',
                            'url' => ["#."],
                            'style' => 'display:inline'
                        ]);
                        $actions .= Form::button(__('Inactive'), ['disabled'=>'true','type' => 'submit','class' => 'btn btn-default bg-red btn-icon']);
                        $actions .= Form::submit('Inactive', ['class' => 'hidden ']);
                        $actions .= Form::close();
                    }
                    $actions .= '</div>';
                }
                    return $actions;
            });
            $datatable = $datatable->rawColumns(['input','customer_detail','reseller_detail','quotation_number','statuss','action','used','voucher_code']);
            return $datatable->make(true);
        }
        // $productList = Products::with( 'generalInformation', 'customer_taxes', 'variations', 'variations.variation_details' )->whereIn('product_type', [0,2])->where('is_active', 1)->get();
        $productList = Products::with( 'generalInformation', 'customer_taxes', 'variations', 'variations.variation_details' )->whereIn('product_type', [0,2])->get();
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
        $data['resellers'] = User::whereHas('contact',function($query){
            $query->where('type',3);
        })->get();
        $data['customers'] = User::whereHas('contact',function($query){
            $query->whereIn('type',[0,2,3]);
        })->get();
        return view('admin.license.index',$data);
    }

    /**
     * License Dashboard
     *
     */
    public function dashboard()
    {

        if(!auth()->user()->can('License Dashboard'))
        access_denied();
        $not_data = array(
            '67DWeddfv5fxlaPLinnsa', '3bKq2pdvaeqWG6kuxZLRF', 'QnJAddmSzUWX05LVUWBLU', 'tjQFydDoJqnJPZ9ZRvouc', 'JDendUXNOrSsTyz1iiURk', 'CVEqdid2An22sFuAiZprD' , 'YN31d5bmv4lZAw12yKjDJ',
            'qwertydvrok', 'DDBoddud1GnrYVlBto7is', 'EBddvybjUNY95a3CxnKL2', 'odicCjdElVzPOYBff6kmL', 'LGWwdc0yqsTh9XlpVWe58', 'ZOK8deb4V5cyy4bfxN5Lk' , '94ORdvLxtAciHode5YVJX',
            'SVjn7dwlZnwQQ2Mm9tGAr', 'd10vdItglNZtm8rhdQULs', 'nRAdObfiYgdaUa1lhuqUD', '10JdrBPVOhpq7zTwT5bLq', '7jIdHkNnUaGgQ4lApt3OY', 'NI5dK3Cf23Ub9Sz1VYWtA' , 'PMdG0rOJX4UiVZC0m4VyF',
            'gZdAVjvQjMsxMxDwnyCcx', 'o0r5ZsntyopuXqsOQCqT', 'LIBSn5wfeW9PGft5QFyA', '6gC4D2MLyoBtVWf8J5nc', 'jqJXf59e0hrwtQYL0Yav', 'qW1tDAIt6rOnigkgtEqa' , '6iOBrlenvfYJr4aclluk',
            'mOIkMCneV0lFUPp0CXCu', 'BxZUf2bvIZbK7IzXI9Dh', 'ndmdmv9i05sBohJ2Brar', 'UoBxzuVlPMLIdDC40FfC', '1iCpOGeDXc39hOvWYu5s', 'oImniuAeENHAIRWpaEN5' , '2OfVdFteDI4zmyxrAWAl',
            'KkV8bo461jCLEKsW5Low', 'Maqut6wmQq1EcfNu0Src', 'ZbZipO77cRgvcvLJceoE', 'dAr0Zc4pkrtgF9yYcfmE', 'fTXTE42mUlWlZqJJUpY2', 'AEGqmuTtOaSSPCxmufWw' , 'DJDRQ1yN69QOL405Yfwq',
            'DfnxiuHMpZnO9En8Emm8', 'krSlcVHLVOBAbZguZjBq', '3drwe700pBTHuDfQGs42', 'DDBodud1GnrYVlBto7is', 'Ur20DpVy6ilE22WIusqB', '417fFuIrtyoEvB39qdul' , 'uc6hXYEdJR7GcIJ3ZX4x',
            '2Q9DmX9UdYifvaszF6q9', '7jIdHkNnUaGgQ4lApt3OY', 'NI5dK3Cf23Ub9Sz1VYWtA', 'PMdG0rOJX4UiVZC0m4VyF', 'gZdAVjvQjMsxMxDwnyCcx', 'o0r5ZsntyopuXqsOQCqT' , 'LIBSn5wfeW9PGft5QFyA',
            '6gC4D2MLyoBtVWf8J5nc', 'jqJXf59e0hrwtQYL0Yav', 'qW1tDAIt6rOnigkgtEqa', '6iOBrlenvfYJr4aclluk', 'mOIkMCneV0lFUPp0CXCu', '3bKq2pdvaeqWG6kuxZLRF' , 'QnJAddmSzUWX05LVUWBLU'
        );
        $data = [];
        $data['total_licenses'] = License::whereNotIn('license_key',$not_data)->count();
        $data['used_licenses'] = License::where('is_used',1)->whereNotIn('license_key',$not_data)->count();
        $data['un_used_licenses'] = License::where('is_used',0)->whereNotIn('license_key',$not_data)->count();
        $data['expired_licenses'] = License::where('status',2)->whereNotIn('license_key',$not_data)->count();

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

        $data['vouchers'] = Voucher::all();
        return view('admin.license.dashboard', $data);
    }

    public function downloadLicenseReportInExcel(Request $request){
        $not_data = array(
            '67DWeddfv5fxlaPLinnsa', '3bKq2pdvaeqWG6kuxZLRF', 'QnJAddmSzUWX05LVUWBLU', 'tjQFydDoJqnJPZ9ZRvouc', 'JDendUXNOrSsTyz1iiURk', 'CVEqdid2An22sFuAiZprD' , 'YN31d5bmv4lZAw12yKjDJ',
            'qwertydvrok', 'DDBoddud1GnrYVlBto7is', 'EBddvybjUNY95a3CxnKL2', 'odicCjdElVzPOYBff6kmL', 'LGWwdc0yqsTh9XlpVWe58', 'ZOK8deb4V5cyy4bfxN5Lk' , '94ORdvLxtAciHode5YVJX',
            'SVjn7dwlZnwQQ2Mm9tGAr', 'd10vdItglNZtm8rhdQULs', 'nRAdObfiYgdaUa1lhuqUD', '10JdrBPVOhpq7zTwT5bLq', '7jIdHkNnUaGgQ4lApt3OY', 'NI5dK3Cf23Ub9Sz1VYWtA' , 'PMdG0rOJX4UiVZC0m4VyF',
            'gZdAVjvQjMsxMxDwnyCcx', 'o0r5ZsntyopuXqsOQCqT', 'LIBSn5wfeW9PGft5QFyA', '6gC4D2MLyoBtVWf8J5nc', 'jqJXf59e0hrwtQYL0Yav', 'qW1tDAIt6rOnigkgtEqa' , '6iOBrlenvfYJr4aclluk',
            'mOIkMCneV0lFUPp0CXCu', 'BxZUf2bvIZbK7IzXI9Dh', 'ndmdmv9i05sBohJ2Brar', 'UoBxzuVlPMLIdDC40FfC', '1iCpOGeDXc39hOvWYu5s', 'oImniuAeENHAIRWpaEN5' , '2OfVdFteDI4zmyxrAWAl',
            'KkV8bo461jCLEKsW5Low', 'Maqut6wmQq1EcfNu0Src', 'ZbZipO77cRgvcvLJceoE', 'dAr0Zc4pkrtgF9yYcfmE', 'fTXTE42mUlWlZqJJUpY2', 'AEGqmuTtOaSSPCxmufWw' , 'DJDRQ1yN69QOL405Yfwq',
            'DfnxiuHMpZnO9En8Emm8', 'krSlcVHLVOBAbZguZjBq', '3drwe700pBTHuDfQGs42', 'DDBodud1GnrYVlBto7is', 'Ur20DpVy6ilE22WIusqB', '417fFuIrtyoEvB39qdul' , 'uc6hXYEdJR7GcIJ3ZX4x',
            '2Q9DmX9UdYifvaszF6q9', '7jIdHkNnUaGgQ4lApt3OY', 'NI5dK3Cf23Ub9Sz1VYWtA', 'PMdG0rOJX4UiVZC0m4VyF', 'gZdAVjvQjMsxMxDwnyCcx', 'o0r5ZsntyopuXqsOQCqT' , 'LIBSn5wfeW9PGft5QFyA',
            '6gC4D2MLyoBtVWf8J5nc', 'jqJXf59e0hrwtQYL0Yav', 'qW1tDAIt6rOnigkgtEqa', '6iOBrlenvfYJr4aclluk', 'mOIkMCneV0lFUPp0CXCu', '3bKq2pdvaeqWG6kuxZLRF' , 'QnJAddmSzUWX05LVUWBLU'
        );
        $data['total_licenses']     = License::whereNotIn('license_key',$not_data)->count();
        $data['used_licenses']      = License::whereNotIn('license_key',$not_data)->where('is_used',1)->count();
        $data['un_used_licenses']   = License::whereNotIn('license_key',$not_data)->where('is_used',0)->count();
        $data['expired_licenses']   = License::whereNotIn('license_key',$not_data)->where('status',2)->count();

        $sheet_array[] = array();
        $sheet_array[] = [
            'Licenses Report',
        ];
        $sheet_array[] = ['Metrics', 'Value'];
        $sheet_array[] = [
            'Metrics' => 'Total License' ,
            'Value' => $data['total_licenses']
        ];
        $sheet_array[] = [
            'Metrics' => 'Used License' ,
            'Value' => $data['used_licenses']
        ];
        $sheet_array[] = [
            'Metrics' => 'Un-Used License' ,
            'Value' => $data['un_used_licenses'] - $data['expired_licenses']
        ];

        $sheet_array[] = [
            'Metrics' => 'Expired License' ,
            'Value' => $data['expired_licenses']
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
        $old_file = public_path().'/storage/licenses/License Report.xlsx';
        if (file_exists($old_file)) {
            unlink($old_file);
        }
        ob_clean();
        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="License Report.xlsx"');
        return $writer->save('php://output');
        return public_path('storage/licenses/License Report.xlsx');

    }
    /**
     * Import License Keys
     *
     */

    public function importLicenseKeys(Request $request)
    {
        $product_id = null;
        $variation_id = null;
        $duplicate = false;
        $duplicate_array = [];
        $upload_path = public_path() . '/storage/licenses/import';
        $upload_count = 0;
        if (!File::exists(public_path() . '/storage/licenses/import/')) {
            File::makeDirectory($upload_path, 0777, true);
        }
        if (!empty($request->files) && $request->hasFile('file')) {
            foreach($request->file('file') as $file_u){
                $file = $file_u;
                $file_name = $file->getClientOriginalName();
                $type = $file->getClientOriginalExtension();
                $file_name = str_replace('.'.$type,'',$file_name);

                if ( $type == 'csv' ) {
                    $file_name .= '.'.$type;
                    $old_file = public_path() . '/storage/licenses/import/' . $file_name;
                    if (file_exists($old_file)) {
                        //delete previous file
                        unlink($old_file);
                    }
                    $file_u->storeAs('public/licenses/import',$file_name);

                    $file = public_path('storage/licenses/import/'.$file_name);
                    $importArr = $this->csvToArray($file);
                    // dd($importArr,$file, file_exists($filename));
                    foreach($importArr as $index =>  $row)
                    {
                        $product = Products::whereHas('variations', function($query) use($row){
                            $query->where('sku', $row['sku']);
                        })->first();
                        $variation = ProductVariation::where('sku', $row['sku'])->first();
                        if($product){
                            $product_id = $product->id;
                            $variation_id = $variation ? $variation->id : null;
                            $check_key = License::where('license_key', $row['license_key'])->where('product_id', $product_id)->first();
                            if($check_key){
                                $duplicate = true;
                                $duplicate_array[] = $row['license_key'];
                                $importArr[$index]['is_duplicate'] = 1;
                            }else{
                                $importArr[$index]['is_duplicate'] = 0;
                                $license = new License;
                                $license->license_key = $row['license_key'];
                                $license->product_id = $product_id;
                                $license->variation_id = $variation_id;
                                $license->status = isset($row['status']) ? $row['status'] : 0;
                                $license->is_used = isset($row['usage']) ? $row['usage'] : 0;
                                $license->save();
                                $upload_count = $upload_count + 1;
                            }
                        }else{
                            $importArr[$index]['is_duplicate'] = 0;
                           
                        }
                    }
                    $header = array('license_key','status','sku','is_duplicate');
                    array_unshift($importArr , $header);
                    // array_push($saveArray, $importArr);
                    $license_file = new LicenseFile;
                    $license_file->file_name = $file_name;
                    $license_file->save();

                    $file_name = $file_name.'_'.Hashids::encode($license_file->id).'.'.$type;

                    $license_file->file_name = $file_name;
                    $license_file->save();

                    $file = public_path('storage/licenses/import/'.$file_name);
                    $this->array2csv($file, $importArr);
                }
            }
            $updated_variant_license_count = License::where('product_id', $product_id)
                ->where('variation_id', $variation_id)->where('is_used', '!=', 1)->count();
            $updated_product_license_count = License::where('product_id', $product_id)
                ->where('variation_id', null)->where('is_used', '!=', 1)->count();
            if($updated_variant_license_count > 100){
                ProductVariation::where('id', $variation_id)->update([
                    'license_keys_notify_flag' => 0,
                    'last_low_key_notify_time' => NULL
                ]);
            }
            if($updated_product_license_count > 100){
                Products::where('id', $product_id)->update([
                    'license_keys_notify_flag' => 0,
                    'last_low_key_notify_time' => NULL
                ]);
            }
            if($duplicate== true){

                Alert::warning(__('Warning'), $upload_count.' '.__('Licenses Imported. Duplicate entries skipped.'))->persistent('Close')->autoclose(5000);
            }
            return back()->with(session()->flash('alert-success', $duplicate== true? $upload_count.' '.__('Licenses Imported. Duplicate entries skipped.'): __('Licenses Imported Successfully.')));
        }
        return redirect()->back()->with(session()->flash('alert-error', __('Something went wrong. Try again later.')));

    }
    public function csvToArray($filename = '', $delimiter = ',')
    {
        if (!file_exists($filename) || !is_readable($filename))
            return false;

        $header = null;
        $data = array();
        if (($handle = fopen($filename, 'r')) !== false)
        {
            $i=0;
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false)
            {
                
                if (!$header){
                    $header = $row;
                }
                else{
                    try {
                        $data[] = array_combine($header, $row);
                    } catch (\Throwable $th) {

                    }
                }
                $i++;
                
               
            }
            fclose($handle);
        }

        return $data;
    }
    public function array2csv($file_path, $data, $delimiter = ',', $enclosure = '"', $escape_char = "\\")
    {
        $f = fopen($file_path, 'a');
        // dd($file_path);
        if ($f === false) {
            die('Error opening the file ' . $file_path);
        }
        foreach ($data as $item) {
            fputcsv($f, $item, $delimiter, $enclosure, $escape_char);
        }
        fclose($f);
        // return stream_get_contents($f);
    }

    /**
     * $status 0,1,2
     * $status 4,5 usage
     */
    public function changeStatus($license_id, $status)
    {
        try {
            $license_id = Hashids::decode($license_id)[0];
        } catch (\Throwable $th) {
            return redirect()->back()->with(session()->flash('alert-error',__('Something went wrong. Try again later.')));
        }

        if($status == 0 || $status == 1 || $status == 2)
            License::where('id', $license_id)->update(['status'=>$status]);
        if($status == 4 || $status == 5 )
            License::where('id', $license_id)->update(['is_used'=>$status == 4 ? 0 : 1]);
        return redirect()->back()->with(session()->flash('alert-success','Status updated successfully.'));

    }
    public function changeBulkStatus( Request $request)
    {
        $ids = explode(';',$request->ids);
        try {
            $status = $request->statuss;
            foreach($ids as $key => $id){
                $ids[$key] = Hashids::decode($id)[0];
            }
        } catch (\Throwable $th) {
            return redirect()->back()->with(session()->flash('alert-error',__('Something went wrong. Try again later.')));
        }

        if($status == 0 || $status == 1 || $status == 2)
            License::whereIn('id', $ids)->update(['status'=>$status]);
        if($status == 4 || $status == 5 )
            License::whereIn('id', $ids)->update(['is_used'=>$status == 4 ? 0 : 1]);
        return redirect()->back()->with(session()->flash('alert-success','Status updated successfully.'));

    }

    public function licenseFileListing(Request $request)
    {
        if($request->ajax()){
            $data = LicenseFile::orderBy('id','desc');
            $datatable = Datatables::of($data);

            $datatable->addColumn('actions', function ($row) {
                $actions = '<div style="display:inline-flex">';
                $actions .= '&nbsp;' . Form::open([
                    'method' => 'POST',
                    'url' => [route('admin.license.file.delete',[ Hashids::encode( $row->id )] )],
                    'style' => 'display:inline'
                ]);
                $actions .= Form::button(__('Remove File'), ['type' => 'submit','class' => 'status-action-btn btn btn-danger btn-icon']);
                $actions .= Form::submit('Remove File', ['class' => 'hidden deleteSubmit']);
                $actions .= Form::close();
                $actions .= '&nbsp;<a data-toggle="modal" data-target="#view_file_modal" data-id="'.Hashids::encode( $row->id ).'" class="btn btn-icon btn-success view-btn" target="_blank"/><i class="fa fa-eye"></i></a>' ;
                $actions .= '</div>';
                return $actions;
            });
            $datatable = $datatable->rawColumns(['actions']);
            return $datatable->make(true);
        }
        return view('admin.license.license_files');
    }

    public function deleteLicenseFile($id){
        try {
            $id = Hashids::decode($id)[0];
        } catch (\Throwable $th) {
            //throw $th;
        }

        $license_file = LicenseFile::where('id', $id)->first();
        $file_name = $license_file->file_name;

        $file = public_path() . '/storage/licenses/import/' . $file_name;
        if (!file_exists($file)) {
            return redirect()->back()->with(session()->flash('alert-error', __('File does not exist. Deleted the file.')));
        }
        $file = public_path('storage/licenses/import/'.$file_name);
        $fileDataArray = $this->csvToArray($file);
        foreach($fileDataArray as $row)
        {
            if($row['is_duplicate'] ==0){
                $license = License::where('license_key',$row['license_key'])->first();
                if( $license && $license->is_used != 1 )
                {
                    $license->delete();
                }
            }
        }
        $license_file->delete();
        Alert::warning(__('Success'), ('Licenses file deleted with all the licenses associated.'))->persistent('Close')->autoclose(5000);
        return redirect()->back()->with(session()->flash('alert-success', __('File deleted along with the un-used licenses')));
    }

    public function viewLicenseFileContent($id)
    {
        try {
            $id = Hashids::decode($id)[0];
        } catch (\Throwable $th) {
            //throw $th;
        }

        $license_file = LicenseFile::where('id', $id)->first();
        $file_name = $license_file->file_name;
        // $html = '<table id="license" class="table table-striped table-bordered" style="width:100%">';
        // $html .= '<thead>';
        //     $html .= '<tr role="row">';
        //         $html .= '<th>License Key</th>';
        //         $html .= '<th>SKU</th>';
        //         $html .= '<th>Is Duplicate</th>';
        //     $html .= '</tr>';
        // $html .= '</thead>';
        // $html .= '<tbody>';

        $file = public_path('storage/licenses/import/'.$file_name);
        $fileDataArray = $this->csvToArray($file);
        foreach($fileDataArray as $row)
        {
            $row['is_duplicate'] = $row['is_duplicate'] == 1 ? 'YES' : 'NO';
            $row['status'] = $row['status'] == 1 ? 'Active' : 'In-Active';
            // $html .= '<tr role="row">';
            //     $html .= '<td>'.$row['license_key'].'</td>';
            //     $html .= '<td>'.$row['sku'].'</td>';
            //     $html .= '<td>'.$row['is_duplicate'].'</td>';
            // $html .= '</tr>';
        }
        $datatable = Datatables::of($fileDataArray);
        $datatable->addColumn('is_duplicate', function ($row) {
            return $row['is_duplicate'] == 1 ? 'YES' : 'NO';;
        });
        $datatable->addColumn('status', function ($row) {
            return $row['status'] == 1 ? 'Active' : 'In-Active';
        });
        return $datatable->make(true);
        // $html .= '</tbody>';
        // $html .= '</table>';
        // return $html;
    }

    /**
     * Export Licenses
     * 
     */
    public function exportLicenseInExcel(){
        $not_data = array(
            '67DWeddfv5fxlaPLinnsa', '3bKq2pdvaeqWG6kuxZLRF', 'QnJAddmSzUWX05LVUWBLU', 'tjQFydDoJqnJPZ9ZRvouc', 'JDendUXNOrSsTyz1iiURk', 'CVEqdid2An22sFuAiZprD' , 'YN31d5bmv4lZAw12yKjDJ',
            'qwertydvrok', 'DDBoddud1GnrYVlBto7is', 'EBddvybjUNY95a3CxnKL2', 'odicCjdElVzPOYBff6kmL', 'LGWwdc0yqsTh9XlpVWe58', 'ZOK8deb4V5cyy4bfxN5Lk' , '94ORdvLxtAciHode5YVJX',
            'SVjn7dwlZnwQQ2Mm9tGAr', 'd10vdItglNZtm8rhdQULs', 'nRAdObfiYgdaUa1lhuqUD', '10JdrBPVOhpq7zTwT5bLq', '7jIdHkNnUaGgQ4lApt3OY', 'NI5dK3Cf23Ub9Sz1VYWtA' , 'PMdG0rOJX4UiVZC0m4VyF',
            'gZdAVjvQjMsxMxDwnyCcx', 'o0r5ZsntyopuXqsOQCqT', 'LIBSn5wfeW9PGft5QFyA', '6gC4D2MLyoBtVWf8J5nc', 'jqJXf59e0hrwtQYL0Yav', 'qW1tDAIt6rOnigkgtEqa' , '6iOBrlenvfYJr4aclluk',
            'mOIkMCneV0lFUPp0CXCu', 'BxZUf2bvIZbK7IzXI9Dh', 'ndmdmv9i05sBohJ2Brar', 'UoBxzuVlPMLIdDC40FfC', '1iCpOGeDXc39hOvWYu5s', 'oImniuAeENHAIRWpaEN5' , '2OfVdFteDI4zmyxrAWAl',
            'KkV8bo461jCLEKsW5Low', 'Maqut6wmQq1EcfNu0Src', 'ZbZipO77cRgvcvLJceoE', 'dAr0Zc4pkrtgF9yYcfmE', 'fTXTE42mUlWlZqJJUpY2', 'AEGqmuTtOaSSPCxmufWw' , 'DJDRQ1yN69QOL405Yfwq',
            'DfnxiuHMpZnO9En8Emm8', 'krSlcVHLVOBAbZguZjBq', '3drwe700pBTHuDfQGs42', 'DDBodud1GnrYVlBto7is', 'Ur20DpVy6ilE22WIusqB', '417fFuIrtyoEvB39qdul' , 'uc6hXYEdJR7GcIJ3ZX4x',
            '2Q9DmX9UdYifvaszF6q9', '7jIdHkNnUaGgQ4lApt3OY', 'NI5dK3Cf23Ub9Sz1VYWtA', 'PMdG0rOJX4UiVZC0m4VyF', 'gZdAVjvQjMsxMxDwnyCcx', 'o0r5ZsntyopuXqsOQCqT' , 'LIBSn5wfeW9PGft5QFyA',
            '6gC4D2MLyoBtVWf8J5nc', 'jqJXf59e0hrwtQYL0Yav', 'qW1tDAIt6rOnigkgtEqa', '6iOBrlenvfYJr4aclluk', 'mOIkMCneV0lFUPp0CXCu', '3bKq2pdvaeqWG6kuxZLRF' , 'QnJAddmSzUWX05LVUWBLU'
        );
        $licenses     = License::whereNotIn('license_key',$not_data)->get();

        $sheet_array[] = array();
        $sheet_array[] = [
            'Licenses Report',
        ];
        $sheet_array[] = [
            'License', 
            'Product',
            'Reseller',
            'Status',
            'Usage',
            'Customer',
            'Voucher Code',
            'Sales Order'
        ];
        foreach($licenses as $license){
            $customer = '';
            if($license->voucher)
            {
                if(@$license->voucher->customer){
                    $customer = @$license->voucher->customer->name . '<br>' . @$license->voucher->customer->email;
                }
            }
            if($license->quotation_order_line)
            {
                if(@$license->quotation_order_line->quotation->customer){
                    $customer = @$license->quotation_order_line->quotation->customer->name . '<br>' . @$license->quotation_order_line->quotation->customer->email;
                }
            }
            $quotation = '';
            if($license->quotation_order_line){
                $quotation = '<a target="_blank" href="' .route('admin.quotations.show',Hashids::encode($license->quotation_order_line->quotation_id)). '">S'.str_pad($license->quotation_order_line->quotation_id, 5, '0', STR_PAD_LEFT).'</a>';
            }
            else if($license->voucher){
                $quotation = '<a target="_blank" href="' .route("admin.voucher.order.vouchers", Hashids::encode($license->voucher->voucherOrder->id)).'?code='.$license->voucher->code. '">';
                $quotation .= str_replace(' ','',@$license->voucher->voucherOrder->reseller->name).'-'.str_pad(@$license->voucher->voucherOrder->id,3,'0',STR_PAD_LEFT).'-'.date('dmY',strtotime($license->voucher->voucherOrder->created_at));
                $quotation .= '</a>';
            }
            else if($license->channel_pilot_order_item){
                $quotation = '<a target="_blank" href="' .route("admin.channel-pilot.marketplace.orders").'?orderIdExternal='.$license->channel_pilot_order_item->order->orderIdExternal. '">';
                $quotation .= $license->channel_pilot_order_item->order->orderIdExternal.' ('.$license->channel_pilot_order_item->order->source.')';
                $quotation .= '</a>';
            }
            $voucher_code = '';
            if($license->voucher){
                $voucher_code = $license->voucher->code.'<br>' ;
                $voucher_code .= '<span style="color:red" >Redeemed At: '.Carbon::parse($license->voucher->redeemed_at)->format('d-M-Y').'</span>' ;
            }
            else if($license->quotation_voucher)
            {
               $voucher_code = $license->quotation_voucher->voucher_code.'<br>' ;
               $voucher_code .= '<span style="color:red" >Redeemed At: '.Carbon::parse($license->quotation_voucher->redeemed_at)->format('d-M-Y').'</span>' ;
            }

            $status = '';
            if($license->status == 0)
                $status = 'Inactive';
            if($license->status == 1)
                $status = 'Active';
            if($license->status == 2)
                $status = 'Expired';
            $sheet_array[] = [
                'License' => $license->license_key, 
                'Product' => $license->product->product_name . ' ' . @$license->variation->variation_name. '( SKU : '.@$license->variation->sku.' )',
                'Reseller' => @$license->voucher->voucherOrder->reseller->name . '<br>' . @$license->voucher->voucherOrder->reseller->email,
                'Status' => $status,
                'Usage' => $license->is_used == 1 ? 'Used' : 'Un-Used' ,
                'Customer' => $customer,
                'Voucher Code' => $voucher_code,
                'Sales Order' => $quotation
            ];
        }

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
        $old_file = public_path().'/storage/licenses/License.xlsx';
        if (file_exists($old_file)) {
            unlink($old_file);
        }
        ob_clean();
        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="License Report.xlsx"');
        return $writer->save('php://output');

    }


}
