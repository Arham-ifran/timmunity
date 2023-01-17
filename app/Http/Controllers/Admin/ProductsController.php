<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Interfaces\PartialViewsRepositoryInterface;
use App\Jobs\TranslateProductDataJob;
use App\Models\ProductAttachedAttributeValue;
use App\Models\ProductSaleOptionalProduct;
use App\Models\ProductGeneralInformation;
use App\Models\ProductAlternativeProduct;
use App\Models\ProductAttachedAttribute;
use App\Models\ProductAccessaryProduct;
use App\Models\ProductVariationDetail;
use App\Models\ProductAttributeValue;
use App\Models\ProductEccomerceImage;
use App\Models\EccomerceCategory;
use App\Models\productCategorie;
use App\Models\ProductAttribute;
use App\Models\ProductVariation;
use App\Models\LanguageTranslation;
use App\Models\EmailTemplate;
use App\Models\productType;
use App\Models\ProductTax;
use App\Models\ProductSale;
use App\Models\License;
use App\Models\Voucher;
use App\Models\Products;
use App\Models\Project;
use App\Models\Services;
use App\Models\Languages;
use App\Models\Tax;
use App\Models\Followers;
use App\Models\ActivityAttachments;
use App\Models\ActivityMessages;
use App\Models\ActivityLogNotes;
use App\Models\ActivityTypes;
use App\Models\LanguageModule;
use App\Models\ScheduleActivities;
use App\Models\Quotation;
use App\Models\QuotationOrderLine;
use App\Models\Contact;
use App\Models\Admin;
use App\Models\Manufacturer;
use App\Models\ContactCountry;

use Yajra\DataTables\DataTables;
use Carbon\Carbon;
use Auth;
use Hashids;
use Form;
use File;
use Image;
use Alert;

