<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Hashids;
use Alert;
use DB;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use App\Models\Products;
use App\Models\ProductVariation;
use App\Models\ProductAttribute;
use App\Models\Languages;
use App\Models\ChannelpilotLog;
use App\Http\Traits\ChannelPilotFeedTrait;
use App\Libraries\ChannelPilot\SellerApi\ChannelPilotSellerAPI_v4_1;
use App\Models\ChannelPilotOrder;
use App\Models\ChannelPilotOrderItem;
use App\Models\ChannelPilotOrderItemVoucher;
use App\Models\Currency;
use App\Models\EmailTemplate;

class ChannelPilotController extends Controller
{
    use ChannelPilotFeedTrait;

    public function analytics(Request $request)
    {

        $data = [];
        $input = $request->all();
        if($request->ajax()){
            $analyticsData = channelpilot_analyticsRawData(\Carbon\Carbon::parse($input['date'])->format('Y-m-d'));
            $analyticsData = json_decode($analyticsData['message']);
            if(isset($analyticsData->data) && $analyticsData->data != null){
                if( isset($input['channel']) || isset($input['article']) ){
                    foreach($analyticsData->data as $index => $analytic){
                        if( isset($input['channel']) && !fnmatch("*".$input['channel'].'*', $analytic->channel->title)){
                            unset($analyticsData->data[$index]);
                            continue;
                        }
                        if( isset($input['article']) && !fnmatch("*".$input['article'].'*', $analytic->articleTitle)){
                            unset($analyticsData->data[$index]);
                            continue;
                        }
                    }
                }
            }

            $data['analyticsData'] = $analyticsData->data ? $analyticsData->data : [];
            $datatable = Datatables::of($data['analyticsData']);
            $datatable->addColumn('channel', function ($row) {
                return $row->channel->title;
            });
            $datatable->addColumn('sku', function ($row) {
                return $row->sku;
            });
            $datatable->addColumn('article', function ($row) {
                return $row->articleTitle;
            });
            $datatable->addColumn('category', function ($row) {
                return $row->category;
            });
            $datatable->addColumn('metrics', function ($row) {
                $html = '<strong>Clicks: <strong>'.$row->clicks.'<br>';
                $html .= '<strong>Articles Sold: <strong>'.$row->articlesSold.'<br>';
                $html .= '<strong>Orders: <strong>'.$row->orders.'<br>';
                $html .= '<strong>Revenue: <strong>'.$row->revenue.'<br>';
                $html .= '<strong>Margin: <strong>'.$row->margin.'<br>';
                $html .= '<strong>Costs: <strong>'.$row->costs.'<br>';
                return $html;
            });
            $datatable = $datatable->rawColumns(['metrics']);
            return $datatable->make(true);
        }
        return view('admin.channelpilot.analytics', $data);


    }
    public function getProductsSheetData()
    {
        $product_attributes = ProductAttribute::all()->toArray();
        $sheet_array = array();
        $attribute_array = array();

        //////// Header Start ////////
        $sorted = usort($product_attributes, function($a, $b) {
            return $a['attribute_name'] <=> $b['attribute_name'];
        });

        $header_array = array();
        array_push($header_array,'SKU');
        array_push($header_array,'Title');
        array_push($header_array,'Description');
        array_push($header_array,'Price');
        array_push($header_array,'Min Price');
        array_push($header_array,'Max Price');
        array_push($header_array,'Image');
        array_push($header_array,'Promotion Start Date');
        array_push($header_array,'Promotion End Date');
        foreach($product_attributes as $productAttribute){
            array_push($header_array,$productAttribute['attribute_name'] );
            array_push($attribute_array,$productAttribute['attribute_name'] );
        }
        array_push($header_array,'Deep Link');
        $sheet_array[] = $header_array;
        //////// Header End ////////
        //////// Body Start ////////
        $products = Products::with('variations.variation_details.attached_attribute','sales','attached_attributes')->where('is_active',1)->orderBy('product_name','asc')->get()->toArray();

        foreach($products as $product)
        {
            $product_obj = Products::with('variations.variation_details.attached_attribute','sales','attached_attributes')->where('id', $product['id'])->first();

            // If the product have variations
            if( count($product['variations']) > 0 )
            {
                foreach($product['variations'] as $index => $product_variation)
                {
                    $variation_detail_obj = ProductVariation::where('id',$product_variation['id'])->first();
                    $price =  currency_format( ( $product_obj->price_without_vat['total_price_exclusive_vat_tax'] + $variation_detail_obj->extra_price ),'','',1);
                    if($variation_detail_obj->variation_sales_price != null)
                    {
                        $price = $variation_detail_obj->variation_sales_price;
                    }

                    $body_array = array();
                    $body_array[] = $product_variation['sku']==null ? '' :$product_variation['sku'];
                    $body_array[] = $product['product_name'];
                    $body_array[] = @$product['sales']['description'] == null ? '' : @$product['sales']['description'] ;
                    // $body_array[] = number_format( ( $product_obj->price_without_vat['total_price_exclusive_vat_tax'] + $variation_detail_obj->extra_price ),2);
                    $body_array[] = $price;
                    $body_array[] = number_format( ( $product_variation['minimum_price'] ),2);
                    $body_array[] = number_format( ( $product_variation['maximum_price'] ),2);
                    $body_array[] = checkImage(asset('storage/uploads/sales-management/products/' . $product['image']), 'placeholder-products.jpg');
                    $body_array[] = $product_variation['promotion_start_date'];
                    $body_array[] = $product_variation['promotion_end_date'];

                    if(count($product_variation['variation_details']) < 2)
                    {
                        // Iterate through the attribute array
                        foreach( $attribute_array as $aatribute ){
                            // set attrbute presense to false
                            $is_attribute_present = false;
                            // Iterate through product variations
                            foreach( $product_variation['variation_details'] as $ind => $varia_detail )
                            {
                                // If the attrbuteis present set the attribute presense to true
                                if( $aatribute == $varia_detail['attached_attribute']['attribute_name'] ){
                                    $is_attribute_present = true;
                                }
                            }
                            // If the attribute is not present
                            if( !$is_attribute_present )
                            {
                                $temp = array();
                                $temp['attached_attribute'] = ['attribute_name' => $aatribute];
                                $temp['attribute_value'] = '';
                                array_push($product_variation['variation_details'], $temp);
                            }
                        }
                    }
                    $sorted = usort($product_variation['variation_details'], function($a, $b) use($product){
                        return $a['attached_attribute']['attribute_name'] <=> $b['attached_attribute']['attribute_name'];
                    });

                    foreach($product_variation['variation_details'] as $variation_detail)
                    {
                        $body_array[] = $variation_detail['attribute_value'];
                    }

                    $body_array[] = route('frontside.shop.product-details', $product['slug']);
                    $sheet_array[] = $body_array;
                }
            }
            else
            {
                $body_array = array();
                $body_array[] = '';
                $body_array[] = @$product['product_name'];
                $body_array[] = @$product['sales']['description'] == null ? '' : @$product['sales']['description'] ;
                $body_array[] = number_format( ( (double)$product_obj->price_without_vat['total_price_exclusive_vat_tax'] ),2);
                $body_array[] = number_format( ( (double)$product_obj->generalInformation->minimum_price ),2);
                $body_array[] = number_format( ( (double)$product_obj->generalInformation->maximum_price ),2);
                $body_array[] = checkImage(asset('storage/uploads/sales-management/products/' . $product['image']), 'placeholder-products.jpg');
                $body_array[] = $product_obj->generalInformation->promotion_start_date;
                $body_array[] = $product_obj->generalInformation->promotion_end_date;
                foreach($attribute_array as $attribute)
                {
                    $body_array[] = '';
                }
                $body_array[] = route('frontside.shop.product-details', @$product['slug']);
                $sheet_array[] = $body_array;
            }
        }
        return $sheet_array;
    }
    public function export_products_data($type = 'csv')
    {

        $sheet_array = $this->getProductsSheetData();

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
        if($type == 'csv')
        {
            $old_file = public_path().'/storage/products/export/product_export.csv';
            if (file_exists($old_file)) {
                unlink($old_file);
            }
            $writer = new Csv($spreadsheet);
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment; filename="product_export.csv"');
            $writer->save("php://output");
        } else if($type == 'xlsx')
        {
            $old_file = public_path().'/storage/products/export/product_export.xlsx';
            if (file_exists($old_file)) {
                unlink($old_file);
            }
            ob_clean();
            $writer = new Xlsx($spreadsheet);
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment; filename="product_export.xlsx"');
            $writer->save("php://output");
        }


    }
    public function export_feed_to_channel_pilot()
    {

        $product_attributes = ProductAttribute::all()->toArray();
        $sheet_array = array();
        $attribute_array = array();

        //////// Header Start ////////
        $sorted = usort($product_attributes, function($a, $b) {
            return $a['attribute_name'] <=> $b['attribute_name'];
        });

        $header_array = array();
        array_push($header_array,'SKU');
        array_push($header_array,'Active Status');
        array_push($header_array,'EAN');
        array_push($header_array,'GTIN');
        array_push($header_array,'Manufacturer');
        array_push($header_array,'mpn');
        array_push($header_array,'Title');
        array_push($header_array,'Description');
        array_push($header_array,'Price');
        array_push($header_array,'Min Price');
        array_push($header_array,'Max Price');
        array_push($header_array,'Image');
        array_push($header_array,'Product Images');
        array_push($header_array,'Promotion Start Date');
        array_push($header_array,'Promotion End Date');
        foreach($product_attributes as $productAttribute){
            array_push($header_array,$productAttribute['attribute_name'] );
            array_push($attribute_array,$productAttribute['attribute_name'] );
        }
        array_push($header_array,'Deep Link');
        $feed_array = array();
        //////// Header End ////////
        //////// Body Start ////////
        $products = Products::with('variations.variation_details.attached_attribute','sales','attached_attributes')->orderBy('product_name','asc')->get()->toArray();

        foreach($products as $product)
        {
            $product_obj = Products::with('variations.variation_details.attached_attribute','sales','attached_attributes')->where('id', $product['id'])->first();
            // If the product have variations
            if( count($product['variations']) > 0 )
            {
                foreach($product['variations'] as $index => $product_variation)
                {
                    $variation_detail_obj = ProductVariation::where('id',$product_variation['id'])->first();
                    $price =  currency_format( ( $product_obj->price_without_vat['total_price_exclusive_vat_tax'] + $variation_detail_obj->extra_price ),'','',1);
                    if($variation_detail_obj->variation_sales_price != null)
                    {
                        $price = $variation_detail_obj->variation_sales_price;
                    }
                    $body_array = array();
                    $body_array['sku'] = $product_variation['sku']==null ? '' :$product_variation['sku'];
                    $body_array['is_active'] = ($product['is_active'] == 1 ? ( $product_variation['is_active'] == 1 ? 'Active' : 'In-Active' ) : 'In-Active' );
                    $body_array['ean'] = $product_variation['ean']==null ? '' :$product_variation['ean'];
                    $body_array['gtin'] = $product_variation['gtin']==null ? '' :$product_variation['gtin'];
                    $body_array['manufacturer'] = @$variation_detail_obj->product->manufacturer->manufacturer_name;
                    $body_array['mpn'] =  $product_variation['mpn'] == null ? '' :$product_variation['mpn'];
                    $body_array['name'] = $product['product_name'];

                    // loop through all active language and add key desctiption_{lang_code}
                    $languages   = Languages::where('is_active',1)->orderBy('local_code')->pluck('local_code')->toArray();
                    foreach($languages as $index => $local_code){
                        if(isset($product['sales']['id'])){
                            $body_array['description_'.$local_code.''] = translation($product['sales']['id'],11,$local_code,'description',$product['sales']['description']);
                        }

                    }
                    // till here
                    $body_array['price'] = $price;
                    $body_array['minimum_price'] = number_format( ( $product_variation['minimum_price'] ),2);
                    $body_array['maximum_price'] = number_format( ( $product_variation['maximum_price'] ),2);
                    $body_array['image'] = checkImage(asset('storage/uploads/sales-management/products/' . $product['image']), 'placeholder-products.jpg');

                    $eccomerce_images = $product_obj->eccomerce_images;
                    $eccomerce_image_array = [];
                    foreach($eccomerce_images as $eccomerce_image){
                        array_push($eccomerce_image_array,url('storage/uploads/sales-management/products/eccomerce').'/'.$eccomerce_image->image);
                    }
                    $body_array['product_images'] = $eccomerce_image_array;

                    $body_array['promotion_start_date'] = $product_variation['promotion_start_date'];
                    $body_array['promotion_end_date'] = $product_variation['promotion_end_date'];
                    if(count($product_variation['variation_details']) < 2)
                    {
                        // Iterate through the attribute array
                        foreach( $attribute_array as $aatribute ){
                            // set attrbute presense to false
                            $is_attribute_present = false;
                            // Iterate through product variations
                            foreach( $product_variation['variation_details'] as $ind => $varia_detail )
                            {
                                // If the attrbuteis present set the attribute presense to true
                                if( $aatribute == $varia_detail['attached_attribute']['attribute_name'] ){
                                    $is_attribute_present = true;
                                }
                            }
                            // If the attribute is not present
                            if( !$is_attribute_present )
                            {
                                $temp = array();
                                $temp['attached_attribute'] = ['attribute_name' => $aatribute];
                                $temp['attribute_value'] = '';
                                array_push($product_variation['variation_details'], $temp);
                            }
                        }
                    }
                    $sorted = usort($product_variation['variation_details'], function($a, $b) use($product){
                        return $a['attached_attribute']['attribute_name'] <=> $b['attached_attribute']['attribute_name'];
                    });
                    foreach($product_variation['variation_details'] as $variation_detail)
                    {
                        $body_array[$variation_detail['attached_attribute']['attribute_name']] = $variation_detail['attribute_value'];
                    }

                    $body_array['deep_link'] = route('frontside.shop.product-details', $product['slug']);
                    $feed_array[] = (object)$body_array;
                }
            }
            else
            {
                $body_array['sku'] = '';
                $body_array['is_active'] = $product['is_active'] == 1 ? 'Active' : 'In-Active' ;
                $body_array['ean'] = '';
                $body_array['gtin'] = '';
                $body_array['manufacturer'] = @$product_obj->manufacturer->manufacturer_name;
                $body_array['mpn'] = '';
                $body_array['name'] = @$product['product_name'];

                $languages   = Languages::where('is_active',1)->orderBy('local_code')->pluck('local_code')->toArray();
                foreach($languages as $index => $local_code){
                    if(isset($product['sales']['id'])){
                        $body_array['description_'.$local_code.''] = translation($product['sales']['id'],11,$local_code,'description',$product['sales']['description']);
                    }

                }

                $body_array['price'] = number_format( ( (double)$product_obj->price_without_vat['total_price_exclusive_vat_tax'] ),2);
                $body_array['minimum_price'] = number_format( ( (double)$product_obj->generalInformation->minimum_price ),2);
                $body_array['maximum_price'] = number_format( ( (double)$product_obj->generalInformation->maximum_price ),2);
                $body_array['image'] = checkImage(asset('storage/uploads/sales-management/products/' . $product['image']), 'placeholder-products.jpg');
                $eccomerce_images = $product_obj->eccomerce_images;
                $eccomerce_image_array = [];
                foreach($eccomerce_images as $eccomerce_image){
                    array_push($eccomerce_image_array,url('storage/uploads/sales-management/products/eccomerce').'/'.$eccomerce_image->image);
                }
                $body_array['product_images'] = $eccomerce_image_array;

                $body_array['promotion_start_date'] = $product_obj->generalInformation->promotion_start_date;
                $body_array['promotion_end_date'] = $product_obj->generalInformation->promotion_end_date;
                foreach($attribute_array as $attribute)
                {
                    $body_array['attribute'] = '';
                }
                $body_array['deeplink'] = route('frontside.shop.product-details', @$product['slug']);
                $feed_array[] = (object)$body_array;
            }
        }
        $upload_response = $this->uploadDataIntoFeed($feed_array);


        if(isset($upload_response['response']->success) && $upload_response['response']->success){
            Alert::success(__('Success'), 'Your product feed for FeedID "'.$upload_response['response']->result->globalFeedId.'" is in '.$upload_response['response']->result->status.' state. '.$upload_response['response']->result->articles.' articles are being imported.')->persistent('Close')->autoclose(5000);
        }
        else
        {
            Alert::error(__('Error'), __('Something went wrong try again later'))->persistent('Close')->autoclose(5000);

        }
        return redirect()->back();

    }
    public function getLogs(Request $request)
    {

        if ($request->ajax()) {
            $data = ChannelpilotLog::orderBy('id','desc');
            // dd($request->all());
            if(isset($request->start_date) && $request->start_date != '' ){
                $data->whereBetween('created_at', [Carbon::parse($request->start_date), Carbon::parse($request->end_date)->addDay()]);
            }
            $datatable = Datatables::of($data);
            $datatable->editColumn('created_at', function ($row) {
                return Carbon::parse($row->created_at)->format('d-M-Y H:i:s');
            });
            // $datatable = $datatable->rawColumns(['delete_check','question','status', 'action']);
            return $datatable->make(true);
        }
        return view('admin.channelpilot.api_logs');
    }

