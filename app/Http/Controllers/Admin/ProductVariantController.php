<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Interfaces\PartialViewsRepositoryInterface;
use App\Models\ProductGeneralInformation;
use App\Models\ProductAlternativeProduct;
use App\Models\ProductAttachedAttribute;
use App\Models\ProductAccessaryProduct;
use App\Models\EccomerceCategory;
use App\Models\productCategorie;
use App\Models\ProductVariation;
use App\Models\EmailTemplate;
use App\Models\productType;
use App\Models\ProductTax;
use App\Models\ProductSale;
use App\Models\Products;
use App\Models\Tax;
use App\Models\Followers;
use App\Models\ActivityAttachments;
use App\Models\ActivityMessages;
use App\Models\ActivityLogNotes;
use App\Models\ActivityTypes;
use App\Models\ScheduleActivities;
use App\Models\Contact;
use App\Models\Admin;
use App\Models\Voucher;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;
use Auth;
use Hashids;
use File;
use Image;
use Alert;
use DB;
use Form;
use App\Models\ProductAttachedAttributeValue;
use App\Models\ProductAttributeValue;
use App\Models\ProductVariationDetail;

class ProductVariantController extends Controller
{
    /**
     * @var PartialViewsRepositories.
     */
    protected $variantRepository;
    /**
     * PartialViewsRepositories Constructor.
     *
     * @param PartialViewsRepositories $variantRepository
     */
    public function __construct(PartialViewsRepositoryInterface $variantRepository)
    {
        $this->variantRepository = $variantRepository;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(!auth()->user()->can('Product Variant Listing'))
        access_denied();

        $data = [];;
        if($request->ajax())
        {
            $data = ProductVariation::orderBy('id','desc');
            if($request->product_id != null){
                $data = $data->where('product_id', Hashids::decode($request->product_id)[0]);
            }
            $datatable = Datatables::of($data);
            $datatable->addColumn('internalreference', function ($row) {
                return @$row->product->generalInformation->internal_reference;
            });
            $datatable->addColumn('productName', function ($row) {
                return @$row->product->product_name.' '.@$row->variation_name;
            });
            $datatable ->filterColumn('productName', function($query, $keyword) {
                $query->whereHas('product', function($q) use($keyword){
                      $q->where('product_name','LIKE', '%'.$keyword.'%');
               });
            });
            $datatable->addColumn('sales_price', function ($row) {
                if($row->variation_sales_price == null){
                    return number_format(@$row->product->generalInformation->sales_price + @$row->extra_price,2);
                }else{
                    return number_format(@$row->variation_sales_price,2);
                }
            });
            $datatable->addColumn('reseller_sales_price', function ($row) {
                return currency_format(@$row->reseller_sales_price,'','',1);
            });
            $datatable->addColumn('cost_price', function ($row) {
                if($row->variation_sales_price == null){
                    return number_format(@$row->product->generalInformation->cost_price,2);
                }else{
                    return number_format(@$row->variation_cost_price,2);
                }
            });
            $datatable->addColumn('action', function ($row) {
                $actions = '';
                $status = 0;
                if($row->is_active == 0){
                    $status = 1;
                }
                if (auth()->user()->hasAnyPermission(['Edit Product Variant','Import License Keys']))
                {
                    $actions = '<div style="display:inline-flex">';
                        $actions .= auth()->user()->can('Edit Product Variant') ? '<a href="'.route( 'admin.product-variant.edit', [ Hashids::encode( $row->product->id ),'variation_id' => Hashids::encode( $row->id ) ] ) .'" class="btn btn-primary">'.__('Edit') .'</a>&nbsp;' : '';
                        $actions .= auth()->user()->can('Import License Keys') ? '<button class="btn btn-warning btn-icon import-modal" data-toggle="modal" data-product-id="'. $row->product->id .'" data-variation-id="'. $row->id.' ">'. __('Import License Keys') .'</button>' : '';
                        $actions .= '&nbsp;' . Form::open([
                            'method' => 'POST',
                            'url' => [route('admin.product-variant.change.status',[Hashids::encode($row->id),$status])],
                            'style' => 'display:inline'
                        ]);
                        if($row->is_active == 0){
                            $actions .= Form::submit('Activate', ['class' => 'btn btn-success']);
                        }else{
                            $actions .= Form::submit('De-activate', ['class' => 'btn btn-danger']);
                        }

                        $actions .= Form::close();
                    $actions .= '</div>';
                }
                return $actions;
            });
            $datatable->addColumn('product_form_action', function ($row) {
                $actions = '';
                $status = 0;
                if($row->is_active == 0){
                    $status = 1;
                }
                if (auth()->user()->hasAnyPermission(['Edit Product Variant']))
                {
                    $actions = '<div style="display:inline-flex">';
                        $actions .= auth()->user()->can('Edit Product Variant') ? '<a target="_blank" href="'.route( 'admin.product-variant.edit', [ Hashids::encode( $row->product->id ),'variation_id' => Hashids::encode( $row->id ) ] ) .'" class="btn btn-primary">'.__('Edit') .'</a>&nbsp;' : '';
                        $actions .= auth()->user()->can('Edit Product Variant') ? '<a href="#." data-id="'.Hashids::encode( $row->id ).'"  class="btn btn-danger variant-delete-button">'.__('Delete') .'</a>&nbsp;' : '';
                        $actions .= '&nbsp;' . Form::open([
                            'method' => 'POST',
                            'url' => [route('admin.product-variant.change.status',[Hashids::encode($row->id),$status])],
                            'style' => 'display:inline'
                        ]);
                        if($row->is_active == 0){
                            $actions .= Form::submit('Activate', ['class' => 'btn btn-success']);
                        }else{
                            $actions .= Form::submit('De-activate', ['class' => 'btn btn-danger']);
                        }

                        $actions .= Form::close();
                    $actions .= '</div>';
                }
                return $actions;
            });
            $datatable->addColumn('status', function ($row) {
                return $row->is_active == 1? __("Active"): __("In Active / Archive");
            });

            $datatable = $datatable->rawColumns(['action','product_form_action']);
            return $datatable->make(true);
        }

        return view('admin.sales.product-variants.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = [];

        $data['id'] = null;
        $data['action'] = 'Add';
        $data['email_templates'] = EmailTemplate::all();
        $data['product_type'] = productType::all();
        $data['product_category'] = productCategorie::all();
        $data['customer_taxes'] = Tax::where('applicable_on',0)->where('is_active',1)->get();
        $data['vendor_taxes'] = Tax::where('applicable_on',1)->where('is_active',1)->get();
        $data['products'] = Products::all();;
        $data['eccomerce_categories'] = EccomerceCategory::all();;
        $data['accessary_products'] = ProductVariation::with('product','variation_details')->get();



        return view('admin.sales.product-variants.product_form')->with($data);
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
        if (isset($input['action']) && $input['action'] == 'Edit') {

            $input['updated_by'] = Auth::user()->id;

            $id = Hashids::decode($input['id']);
            $model = Products::findOrFail($id)[0];

            $upload_path = public_path() . '/storage/uploads/sales-management/products/';
            if (!File::exists(public_path() . '/storage/uploads/sales-management/products/')) {
                File::makeDirectory($upload_path, 0777, true);
            }
            if (!empty($request->files) && $request->hasFile('product_image')) {
                $file = $request->file('product_image');
                $file_name = $file->getClientOriginalName();
                $type = $file->getClientOriginalExtension();
                $real_path = $file->getRealPath();
                $size = $file->getSize();
                $size_mbs = ($size / 1024) / 1024;
                $mime_type = $file->getMimeType();

                if ($type == 'jpg' or $type == 'JPG' or $type == 'PNG' or $type == 'png' or $type == 'jpeg' or $type == 'JPEG') {
                    $file_temp_name = 'product-' . time() . '.' . $type;

                    $old_file = public_path() . '/storage/uploads/sales-management/products/'  . '/' . $model->image;
                    if (file_exists($old_file) && !empty($model->image)) {
                        //delete previous file
                        unlink($old_file);
                    }

                    $path = public_path('storage/uploads/sales-management/products/') . '/' . $file_temp_name;
                    $img = Image::make($file)->resize(300, 300, function ($constraint) {
                        $constraint->aspectRatio();
                    })->save($path);
                    $input['image'] = $file_temp_name;
                }
            }
            $model->update($input);
            ProductVariation::where('id', $request->variation_id)->update(
                [
                    'sku'=>$request->sku,
                    'ean'=>$request->ean,
                    'gtin'=>$request->gtin,
                    'mpn'=>$request->mpn,
                    'minimum_price'=>$request->minimum_price,
                    'maximum_price'=>$request->maximum_price,
                    'promotion_start_date'=>$request->promotion_start_date,
                    'promotion_end_date'=>$request->promotion_end_date,
                    'variation_sales_price'=>$request->variant_specific_information['variation_sales_price'],
                    'variation_cost_price'=>$request->variant_specific_information['variation_cost_price'],
                    'reseller_sales_price'=>$request->variant_specific_information['reseller_sales_price'],
                    'reseller_cost_price'=>$request->variant_specific_information['reseller_cost_price'],
                ]
            );
            ProductGeneralInformation::where('product_id', $id)->delete();
            ProductTax::where('product_id', $id)->delete();
            ProductSale::where('product_id', $id)->delete();

        } else {

            $model = new Products();

            $upload_path = public_path() . '/storage/uploads/sales-management/products/';
            if (!File::exists(public_path() . '/storage/uploads/sales-management/products/')) {
                File::makeDirectory($upload_path, 0777, true);
            }
            if (!empty($request->files) && $request->hasFile('product_image')) {
                $file = $request->file('product_image');
                $file_name = $file->getClientOriginalName();
                $type = $file->getClientOriginalExtension();
                $real_path = $file->getRealPath();
                $size = $file->getSize();
                $size_mbs = ($size / 1024) / 1024;
                $mime_type = $file->getMimeType();

                if ($type == 'jpg' or $type == 'JPG' or $type == 'PNG' or $type == 'png' or $type == 'jpeg' or $type == 'JPEG') {
                    $file_temp_name = 'product-' . time() . '.' . $type;

                    $old_file = public_path() . '/storage/uploads/sales-management/products/' . '/' . $model->image;

                    if (file_exists($old_file) && !empty($model->image)) {
                        //delete previous file
                        unlink($old_file);
                    }

                    $path = public_path('storage/uploads/sales-management/products/') . '/' . $file_temp_name;

                    // if ($size_mbs >= 2) {
                    //     $img = Image::make($file)->fit(300, 300)->save($path);
                    // } else {
                    //     $img = Image::make($file)->resize(300, 300)->save($path);
                    // }
                    $img = Image::make($file)->resize(300, 300, function ($constraint) {
                        $constraint->aspectRatio();
                    })->save($path);
                    $input['image'] = $file_temp_name;
                }
            }
        }
        $input['can_be_sale'] = isset($input['can_be_sale']) ? $input['can_be_sale'] : 0;
        $input['can_be_purchase'] = isset($input['can_be_purchase']) ? $input['can_be_purchase'] : 0;

        $model->fill($input)->save();

        if (!empty($request->files) && $request->hasFile('product_image')) {
            $model->image = $file_temp_name;
            $model->save();
        }
        /**    Product General Information  */
        $input['general']['product_id'] = $model->id;
        $input['general']['product_type_id'] = Hashids::decode($input['general']['product_type_id'])[0];
        $input['general']['product_category_id'] = Hashids::decode($input['general']['product_category_id'])[0];
        $input['general']['eccomerce_category'] = $input['eccomerce']['category'] != null && $input['eccomerce']['category'] != "" ? Hashids::decode($input['eccomerce']['category'])[0] : null;

        ProductGeneralInformation::create($input['general']);

        /** Vendor Taxes */
        if( isset($input['can_be_purchase']) && $input['can_be_purchase'] == 1 )
        {
            if($request->vendor != null){
                if( $input['vendor']['taxes'] != null && gettype($input['vendor']['taxes']) != 'array'){
                    $temp  = $input['vendor']['taxes'];
                    $input['vendor']['taxes'] = array();
                    $input['vendor']['taxes'][] = $temp;
                }
                foreach($input['vendor']['taxes'] as $v_tax){
                    $tax_id = Hashids::decode($v_tax)[0];

                    $product_tax = new ProductTax;
                        $product_tax->product_id = $model->id;
                        $product_tax->tax_id = $tax_id;
                        $product_tax->type = 1;
                    $product_tax->save();
                }
            }

        }
        /** Customer Taxes */
        if(isset($input['general']['customer_taxes'])){
            if( $input['general']['customer_taxes'] != null && gettype($input['general']['customer_taxes']) != 'array'){
                $temp  = $input['general']['customer_taxes'];
                $input['general']['customer_taxes'] = array();
                $input['general']['customer_taxes'][] = $temp;
            }
            foreach($input['general']['customer_taxes'] as $c_tax){
                $tax_id = Hashids::decode($c_tax)[0];

                $product_tax = new ProductTax;
                $product_tax->product_id = $model->id;
                $product_tax->tax_id = $tax_id;
                $product_tax->type = 0;
                $product_tax->save();
            }
        }

        /** Product Sales Information */
        if( isset($input['can_be_sale']) && $input['can_be_sale'] == 1 ){
            $input['sales']['product_id'] = $model->id;
            if($input['sales']['email_template_id'])
            {
                $input['sales']['email_template_id'] = Hashids::decode($input['sales']['email_template_id'])[0];
            }
            ProductSale::create($input['sales']);
        }

        /** Product Eccomerce Information */
        /** Alternative Products */
        if(isset($input['eccomerce']['alternative_products'])){
            foreach($input['eccomerce']['alternative_products'] as $a_p)
            {
                $alternative_product = new ProductAlternativeProduct;
                $alternative_product->product_id = $model->id;
                $alternative_product->alternative_product_id = Hashids::decode($a_p)[0];
                $alternative_product->save();
            }
        }
        /** Accessary Products */
        if(isset($input['eccomerce']['accessary_products'])){
            foreach($input['eccomerce']['accessary_products'] as $a_p)
            {
                $alternative_product = new ProductAccessaryProduct;
                    $alternative_product->product_id = $model->id;
                    $alternative_product->accessary_product_id = Hashids::decode($a_p)[0];
                $alternative_product->save();
            }
        }
        Alert::success(__('Success'), __('Product Variant added successfully!'))->persistent('Close')->autoclose(5000);

        return redirect()->route('admin.product-variant.index');
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function edit($id, Request $request )
    {
        if(!auth()->user()->can('Edit Product Variant'))
        access_denied();

        $input = $request->all();
        $data = [];
        $variation_id = null;
        $id = Hashids::decode($id);    // Product id
        $data['action'] = 'Edit';
        $data['id'] = $id;
        $data['email_templates'] = EmailTemplate::all();
        $data['product_type'] = productType::all();
        $data['product_category'] = productCategorie::all();
        $data['customer_taxes'] = Tax::where('applicable_on',0)->where('is_active',1)->get();
        $data['vendor_taxes'] = Tax::where('applicable_on',1)->where('is_active',1)->get();
        $data['products'] = Products::all();;
        $data['eccomerce_categories'] = EccomerceCategory::all();;
        $data['accessary_products'] = ProductVariation::with('product','variation_details')->get();
        $data['model'] = Products::with('variations.voucherOrder')->withCount('attributes')->with('variations',function($query) use($input){
            $query->where('id',Hashids::decode($input['variation_id'])[0]);
        })->find($id)[0];
        $data['voucher_count'] = Voucher::whereHas('voucherOrder', function($query) use($id){
            $query->where('product_id', $id);
        })->where('status','!=', 2)->count();
        $data['model_general_info'] = ProductGeneralInformation::where('product_id', $id)->first();
        $data['model_attached_attributes'] = ProductAttachedAttribute::with('attributeValue')->where('product_id', $id)->get();
        $data['model_sales'] = ProductSale::where('product_id', $id)->first();
        $data['model_customer_taxes'] = ProductTax::where('product_id', $id)->where('type',0)->pluck('tax_id')->toArray();
        $data['model_vendor_taxes'] = ProductTax::where('product_id', $id)->where('type',1)->pluck('tax_id')->toArray();

        if(isset( $input['variation_id'] ) )  // if the product has a variation
        {
            $variation_id = Hashids::decode($input['variation_id'])[0];    // Product id
            $data['variation'] = ProductVariation::withCount('voucherOrder')->with('variation_details')->where('id', $variation_id)->first();
            $data['voucher_count'] = Voucher::whereHas('voucherOrder', function($query) use($variation_id){
                $query->where('variation_id', $variation_id);
            })->where('status','!=', 2)->count();
        }
        // Code For Activities Section
        $log_uid = Auth::user()->id;
        $log_user_name = Auth::user()->firstname .' '. Auth::user()->lastname;
        $start_date = $data['model']->start_date;
        $end_date = $data['model']->end_date;
        $logged_in_follower_ids = Followers::where('contact_id', $log_uid)->where('variant_id', $variation_id)->where('module_type', 6)->where('follower_type', 2)->pluck('follower_id')->toArray();
        if (Contact::whereIn('id', $logged_in_follower_ids)->where('admin_id', $log_uid)->exists()) {

           $data['is_following'] = 1;
        }
        else {

            $data['is_following'] = 0;
        }
        $data['followers'] = $this->variantRepository->follower_list($variation_id, $log_uid, $module_type = 6);
        $data['send_messages'] = ActivityMessages::with('activity_message_users','activity_attachments')->where('variant_id', $variation_id)->orderBy('id','desc')->get();
        $attachments = ActivityAttachments::where('variant_id', $variation_id)->orderBy('send_msg_id','desc')->orderBy('log_note_id', 'desc')->orderBy('schedule_activity_id', 'desc')->get();
        $data['log_notes'] = ActivityLogNotes::with('log_note_users','activity_attachments')->where('variant_id', $variation_id)->orderBy('id','desc')->get();
        $recipients = Contact::where('admin_id','<>', Auth::user()->id)->orWhere('admin_id', null)->where('status', 1)->get();
        $schedule_users = Admin::where('is_active',1)->where('is_archive','<>', 1)->get();
        $schedule_activity_types = ActivityTypes::where('status',1)->get();
        $schedule_activities = ScheduleActivities::with('activity_types','schedule_by_users','assign_to_users')->where('variant_id', $variation_id)->where('status', 0)->orderBy('due_date','asc')->get();
        $scheduled_done_activities = ScheduleActivities::with('activity_types','schedule_by_users','assign_to_users','activity_attachments')->where('variant_id', $variation_id)->where('status', 1)->orderBy('id','desc')->get();
        $data['diffInDays'] = Carbon::parse($start_date)->diffInDays(Carbon::parse($end_date));
        $data['diffInMonths'] = Carbon::parse($start_date)->floatdiffInMonths(Carbon::parse($end_date));
        $data['send_messages_view'] = $this->variantRepository->sendMsgs($variation_id, $log_uid, $module ='productVariants', $log_user_name, $recipients, $module_type = 6,$log_uid);
        $data['log_notes_view'] = $this->variantRepository->logNotes($variation_id, $log_uid, $module ='productVariants', $log_user_name);
        $data['schedual_activities_view'] = $this->variantRepository->schedualActivities($variation_id, $log_uid, $module ='productVariants', $schedule_users, $schedule_activity_types, $log_uid, $module_type = 6);
        $data['notes_tab_partial_view'] = $this->variantRepository->notesTabPartialView($data['log_notes']);
        $data['send_message_tab_partial_view'] = $this->variantRepository->sendMsgTabPartialView($data['send_messages']);
        $data['schedual_activity_tab_partial_view'] = $this->variantRepository->schedualActivityTabPartialView($schedule_activities, $scheduled_done_activities, $module ='productVariants');
        $data['attachments_partial_view'] = $this->variantRepository->attachmentsPartialView($attachments);
        return view('admin.sales.product-variants.product_form')->with($data);

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
    public function destroy(Request $request, $id)
    {
        try {
            $id = Hashids::decode($id)[0];
        } catch (\Throwable $th) {
            return "false";
        }
        ProductVariation::where('id', $id)->delete();
        return "true";
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function deleteProductVariants(Request $request)
    {
        $input = $request->all();
        $variation_ids = explode(',',$input['variation_ids']);
        $product_ids = explode(',',$input['product_ids']);

        Products::whereIn('id', $product_ids)->delete();
        ProductVariation::whereIn('id', $variation_ids)->delete();

        Alert::success(__('Success'), __('Product Variants deleted successfully!'))->persistent('Close')->autoclose(5000);

        return redirect()->route('admin.product-variant.index');
    }

    public function addNewVariant(Request $request, $product_id)
    {
        $product_id = Hashids::decode($product_id)[0];
        $input = $request->all();
        $input['attributes'] = array_filter($input['attributes'], static function($var){return $var !== null;} );
        $allow_saving_new_variant = true;
        $variation_details_json = array();
        foreach($input['attributes'] as $product_attached_attribute_id => $attribute_value_id)
        {
            $product_attached_attribute = ProductAttachedAttribute::where("id",$product_attached_attribute_id)->first();
            $attribute_value = ProductAttributeValue::where('id',$attribute_value_id)->first();
            $variation_details_json[$product_attached_attribute->attribute_id] = $attribute_value->id;
            // $product_variation_detail = ProductVariationDetail::whereHas('variations',function($query)use($product_id){
            //         $query->where('product_id', $product_id);
            //     })->where('product_attached_attribute_id' , $product_attached_attribute_id)
            //     ->where('attribute_id' , $product_attached_attribute->attribute_id)
            //     ->where('attribute_value' , $attribute_value->attribute_value)
            //     ->where('attribute_value_id' , $attribute_value->id)->first();
        }
        $check_variation = ProductVariation::where('product_id', $product_id)->where('variation_detail_json',json_encode($variation_details_json))->first();
        if($check_variation){
            return 'false';
        }

        $product_variation =  new ProductVariation;
        $product_variation->product_id = $product_id;
        $product_variation->is_active = 1;
        $product_variation->sku = $input['sku'];
        $product_variation->ean = $input['ean'];
        $product_variation->gtin = $input['gtin'];
        $product_variation->mpn = $input['mpn'];
        $product_variation->variation_sales_price = $input['variation_sales_price'];
        $product_variation->variation_cost_price = $input['variation_cost_price'];
        $product_variation->reseller_sales_price = $input['reseller_sales_price'];
        $product_variation->save();
        $variation_details_json = array();
        foreach($input['attributes'] as $product_attached_attribute_id => $attribute_value_id)
        {
            $product_attached_attribute = ProductAttachedAttribute::where("id",$product_attached_attribute_id)->first();
            $attribute_value = ProductAttributeValue::where('id',$attribute_value_id)->first();

            $check_product_attached_attribute_value = ProductAttachedAttributeValue::where('product_attached_atribute_id', $product_attached_attribute_id)
                ->where('value_id', $attribute_value->id)->first();
            $product_attached_attribute_value = $check_product_attached_attribute_value;
            if(!$product_attached_attribute_value){
                $product_attached_attribute_value = new ProductAttachedAttributeValue;
                $product_attached_attribute_value->product_attached_atribute_id = $product_attached_attribute_id;
                $product_attached_attribute_value->value_id =  $attribute_value->id;
                $product_attached_attribute_value->is_active =  1;
                $product_attached_attribute_value->save();
            }
            $product_variation_detail = new ProductVariationDetail();
                $product_variation_detail->product_variation_id = $product_variation->id;
                $product_variation_detail->product_attached_attribute_id = $product_attached_attribute_id;
                $product_variation_detail->attribute_id = $product_attached_attribute->attribute_id;
                $product_variation_detail->attribute_value = $attribute_value->attribute_value;
                $product_variation_detail->attribute_value_id = $attribute_value->id;
            $product_variation_detail->save();
            // Variaiton Details Json
            $variation_details_json[$product_attached_attribute->attribute_id] = $attribute_value->id;
        }
        $product_variation->variation_detail_json = $variation_details_json;
        $product_variation->save();
        return 'true';

    }

    public function changeStatus($variant_id, $status){
        try {
            $variant_id = Hashids::decode($variant_id);
        } catch (\Throwable $th) {
            //throw $th;
        }
        if($status == 0 || $status == 1){
            $variation = ProductVariation::where('id',$variant_id)->first();
            $variation->is_active = $status;
            $variation->save();
            // if($status == 0) {
            //     foreach($variation->variation_details as $ind => $varitaion_detail){
            //         $allow_attribute_value_inactive = 1;
            //         $attribute_id = $varitaion_detail->attribute_id;
            //         $attribute_value_id = $varitaion_detail->attribute_value_id;
                    
            //         // $ProductVariations = ProductVariation::with('variation_details')->where('product_id',$variation->product_id)->get();
            //         $ProductVariations = ProductVariation::whereHas('variation_details',function($q) use($attribute_id, $attribute_value_id){
            //             $q->where('attribute_id', $attribute_id);
            //             $q->where('attribute_value_id',$attribute_value_id);
            //         })->where('product_id',$variation->product_id)->get();
            //         foreach($ProductVariations  as $v ){
            //             if($v->is_active == 1){
            //                 $allow_attribute_value_inactive = 0;
            //             }
            //         }
            //     }

            // }

            if($status == 0){
                Alert::success(__('Success'), __('Product variant deactivated successfully!'))->persistent('Close')->autoclose(5000);
            }else{
                Alert::success(__('Success'), __('Product variant activated successfully!'))->persistent('Close')->autoclose(5000);
            }
        }else{
            Alert::success(__('Warning'), __('Invalid status posted!'))->persistent('Close')->autoclose(5000);
        }
        return redirect()->back();

    }
}