class ProductsController extends Controller
{
    /**
     * @var PartialViewsRepositories.
     */
    protected $productRepository;
    /**
     * PartialViewsRepositories Constructor.
     *
     * @param PartialViewsRepositories $productRepository
     */
    public function __construct(PartialViewsRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(!auth()->user()->can('Products Listing'))
        access_denied();

        $data = [];

        if($request->ajax())
        {
            $data = Products::with('generalInformation')->withCount('variations')->orderBy('id','desc')->get();
            $datatable = Datatables::of($data);
            $datatable->addColumn('productName', function ($row) {
                return auth()->user()->can('Edit Product') ? '<a href="' .route('admin.products.edit', Hashids::encode( $row->id)). '">'.$row->product_name.'</a>' : $row->product_name;
            });
            $datatable->addColumn('internal_reference', function ($row) {
                return @$row->generalInformation->internal_reference;
            });
            $datatable->addColumn('sales_price', function ($row) {
                return number_format(@$row->generalInformation->sales_price, 2);
            });
            $datatable->addColumn('cost_price', function ($row) {
                return number_format(@$row->generalInformation->cost_price, 2);
            });

            $datatable->addColumn('action', function ($row) {
                $actions = '';
                if (auth()->user()->hasAnyPermission(['Edit Product','Delete Product']))
                {
                    $actions = '<div style="display:inline-flex">';
                    $actions .= auth()->user()->can('Edit Product') ? '&nbsp;<a class="btn btn-primary btn-icon" href="' . route('admin.products.edit',Hashids::encode($row->id)) . '" title='.__('Edit').'><i class="fa fa-pencil"></i></a>&nbsp;' : '';
                    if(auth()->user()->can('Delete Product')) {
                        $actions .= '&nbsp;' . Form::open([
                            'method' => 'DELETE',
                            'url' => [route('admin.products.destroy',Hashids::encode($row->id))],
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
            $datatable->addColumn('status', function ($row) {
                return $row->is_active == 1 ? __("Active"): __("In Active / Archive");
            });

            $datatable = $datatable->rawColumns(['productName','action']);
            return $datatable->make(true);
        }
        return view('admin.sales.products.index');
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!auth()->user()->can('Add Product'))
        access_denied();

        $data = [];

        $data['id'] = null;
        $data['action'] = 'Add';
        $data['email_templates'] = EmailTemplate::all();
        $data['product_type'] = productType::all();
        $data['product_category'] = productCategorie::all();
        $data['customer_taxes'] = Tax::where('applicable_on',0)->where('is_active',1)->get();
        $data['vendor_taxes'] = Tax::where('applicable_on',1)->where('is_active',1)->get();
        $data['products'] = Products::all();
        $data['eccomerce_categories'] = EccomerceCategory::all();
        $data['accessary_products'] = ProductVariation::with('product','variation_details')->get();
        $data['projects'] = Project::all();
        $data['manufacturers'] = Manufacturer::whereNull('associated_manufacturer_id')->get();
        $data['services'] = Services::all();
        return view('admin.sales.products.product_form')->with($data);
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
        $allow_translation = 1;
        if (isset($input['action']) && $input['action'] == 'Edit')
        {

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

                    // $img = Image::make($file)->resize(300, 300, function ($constraint) {
                    //     $constraint->aspectRatio();
                    // })->save($path);
                    $img = Image::make($file)->save($path);
                    $input['image'] = $file_temp_name;
                }
            }


            $model->update($input);
            if( $input['sales']['description'] == '' || $model->description == $input['sales']['description'])
            {
                $allow_translation = 0;
            }
            ProductGeneralInformation::where('product_id', $id)->delete();
            ProductTax::where('product_id', $id)->delete();
            ProductSale::where('product_id', $id)->delete();
            ProductAlternativeProduct::where('product_id', $id)->delete();
            ProductAccessaryProduct::where('product_id', $id)->delete();

        }
        else
        {

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


                    $img = Image::make($file)->save($path);
                    $input['image'] = $file_temp_name;
                }
            }
            $slug = strtolower( trim( preg_replace( '/[^A-Za-z0-9-]+/', '-', $input['product_name'] ) ) );
            $slug = preg_replace("/[^\w^.]{2,}/","$1",$slug);

            $product = Products::where('slug', $slug)->first();
            // $slug = $request->slug;
            if($product){
                while($product){
                    $slug = $slug.'-copy';
                    $page = Products::where('slug', $slug)->first();
                }
            }
            $model->slug = $slug;
        }
        $input['can_be_sale'] = isset($input['can_be_sale']) ? $input['can_be_sale'] : 0;
        $input['can_be_purchase'] = isset($input['can_be_purchase']) ? $input['can_be_purchase'] : 0;
        $input['service_id'] = isset($input['service_id']) ? $input['service_id'] : null;
        $input['project_id'] = isset($input['project_id']) ? Hashids::decode($input['project_id'])[0] : null;
        $input['prefix'] = $input['prefix'] == null ? 0 : $input['prefix'];
        $input['secondary_ids'] = '';

        if(isset($input['secondary_project_id']))
        {
            foreach($input['secondary_project_id'] as $sec_project)
            {
                $input['secondary_ids'] .= Hashids::decode($sec_project)[0].',';
            }
        }
        $model->fill($input)->save();
        $model->manufacturer_id = $request->manufacturer;
        $model->service_id = $input['service_id'];
        $model->secondary_project_ids = $input['secondary_ids'];
        $model->order_number = $input['order_number'] ? $input['order_number'] : 0;
        $model->save();
        if (!empty($request->files) && $request->hasFile('product_image')) {
            $model->image = $file_temp_name;
            $model->save();
        }
        /**    Product General Information  */
        $input['general']['product_id'] = $model->id;
        $input['general']['product_type_id'] = Hashids::decode($input['general']['product_type_id'])[0];
        $input['general']['product_category_id'] = Hashids::decode($input['general']['product_category_id'])[0];

        $input['general']['saas_discount_percentage']         = $input['saas_discount_percentage'];
        // dd($input['general'])
        $general_info = ProductGeneralInformation::create($input['general']);
        $general_info->download_link = $input['general']['download_link'];
        $general_info->save();
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

            $product_sale = ProductSale::where('product_id', $model->id)->first();
            if($allow_translation == 1)
            {
                dispatch(new \App\Jobs\TranslateProductDataJob($product_sale->id));
            }
        }
        if($input['action'] == 'Add'){
            $attached_attribute_value_ids = [];
            /**
             * if attributes are set then make the variatios based on the variation creation mode
             *
             *  dynamic mode no variation will be made for any attribute
             *  never mode, variation for the attribute will not be made
             *  instant mode creates the variations instantly
             *
             */
            $current_variation_ids = ProductVariation::where('product_id',$model->id)->pluck('id')->toArray();
            $current_variation_ids = $current_variation_ids == null ? array() :$current_variation_ids  ;

            $current_variation_usage = Quotation::whereHas('order_lines',function ($query) use($current_variation_ids){
                        $query->whereIn('variation_id',$current_variation_ids);
                    })->count();
            $current_variation_usage += License::whereIn('variation_id', $current_variation_ids)->count();
            if($current_variation_usage == 0){
                ProductVariation::where('product_id',$model->id)->delete();
                ProductVariationDetail::whereIn('product_variation_id', $current_variation_ids)->delete();
                $is_dynamic = 0;    // will be se  to 1 if any dynamic variant exists and then no varitions willbe made
                $variations_attributes_attached = array();
                if( isset( $request->attribute_id[0] ) && $request->attribute_id[0] != null )
                {
                    // Remove null indexes from the attribute_id params array
                    $request->attribute_id = array_filter($request->attribute_id);
                    // sync the Attached Attributes sent from the form
                    $sync_attached_attribute_data = array();
                    foreach($request->attribute_id as $attribute_id)
                    {
                        // Get the attribute
                        $attribute = ProductAttribute::where('id', $attribute_id)->first();
                        // Get the attribute name
                        $attribute_name = $attribute->attribute_name;
                        $sync_attached_attribute_data[$attribute_id] = array('attribute_name' => $attribute_name);

                    }
                    $model->attached_attributes()->sync($sync_attached_attribute_data);


                    // Loop through all product attached attributes
                    foreach($model->attributes as $model_attached_attribute)
                    {
                        $attribute_values = $request['attribute_value'.$model_attached_attribute->attribute_id];
                        $attribute_values = $attribute_values == null ? [] : $attribute_values;
                        $attached_attribute_value_ids = array_merge($attached_attribute_value_ids, $attribute_values == null ? [] : $attribute_values);
                        // Get the attribute
                        $attribute = ProductAttribute::where('id', $model_attached_attribute->attribute_id)->first();
                        // Get the attribute name
                        $attribute_name = $attribute->attribute_name;

                        // Array to sync data $sync_data[attribute_id] => value => value_name
                        $sync_data = array();
                        foreach( $attribute_values as $ind => $attr_id )
                        {
                            $attribute_value = ProductAttributeValue::where('id', $attr_id )->first()->attribute_value;
                            $sync_data[$attr_id] = array('value' => $attribute_value);
                        }
                        //sync Product Attached Attribute Values with Product Attached Attributes
                        $model_attached_attribute->attached_attribute_values()->sync($sync_data);

                        if( $attribute->variants_creation_mode == 2 ){       // Dynamic
                            $is_dynamic = 1;
                        }elseif( $attribute->variants_creation_mode == 1){   // Instant

                        $variations = ProductAttachedAttributeValue::where('product_attached_atribute_id', $model_attached_attribute->id)
                                    ->join('product_attached_atributes','product_attached_atributes.id', 'product_attached_atribute_values.product_attached_atribute_id')
                                    ->select('product_attached_atributes.attribute_id','product_attached_atribute_values.product_attached_atribute_id','product_attached_atribute_values.value','product_attached_atribute_values.value_id')->get()->toArray() ;
                        array_push($variations_attributes_attached,$variations) ;
                        }
                    }
                    $to_delete_variations = ProductVariationDetail::whereHas('variations.product', function ($q) use($model){
                                $q->where('id', $model->id);
                            })->join('product_variations','product_variations.id','product_variation_details.product_variation_id')
                            ->whereNotIn('product_variation_details.attribute_value_id',$attached_attribute_value_ids)
                            ->get();

                    $variation_details_json = array();
                    if( $is_dynamic == 0 ){
                        $cartesian_result = array();
                        $cartesian_product_result = $this->cartesian($variations_attributes_attached);
                        foreach($cartesian_product_result as $x){
                            $product_variation =  new ProductVariation;
                            $product_variation->product_id = $model->id;
                            $product_variation->is_active = 1;
                            $product_variation->save();
                            foreach($x as $y){
                                $product_variation_detail = new ProductVariationDetail;
                                    $product_variation_detail->product_variation_id = $product_variation->id;
                                    $product_variation_detail->product_attached_attribute_id = $y['product_attached_atribute_id'];
                                    $product_variation_detail->attribute_id = $y['attribute_id'];
                                    $product_variation_detail->attribute_value = $y['value'];
                                    $product_variation_detail->attribute_value_id = $y['value_id'];
                                $product_variation_detail->save();
                                // Variaiton Details Json
                                $variation_details_json[$y['attribute_id']] = $y['value_id'];
                            }
                            $product_variation->variation_detail_json = $variation_details_json;
                            $product_variation->save();
                        }
                    }

                }
            }
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
            foreach($input['eccomerce']['accessary_products'] as $aa_p)
            {
                $accessary_product = new ProductAccessaryProduct;
                    $accessary_product->product_id = $model->id;
                    $accessary_product->accessary_product_id = Hashids::decode($aa_p)[0];
                $accessary_product->save();
            }
        }
        /** Eccomerce Images */
        if($request->hasfile('eccomerce_images'))
        {
            $upload_path = public_path() . '/storage/uploads/sales-management/products/eccomerce/';
            if (!File::exists(public_path() . '/storage/uploads/sales-management/products/eccomerce/')) {
                File::makeDirectory($upload_path, 0777, true);
            }
            $images = $request->file('eccomerce_images');
            foreach( $images as $ind => $image)
            {
                $file = $image;
                $file_name = $file->getClientOriginalName();
                $type = $file->getClientOriginalExtension();
                $real_path = $file->getRealPath();
                $size = $file->getSize();
                $size_mbs = ($size / 1024) / 1024;
                $mime_type = $file->getMimeType();

                if ($type == 'jpg' or $type == 'JPG' or $type == 'PNG' or $type == 'png' or $type == 'jpeg' or $type == 'JPEG') {
                    $file_temp_name = 'product-'.$ind.'-' . time() . '.' . $type;

                    $path = public_path('storage/uploads/sales-management/products/eccomerce') . '/' . $file_temp_name;

                    // $img = Image::make($file)->resize(300, 300, function ($constraint) {
                    //     $constraint->aspectRatio();
                    // })->save($path);
                    $img = Image::make($file)->save($path);
                    $input['eccomerce'] = $file_temp_name;
                }

                $eccomerce_image = new ProductEccomerceImage;
                $eccomerce_image->product_id = $model->id;
                $eccomerce_image->image = $file_temp_name;
                $eccomerce_image->save();
            }
        }
        if (isset($input['action']) && $input['action'] == 'Edit') {
            Alert::success(__('Success'), __('Product updated successfully!'))->persistent('Close')->autoclose(5000);
        }else{
            Alert::success(__('Success'), __('Product added successfully!'))->persistent('Close')->autoclose(5000);
        }

        return redirect()->route('admin.products.index');
    }
    public function cartesian($input) {
        $result = array();

        // while (list($key, $values) = ($input)) {
        foreach ($input as $key => $values) {
            // If a sub-array is empty, it doesn't affect the cartesian product
            if (empty($values)) {
                continue;
            }

            // Seeding the product array with the values from the first sub-array
            if (empty($result)) {
                foreach($values as $value) {
                    $result[] = array($key => $value);
                }
            }
            else {
                // Second and subsequent input sub-arrays work like this:
                //   1. In each existing array inside $product, add an item with
                //      key == $key and value == first item in input sub-array
                //   2. Then, for each remaining item in current input sub-array,
                //      add a copy of each existing array inside $product with
                //      key == $key and value == first item of input sub-array

                // Store all items to be added to $product here; adding them
                // inside the foreach will result in an infinite loop
                $append = array();

                foreach($result as &$product) {
                    // Do step 1 above. array_shift is not the most efficient, but
                    // it allows us to iterate over the rest of the items with a
                    // simple foreach, making the code short and easy to read.
                    $product[$key] = array_shift($values);

                    // $product is by reference (that's why the key we added above
                    // will appear in the end result), so make a copy of it here
                    $copy = $product;

                    // Do step 2 above.
                    foreach($values as $item) {
                        $copy[$key] = $item;
                        $append[] = $copy;
                    }

                    // Undo the side effecst of array_shift
                    array_unshift($values, $product[$key]);
                }

                // Out of the foreach, we can add to $results now
                $result = array_merge($result, $append);
            }
        }

        return $result;
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
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if(!auth()->user()->can('Edit Product'))
        access_denied();

        $data = [];
        $id = Hashids::decode($id)[0];

        $data['action'] = 'Edit';

        $data['id'] = $id;
        $data['email_templates'] = EmailTemplate::all();
        $data['product_type'] = productType::all();
        $data['product_category'] = productCategorie::all();
        $data['customer_taxes'] = Tax::where('applicable_on',0)->where('is_active',1)->get();
        $data['vendor_taxes'] = Tax::where('applicable_on',1)->where('is_active',1)->get();
        $data['products'] = Products::all();;
        $data['eccomerce_categories'] = EccomerceCategory::all();;
        $data['product_variations'] = ProductVariation::with('variation_details')->get();
        $data['accessary_products'] = ProductVariation::with('product','variation_details')->get();
        $data['projects'] = Project::all();
        $data['services'] = Services::all();
        // dd($data['services']);
        $data['manufacturers'] = Manufacturer::whereNull('associated_manufacturer_id')->get();
        $data['model'] = Products::withCount(['attributes','manufacturer'])->find($id);
        // dd($data['model']->secondary_projects_array);
        if(!$data['model']){
            Alert::success(__('Warning'), __('Invalid Product!'))->persistent('Close')->autoclose(5000);
            return redirect()->route('admin.products.index');
        }
        $data['model_general_info'] = ProductGeneralInformation::where('product_id', $id)->first();
        $data['model_attached_attributes'] = ProductAttachedAttribute::with('attributeValue','allAttributeValue')->where('product_id', $id)->get();
        $data['model_sales'] = ProductSale::where('product_id', $id)->first();
        $data['model_customer_taxes'] = ProductTax::where('product_id', $id)->where('type',0)->pluck('tax_id')->toArray();
        $data['model_vendor_taxes'] = ProductTax::where('product_id', $id)->where('type',1)->pluck('tax_id')->toArray();
        $data['model_accessary_product'] = ProductAccessaryProduct::where('product_id', $id)->pluck('accessary_product_id')->toArray();
        $data['model_alternative_product'] = ProductAlternativeProduct::where('product_id', $id)->pluck('alternative_product_id')->toArray();
        $data['model_eccomerce_images'] = ProductEccomerceImage::where('product_id', $id)->get();
          // Code For Activities Section
        $log_uid = Auth::user()->id;
        $log_user_name = Auth::user()->firstname .' '. Auth::user()->lastname;
        $start_date = $data['model']->start_date;
        $end_date = $data['model']->end_date;
        $logged_in_follower_ids = Followers::where('contact_id', $log_uid)->where('product_id', $id)->where('module_type', 5)->where('follower_type', 2)->pluck('follower_id')->toArray();
        if (Contact::whereIn('id', $logged_in_follower_ids)->where('admin_id', $log_uid)->exists()) {

           $data['is_following'] = 1;
        }
        else {

            $data['is_following'] = 0;
        }
        $data['followers'] = $this->productRepository->follower_list($id,$log_uid, $module_type = 5);
        $data['send_messages'] = ActivityMessages::with('activity_message_users','activity_attachments')->where('product_id',$id)->orderBy('id','desc')->get();
        $attachments = ActivityAttachments::where('product_id', $id)->orderBy('send_msg_id','desc')->orderBy('log_note_id', 'desc')->orderBy('schedule_activity_id', 'desc')->get();
        $data['log_notes'] = ActivityLogNotes::with('log_note_users','activity_attachments')->where('product_id',$id)->orderBy('id','desc')->get();
        $recipients = Contact::where('admin_id','<>', Auth::user()->id)->orWhere('admin_id', null)->where('status', 1)->get();
        $schedule_users = Admin::where('is_active',1)->where('is_archive','<>', 1)->get();
        $schedule_activity_types = ActivityTypes::where('status',1)->get();
        $schedule_activities = ScheduleActivities::with('activity_types','schedule_by_users','assign_to_users')->where('product_id', $id)->where('status', 0)->orderBy('due_date','asc')->get();
        $scheduled_done_activities = ScheduleActivities::with('activity_types','schedule_by_users','assign_to_users','activity_attachments')->where('product_id', $id)->where('status', 1)->orderBy('id','desc')->get();
        $data['diffInDays'] = Carbon::parse($start_date)->diffInDays(Carbon::parse($end_date));
        $data['diffInMonths'] = Carbon::parse($start_date)->floatdiffInMonths(Carbon::parse($end_date));
        $data['send_messages_view'] = $this->productRepository->sendMsgs($id, $log_uid, $module ='products', $log_user_name, $recipients, $module_type = 5,$log_uid);
        $data['log_notes_view'] = $this->productRepository->logNotes($id, $log_uid, $module ='products', $log_user_name);
        $data['schedual_activities_view'] = $this->productRepository->schedualActivities($id, $log_uid, $module ='products', $schedule_users, $schedule_activity_types, $log_uid, $module_type = 5);
        $data['notes_tab_partial_view'] = $this->productRepository->notesTabPartialView($data['log_notes']);
        $data['send_message_tab_partial_view'] = $this->productRepository->sendMsgTabPartialView($data['send_messages']);
        $data['schedual_activity_tab_partial_view'] = $this->productRepository->schedualActivityTabPartialView($schedule_activities, $scheduled_done_activities, $module ='products');
        $data['attachments_partial_view'] = $this->productRepository->attachmentsPartialView($attachments);

        return view('admin.sales.products.product_form')->with($data);

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
        if(!auth()->user()->can('Delete Product'))
        access_denied();

        $id = Hashids::decode($id);
        $check_quotation_usage = QuotationOrderLine::where('product_id',$id)->first();
        $check_license_usage = License::where('product_id',$id)->first();
        $check_voucher_usage = Voucher::where('product_id',$id)->first();
        if($check_license_usage || $check_quotation_usage || $check_voucher_usage){
            Alert::warning(__('Warning'), __('You cannot delete the product because it is used on some quotations'))->persistent('Close')->autoclose(5000);
            return redirect()->back();
        }

        // Delete the Product and its related details
        ProductEccomerceImage::where('product_id', $id)->delete();
        ProductAccessaryProduct::where('product_id', $id)->delete();
        ProductAttachedAttribute::where('product_id', $id)->delete();
        ProductAlternativeProduct::where('product_id', $id)->delete();
        ProductGeneralInformation::where('product_id', $id)->delete();
        ProductVariation::where('product_id', $id)->delete();
        ProductSale::where('product_id', $id)->delete();
        ProductTax::where('product_id', $id)->delete();
        Products::where('id', $id)->delete();

        Alert::success(__('Success'), __('Product deleted successfully!'))->persistent('Close')->autoclose(5000);


        return redirect()->route('admin.products.index');
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function deleteProducts(Request $request)
    {
        $ids = explode(',',$request->ids);
        // $check_usage = QuotationOrderLine::whereIn('product_id',$ids)->first();
        $check_quotation_usage = QuotationOrderLine::whereIn('product_id',$ids)->first();
        $check_license_usage = License::whereIn('product_id',$ids)->first();
        $check_voucher_usage = Voucher::whereIn('product_id',$ids)->first();
        if($check_license_usage || $check_quotation_usage || $check_voucher_usage){
            Alert::warning(__('Warning'), __('You cannot delete the product because it is used on some quotations'))->persistent('Close')->autoclose(5000);
            return redirect()->back();
        }
        // Deleting the Activiyt Log Notes For the Product

        Products::whereIn('id', $ids)->delete();
        ProductGeneralInformation::whereIn('product_id', $ids)->delete();
        ProductTax::whereIn('product_id', $ids)->delete();
        ProductSale::whereIn('product_id', $ids)->delete();
        ProductAttachedAttribute::whereIn('product_id', $ids)->delete();
        ProductVariation::whereIn('product_id', $ids)->delete();
        ProductAlternativeProduct::whereIn('product_id', $ids)->delete();
        ProductAccessaryProduct::whereIn('product_id', $ids)->delete();
        ProductEccomerceImage::whereIn('product_id', $ids)->delete();

        Alert::success(__('Success'), __('Product deleted successfully!'))->persistent('Close')->autoclose(5000);


        return redirect()->route('admin.products.index');
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function archiveProducts(Request $request)
    {

        if(!auth()->user()->can('Archive / Unarchive Product'))
        access_denied();

        $ids = explode(',',$request->ids);
        Products::whereIn('id', $ids)->update( [ 'is_active' => 0 ] );

        Alert::success(__('Success'), __('Product archived successfully!'))->persistent('Close')->autoclose(5000);


        return redirect()->route('admin.products.index');
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function unarchiveProducts(Request $request)
    {
        if(!auth()->user()->can('Archive / Unarchive Product'))
        access_denied();
        $ids = explode(',',$request->ids);
        Products::whereIn('id', $ids)->update( [ 'is_active' => 1 ] );

        Alert::success(__('Success'), __('Product  Un-archived successfully!'))->persistent('Close')->autoclose(5000);


        return redirect()->route('admin.products.index');
    }
    /**
     * Configure Product Variants Listing
     *
     *
     * @param  $product_id
     * @param \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function configureVariants($product_id,  Request $request)
    {
        if(!auth()->user()->can('Configure Variants Listing'))
        access_denied();

        $product_id = Hashids::decode($product_id);
        $data['product_attached_attributes']  = ProductAttachedAttribute::with('attributeValue','attributeDetail')->where('product_id',$product_id)->get();
        return view('admin.sales.products.product_configure_variant_list',$data);
    }
    /**
     * Configure Product Variant Edit
     *
     *
     * @param  $product_attached_attribute_value_id
     * @param \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function configureVariantsEdit($product_attached_attribute_value_id,  Request $request)
    {
        $product_attached_attribute_value_id = Hashids::decode($product_attached_attribute_value_id)[0];
        $data['product_attached_attribute']  = ProductAttachedAttributeValue::with('attachedAttribute')->where('id',$product_attached_attribute_value_id)->first();
        return view('admin.sales.products.product_configure_variant_form',$data);
    }
    public function configureVariantsChangeStatus($product_attached_attribute_value_id,  Request $request)
    {
        $product_attached_attribute_value_id = Hashids::decode($product_attached_attribute_value_id)[0];

        $paav = ProductAttachedAttributeValue::with('attachedAttribute')->where('id',$product_attached_attribute_value_id)->first();
        $attribute_id = $paav->attachedAttribute->attribute_id;
        $product_id = $paav->attachedAttribute->product_id;
        $value_id = $paav->value_id;
        $paav->is_active = $request->status;
        $paav->save();

        $ProductVariations = ProductVariation::whereHas('variation_details',function($q) use($attribute_id, $value_id){
            $q->where('attribute_id', $attribute_id);
            $q->where('attribute_value_id',$value_id);
        })->where('product_id',$product_id)->get();
        foreach($ProductVariations as $ProductVariation){
            $ProductVariation->is_active = $request->status;
            $ProductVariation->save();
        }
        // dd($ProductVariation);

        Alert::success(__('Success'), __('Product Variant status updated successfully!'))->persistent('Close')->autoclose(5000);
        return redirect()->back();
    }
    /**
     * Configure Product Variant Edit Post
     *
     * @param \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     *
     */
    public function configureVariantsEditPost( Request $request)
    {
        $product_attached_attribute_value_id = Hashids::decode($request->id)[0];
        ProductAttachedAttributeValue::where('product_attached_atribute_values.id', $product_attached_attribute_value_id)
        ->update(['extra_price'=>$request->extra_price]);
        $product_id = ProductAttachedAttributeValue::where('product_attached_atribute_values.id', $product_attached_attribute_value_id)
            ->join('product_attached_atributes','product_attached_atributes.id','product_attached_atribute_values.product_attached_atribute_id')
            ->select('product_attached_atributes.*')->first()->product_id;

        return redirect()->route('admin.products.configure.variants', Hashids::encode($product_id));
    }
    /**
     * Check Attribute if used for any quotation
     *
     * @param \Illuminate\Http\Request  $request
     * @return true if attribute is used else false
     *
     */
    public function check_product_variation_attribute_value_usage_quotation(Request $request)
    {

        // Get all variation ids for the product
        $variation_ids = ProductVariation::where('product_id',$request->product_id)->pluck('id')->toArray();
        $variation_ids = $variation_ids == null ? array() :$variation_ids;

        $variation_usage = Quotation::whereHas('order_lines',function ($query) use($variation_ids){
                    $query->whereIn('variation_id',$variation_ids);
                })->count();

        if($variation_usage > 0){
            return 'true';
        }
        return 'false';
    }
    public function remove_variation_based_attribute(Request $request)
    {
        if(isset($request->attribute_id) && $request->attribute_id != null && $request->attribute_id != '' ){
            $attribute_id = $request->attribute_id;
            $product_variations = Products::join('product_variants','product_variants.product_id','products.id')
                                        ->join('product_variation_details','product_variation_details.product_variation_id','product_variations.id')
                                        ->join('product_attached_atributes','product_attached_atributes.id','product_variation_details.product_attached_attribute_id')
                                        ->where('product_attached_atributes.attribute_id',$request->attribute_id)
                                        ->where('products.id',$request->product_id)->pluck('product_variations.id')->toArray();
        }
    }
    public function product_attributes_options(){
        $attributes = ProductAttribute::with('attributeValue')->get();
        $html = view('admin.sales.products.modal-box.add-attribute-product',[ 'attributes' => $attributes])->render();
        return response()->json([
            'html' => $html,
            'attributes' => $attributes,
        ]);
    }
    public function remove_eccomerce_image(Request $request)
    {
        $image_id  = 0;
        try {
            //code...
            $image_id = Hashids::decode($request->image_id)[0];
        } catch (\Throwable $th) {
            //throw $th;
        }
        $eccomerce_image = ProductEccomerceImage::where('id', $image_id )->first();
        if($eccomerce_image)
        {
            $old_file = public_path() . '/storage/uploads/sales-management/products/eccomerce' . '/' . $eccomerce_image->image;

            if (file_exists($old_file) && !empty($model->image)) {
                //delete previous file
                unlink($old_file);
            }
            ProductEccomerceImage::where('id', $image_id )->delete();
            return 'true';
        }
        return 'faslse';
    }
    /**
     * Get Product Variations Json Ajax
     *
     */
    public function getVariations($product_id,Request $request)
    {
        $product_id = Hashids::decode($product_id)[0];

        $product_variations = Products::with(['generalInformation','variations' => function ($query) {
            $query->where('is_active', 1);
        },'variations.variation_details'])->where('id', $product_id)->first();
        
        foreach($product_variations->variations as $ind => $product_variation){
            $product_variations->variations[$ind]->variation_name  = $product_variation->variation_name;
            $product_variation->hashedid  = Hashids::encode($product_variation->id);
        }

        $input = $request->all();
        $vat_percentage =\App\Models\SiteSettings::first()->defualt_vat;
        if(isset($input['country_id'])){
            if( ContactCountry::where('id', Hashids::decode($input['country_id'])[0])->first()){
                if(ContactCountry::where('id', Hashids::decode($input['country_id'])[0])->first()->is_default_vat == 0){
                    $vat_percentage = ContactCountry::where('id', Hashids::decode($input['country_id'])[0])->first()->vat_in_percentage;
                }
            }
        }else{
            if(Auth::user())
            {
                $vat_percentage = Auth::user()->contact->contact_countries->vat_in_percentage;
                if(Auth::user()->contact->contact_countries->is_default_vat == 1)
                {
                    $vat_percentage =\App\Models\SiteSettings::first()->defualt_vat;
                }
            }
        }

        $product_price = $product_variations->reseller_price_without_vat['total_price_exclusive_vat'];
        $end_product_price = 0;
        
        if(Auth::user() && isset(Auth::user()->contact) && Auth::user()->contact->type == 3)
        {
            $reseller_prices = resellerProductPrice(Auth::user()->contact->id, $product_id);
            $product_price = $reseller_prices['product_price'];
            $end_product_price = $reseller_prices['end_product_price'];
        }
        if(count($product_variations->variations) > 0){
            foreach($product_variations->variations as $ind => $product_variation){
                    if($product_variation->variation_sales_price != ''  && $product_variation->variation_sales_price != null ){
                        if($product_variation->variation_sales_price < $product_price ){
                            $product_price = $product_variation->variation_sales_price;
                        }
                        if($product_variation->variation_sales_price > $end_product_price ){
                            $end_product_price = $product_variation->variation_sales_price;
                        }
                    }
            }
        }

        $product_price  = $product_price + ($product_price * $vat_percentage / 100);
        $end_product_price  = $end_product_price + ($end_product_price * $vat_percentage / 100);

       
        $product_variations->product_price = currency_format( ( $product_price * (\Session::get('exchange_rate') ? \Session::get('exchange_rate') : 1) ),'','',1);
        $product_variations->end_product_price = currency_format( ( $end_product_price * (\Session::get('exchange_rate') ? \Session::get('exchange_rate') : 1) ),'','',1);
        return array(
            'success'=> 'true',
            'data'=> $product_variations,
            'message'=> __('Product Variations')
        );
        try {
        } catch (\Throwable $th) {
            return array(
                'success'=> 'false',
                'data'=> [],
                'message'=> __('Invalid Product')
            );
        }
    }
    public function getVariationsDetails($variation_id, Request $request){
        $vat_percentage = \App\Models\SiteSettings::first()->defualt_vat;
        $input = $request->all();
        if(isset($input['country_id'])){
            if( ContactCountry::where('id', Hashids::decode($input['country_id'])[0])->first()){
                if(ContactCountry::where('id', Hashids::decode($input['country_id'])[0])->first()->is_default_vat == 0){
                    $vat_percentage = ContactCountry::where('id', Hashids::decode($input['country_id'])[0])->first()->vat_in_percentage;
                }
            }
        }else{
            if(Auth::user())
            {
                $vat_percentage = Auth::user()->contact->contact_countries->vat_in_percentage;
                if(Auth::user()->contact->contact_countries->is_default_vat == 1)
                {
                    $vat_percentage = \App\Models\SiteSettings::first()->defualt_vat;
                }
            }
        }
        $variation_id = Hashids::decode($variation_id)[0];
        $variation = ProductVariation::with('product')->where('id', $variation_id)->first();
        $product_price = 0;
        if($variation->reseller_sales_price != null){
            $product_price = $variation->reseller_sales_price;
        }
        else{
            if($variation->variation_sales_price == null){
                $extra_price = $variation->extra_price;
                $product_price = $variation->product->price_without_vat['total_price_exclusive_vat_tax'] + $extra_price;
            }else{
                $product_price = $variation->variation_sales_price;
            }
        }
        $product_price -= $product_price * $variation->product->generalInformation->voucher_discount_percentage / 100;
        
        if(isset($request->reseller_id) && $request->reseller_id != '' || ( Auth::user() && Auth::user()->contact->type == 3))
        {
            if(Auth::user())
            {
                $request->reseller_id = Hashids::encode(Auth::user()->id);
            }
            $reseller = Contact::whereHas('user', function($q) use($request){
                $q->where('id',Hashids::decode($request->reseller_id)[0]);
            })->first();
          
            $product_price = resellerProductVariationPrice($reseller->id,$variation_id);
        }
        $product_price += $product_price * $vat_percentage / 100;
        return array(
            'success'=> 'true',
            'data'=> currency_format(($product_price* (\Session::get('exchange_rate') ? \Session::get('exchange_rate') : 1)),'','',1),
            'message'=> __('Product Variations')
        );


    }

    public function checkOrderNumber(Request $request)
    {
        $product = Products::where('order_number',$request->order_number);
        
        if($request->product_id != 0)
        {
            $product->where('id', '!=', $request->product_id);
        }
        $product = $product->first();
        if($product){
            return 'false';
        }
        return 'true';
    }
}