    public function marketPlaceOrdersList(Request $request)
    {
        if ($request->ajax()) {
            $input = $request->all();
            $data = ChannelPilotOrder::orderBy('orderIdExternal','desc');
            if(isset($input['start_date']) && $input['start_date'] != ''){
                $data->whereBetween('orderTime', [Carbon::parse($request->start_date), Carbon::parse($request->end_date)->addDay()]);
            }
            if(isset($input['orderIdExternal']) && $input['orderIdExternal'] != ''){
                $data->where('orderIdExternal','LIKE','%'.$input['orderIdExternal'].'%');
            }
            if(isset($input['source']) && $input['source'] != ''){
                $data->where('source','LIKE',$input['source'].'%');
            }
            if(isset($input['product_search']) && $input['product_search'] != ''){
                $data->whereHas('items',function($q) use($input){
                    $q->where('article','LIKE','%'.$input['product_search'].'%');
                });
            }
            if(isset($input['customer_search']) && $input['customer_search'] != ''){
                // dd('a');
                $data->where(function($q) use($input){
                    $q->where('customer_name','LIKE','%'.$input['customer_search'].'%');
                    $q->orWhere('customer_email','LIKE','%'.$input['customer_search'].'%');
                });
            }
            $datatable = Datatables::of($data);
            $datatable->addColumn('customer', function ($row) {
                $html = '<strong>Name: </strong>'.$row->customer_name.'<br>';
                $html .= '<strong>Email: </strong>'.$row->customer_email.'<br>';
                $html .= $row->customer_phone ? '<strong>Phone: </strong>'.$row->customer_phone : '';
                return $html;
            });
            $datatable->addColumn('net', function ($row) {
                $currency = Currency::where('code',$row->currency)->first();
                $html = '<strong>Total Sum Order: </strong>'.$currency->symbol.' '.number_format($row->totalSumOrder_net,2).' '.$row->currency.'<br>';
                $html .= '<strong>Total Sum Items(Inclusive Discount): </strong>'.$currency->symbol.' '.number_format($row->totalSumOrderInclDiscount_net,2).' '.$row->currency.'<br>';
                return $html;
            });
            $datatable->addColumn('gross', function ($row) {
                $currency = Currency::where('code',$row->currency)->first();
                $html = '<strong>Total Sum Order: </strong>'.$currency->symbol.' '.number_format($row->totalSumOrder_gross,2).' '.$row->currency.'<br>';
                $html .= '<strong>Total Sum Items(Inclusive Discount): </strong>'.$currency->symbol.' '.number_format($row->totalSumOrderInclDiscount_gross,2).' '.$row->currency.'<br>';
                return $html;
            });
            $datatable->editColumn('vat_percentage', function ($row) {
                return number_format($row->vat_percentage,2);
            });
            $datatable->editColumn('order_time', function ($row) {
                return Carbon::parse($row->orderTime)->format('d-M-Y H:i');
            });
            $datatable->addColumn('actions', function ($row) {
                $actions = '<div style="display:inline-flex">';
                $actions .= '<a data-id="'.Hashids::encode($row->id).'" type="button" class="btn skin-green-light-btn detail_btn" data-toggle="modal" data-target="#import-license-modal"><i class="fa fa-eye"></i></a>';
                $actions .= '</div">';

                return $actions;
            });
            $datatable = $datatable->rawColumns(['customer','net','actions','gross']);
            return $datatable->make(true);
        }
        return view('admin.channelpilot.marketplace_orders');
    }
    public function marketPlaceOrdersDetail($id)
    {
        try {
            $id = Hashids::decode($id)[0];
        } catch (\Throwable $th) {
        }
        $data=[];
        $data['model'] = ChannelPilotOrder::where('id', $id)->first();
        $data['currency'] = Currency::where('code',$data['model']->currency)->first();
        $html = view('admin.channelpilot.marketplace_orders_detail_modal_view',$data)->render();
        return $html;
    }

    public function getMarketPlaceOrders(){
        $channel_pilot_account_token = env('CHANNELPILOT_MERCHANT_ID');
        $channel_pilot_shop_token    = env('CHANNELPILOT_SHOP_TOKEN');
        $api = new ChannelPilotSellerAPI_v4_1($channel_pilot_account_token, $channel_pilot_shop_token);

        $result = $api->getNewMarketplaceOrders();
        // dd($result);
        $marketPlaceOrders = $this->mapMarketPlaceOrders($result);
        $this->saveMarketPlaceOrders($marketPlaceOrders);
        return redirect()->back();
    }

    protected function mapMarketPlaceOrders($marketPlaceOrders)
    {

        $orders = [];
        foreach($marketPlaceOrders->orders as $marketPlaceOrder)
        {
            $new_order = (object)[];
            $new_order->orderIdExternal = $marketPlaceOrder->orderHeader->orderIdExternal;
            $new_order->source = $marketPlaceOrder->orderHeader->source;
            $new_order->orderTime = $marketPlaceOrder->orderHeader->orderTime;
            $new_order->purchaseOrderNumber = $marketPlaceOrder->orderHeader->purchaseOrderNumber;

            $new_order->customer = (object)[];
            $new_order->customer->name= $marketPlaceOrder->customer->nameFull;
            $new_order->customer->email= $marketPlaceOrder->customer->email;
            $new_order->customer->phone= $marketPlaceOrder->customer->phone;
            $new_order->customer->mobile= $marketPlaceOrder->customer->mobile;
            $new_order->customer->vat= $marketPlaceOrder->customer->vat;

            $new_order->addressInvoice = (object)[];
            $new_order->addressInvoice->streetFull = $marketPlaceOrder->addressInvoice->streetFull;
            $new_order->addressInvoice->city = $marketPlaceOrder->addressInvoice->city;
            $new_order->addressInvoice->state = $marketPlaceOrder->addressInvoice->state;
            $new_order->addressInvoice->phone = $marketPlaceOrder->addressInvoice->phone;

            $new_order->itemsOrdered = [];
            foreach($marketPlaceOrder->itemsOrdered as $itemsOrdered){
                $new_item_ordered = (object)[];
                $new_item_ordered->idExternal= $itemsOrdered->idExternal;
                $new_item_ordered->article= $itemsOrdered->article->title;
                $new_item_ordered->ean= $itemsOrdered->article->ean;
                $new_item_ordered->qty= $itemsOrdered->quantityOrdered;

                $new_item_ordered->costsSingle = (object)[];
                $new_item_ordered->costsSingle->gross = $itemsOrdered->costsSingle->gross;
                $new_item_ordered->costsSingle->net = $itemsOrdered->costsSingle->net;
                $new_item_ordered->costsSingle->tax = $itemsOrdered->costsSingle->tax;
                $new_item_ordered->costsSingle->taxRate = $itemsOrdered->costsSingle->taxRate;

                $new_item_ordered->costsTotal = (object)[];
                $new_item_ordered->costsTotal->gross = $itemsOrdered->costsTotal->gross;
                $new_item_ordered->costsTotal->net = $itemsOrdered->costsTotal->net;
                $new_item_ordered->costsTotal->tax = $itemsOrdered->costsTotal->tax;
                $new_item_ordered->costsTotal->taxRate = $itemsOrdered->costsTotal->taxRate;

                $new_item_ordered->discountSingle = (object)[];
                $new_item_ordered->discountSingle->gross = $itemsOrdered->discountSingle->gross;
                $new_item_ordered->discountSingle->net = $itemsOrdered->discountSingle->net;
                $new_item_ordered->discountSingle->tax = $itemsOrdered->discountSingle->tax;
                $new_item_ordered->discountSingle->taxRate = $itemsOrdered->discountSingle->taxRate;

                $new_item_ordered->discountTotal = (object)[];
                $new_item_ordered->discountTotal->gross = $itemsOrdered->discountTotal->gross;
                $new_item_ordered->discountTotal->net = $itemsOrdered->discountTotal->net;
                $new_item_ordered->discountTotal->tax = $itemsOrdered->discountTotal->tax;
                $new_item_ordered->discountTotal->taxRate = $itemsOrdered->discountTotal->taxRate;

                array_push($new_order->itemsOrdered, $new_item_ordered);
            }

            $new_order->summary = (object)[];
            $new_order->summary->currencyIso3 = $marketPlaceOrder->summary->currencyIso3;
            $new_order->summary->feeTotalNet = $marketPlaceOrder->summary->feeTotalNet;

            $new_order->summary->totalSumItems  = (object)[];
            $new_order->summary->totalSumItems->gross = $marketPlaceOrder->summary->totalSumItems->gross;
            $new_order->summary->totalSumItems->tax = $marketPlaceOrder->summary->totalSumItems->tax;
            $new_order->summary->totalSumItems->net = $marketPlaceOrder->summary->totalSumItems->net;
            $new_order->summary->totalSumItems->taxRate = $marketPlaceOrder->summary->totalSumItems->taxRate;

            $new_order->summary->totalSumItemsInclDiscount  = (object)[];
            $new_order->summary->totalSumItemsInclDiscount->gross= $marketPlaceOrder->summary->totalSumItemsInclDiscount->gross;
            $new_order->summary->totalSumItemsInclDiscount->tax= $marketPlaceOrder->summary->totalSumItemsInclDiscount->tax;
            $new_order->summary->totalSumItemsInclDiscount->net= $marketPlaceOrder->summary->totalSumItemsInclDiscount->net;
            $new_order->summary->totalSumItemsInclDiscount->taxRate= $marketPlaceOrder->summary->totalSumItemsInclDiscount->taxRate;

            $new_order->summary->totalSumOrder  = (object)[];
            $new_order->summary->totalSumOrder->gross = $marketPlaceOrder->summary->totalSumOrder->gross;
            $new_order->summary->totalSumOrder->tax = $marketPlaceOrder->summary->totalSumOrder->tax;
            $new_order->summary->totalSumOrder->net = $marketPlaceOrder->summary->totalSumOrder->net;
            $new_order->summary->totalSumOrder->taxRate = $marketPlaceOrder->summary->totalSumOrder->taxRate;

            $new_order->summary->totalSumOrderInclDiscount  = (object)[];
            $new_order->summary->totalSumOrderInclDiscount->gross = $marketPlaceOrder->summary->totalSumOrderInclDiscount->gross;
            $new_order->summary->totalSumOrderInclDiscount->tax = $marketPlaceOrder->summary->totalSumOrderInclDiscount->tax;
            $new_order->summary->totalSumOrderInclDiscount->net = $marketPlaceOrder->summary->totalSumOrderInclDiscount->net;
            $new_order->summary->totalSumOrderInclDiscount->taxRate = $marketPlaceOrder->summary->totalSumOrderInclDiscount->taxRate;

            array_push($orders, $new_order);
        }
        return $orders;
    }

    protected function saveMarketPlaceOrders($marketPlaceOrders)
    {
        foreach($marketPlaceOrders as $marketPlaceOrder)
        {
            $channel_pilot_order = new ChannelPilotOrder;
            $channel_pilot_order->orderIdExternal = $marketPlaceOrder->orderIdExternal;
            $channel_pilot_order->source = $marketPlaceOrder->source;
            $channel_pilot_order->orderTime = $marketPlaceOrder->orderTime;
            $channel_pilot_order->customer_name = $marketPlaceOrder->customer->name;
            $channel_pilot_order->customer_email = $marketPlaceOrder->customer->email;
            $channel_pilot_order->customer_phone = $marketPlaceOrder->customer->phone;
            $channel_pilot_order->customer_mobile = $marketPlaceOrder->customer->mobile;
            $channel_pilot_order->customer_street = $marketPlaceOrder->addressInvoice->streetFull;
            $channel_pilot_order->customer_city = $marketPlaceOrder->addressInvoice->city;
            $channel_pilot_order->customer_state = $marketPlaceOrder->addressInvoice->state;
            $channel_pilot_order->currency = $marketPlaceOrder->summary->currencyIso3;
            $channel_pilot_order->totalSumItems_gross = $marketPlaceOrder->summary->totalSumItems->gross;
            $channel_pilot_order->totalSumItems_net = $marketPlaceOrder->summary->totalSumItems->net;
            $channel_pilot_order->totalSumItemsInclDiscount_gross = $marketPlaceOrder->summary->totalSumItemsInclDiscount->gross;
            $channel_pilot_order->totalSumItemsInclDiscount_net = $marketPlaceOrder->summary->totalSumItemsInclDiscount->net;
            $channel_pilot_order->totalSumOrder_gross = $marketPlaceOrder->summary->totalSumOrder->gross;
            $channel_pilot_order->totalSumOrder_net = $marketPlaceOrder->summary->totalSumOrder->net;
            $channel_pilot_order->totalSumOrderInclDiscount_gross = $marketPlaceOrder->summary->totalSumOrderInclDiscount->gross;
            $channel_pilot_order->totalSumOrderInclDiscount_net = $marketPlaceOrder->summary->totalSumOrderInclDiscount->net;
            $channel_pilot_order->vat_percentage = $marketPlaceOrder->summary->totalSumItems->taxRate;
            $channel_pilot_order->save();


            foreach($marketPlaceOrder->itemsOrdered as $itemOrdered)
            {
                $channel_pilot_order_item = new ChannelPilotOrderItem;
                $channel_pilot_order_item->channel_pilot_order_id = $channel_pilot_order->id;
                $channel_pilot_order_item->idExternal = $itemOrdered->idExternal;
                $channel_pilot_order_item->article = $itemOrdered->article;
                $channel_pilot_order_item->ean = $itemOrdered->ean;
                $channel_pilot_order_item->qty = $itemOrdered->qty;
                $channel_pilot_order_item->costsSingle_gross = $itemOrdered->costsSingle->gross;
                $channel_pilot_order_item->costsSingle_net = $itemOrdered->costsSingle->net;
                $channel_pilot_order_item->discountSingle_gross = $itemOrdered->discountSingle->gross;
                $channel_pilot_order_item->discountSingle_net = $itemOrdered->discountSingle->net;
                $channel_pilot_order_item->save();
            }
            $this->generateVouchersMarketPlaceOrder($channel_pilot_order->id);
        }
    }

    protected function generateVouchersMarketPlaceOrder($order_id)
    {
        $voucher_list = '';
        $order = ChannelPilotOrder::where('id', $order_id)->first();
        foreach($order->items as $item)
        {
            $product = Products::whereHas('variations',function($q) use($item){
                $q->where('ean', $item->ean);
            })->first();
            $order_quantity = $item->qty;
            for($i = 0; $i < $order_quantity ; $i++)
            {
                $voucher_code = uniqid(mt_rand());
                $voucher = new ChannelPilotOrderItemVoucher;
                    $voucher->voucher_code = $product->prefix.$voucher_code;
                    $voucher->channel_pilot_order_item_id = $item->id;
                    $voucher->redeemed = 0;
                    $voucher->status = 1;
                $voucher->save();
                $voucher_list .= '<li>'.$voucher->voucher_code.'</li>';
            }
        }
        $voucher_list .= '</ul>';
        $name = $order->customer_name;
        $email = $order->customer_email;
        $link = route('voucher.generic.redeem.page');
        $source = $order->source;
        $order_number = $order->orderIdExternal.' from '.$source;
        $email_template = EmailTemplate::where('type','order_vouchers_email')->first();
        $lang = app()->getLocale();
        $email_template = transformEmailTemplateModel($email_template,$lang);
        $content = $email_template['content'];
        $subject = $email_template['subject'];
        $search = array("{{name}}","{{order_number}}","{{voucher_list}}","{{app_name}}","{{link}}");
        $replace = array($name,$order_number,$voucher_list,env('APP_NAME'),$link);
        $content = str_replace($search,$replace,$content);
        $file = '';
        $file = $this->generateVoucherSheetExcelChannelPilot($order->id);
        // $file = public_path('storage/quotations/vouchers/'.$order->orderIdExternal.' - '.$source.'_vouchers.xlsx');
        dispatch(new \App\Jobs\SendLicenseEmailJob($email,$subject,$content,$file));
        // dd($order);
    }

    public function generateVoucherSheetExcelChannelPilot($order_id)
    {
        $order = ChannelPilotOrder::where('id', $order_id)->first();
        $items = $order->items;
        $source = $order->source;
        $voucher_array[] = array();
        $main_platform = 'TIMmunity';
        $voucher_array[] = [
            'Order Number',
            $order->orderIdExternal.' from '.$source
        ];
        $voucher_array[] = ['#', 'Product' , 'Voucher Code'];
        $index = 1;
        foreach ($items as $item) {
            $product_name = $item->article;
            foreach($item->vouchers as $voucher){
                $voucher_array[] = [
                    '#' => $index,
                    'Product' => $product_name,
                    'Voucher Code' => $voucher->voucher_code
                ];
                $index++;
            }
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
        $old_file = public_path().'/storage/quotations/vouchers/'.$order->orderIdExternal.' - '.$source.'_vouchers.xlsx';
        if (file_exists($old_file)) {
            unlink($old_file);
        }
        ob_clean();
        $writer = new Xlsx($spreadsheet);
        // header('Content-Type: application/vnd.ms-excel');
        $writer->save(public_path().'/storage/quotations/vouchers/'.$order->orderIdExternal.' - '.$source.'_vouchers.xlsx');
        // dd('am');
        return public_path('storage/quotations/vouchers/S'.$order->orderIdExternal.' - '.$source.'_vouchers.xlsx');
    }
}
