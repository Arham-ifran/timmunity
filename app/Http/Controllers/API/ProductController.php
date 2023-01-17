<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController;
use Illuminate\Http\Request;
use App\Models\Products;
use App\Models\ProductVariation;
use App\Models\DistributorProductDetail;
use App\Models\Project;
use App\Models\Manufacturer;
use App\Models\ProductSale;
use Hashids;

class ProductController extends BaseController
{

    public function getManufacturers(Request $request) {
        $manufacturers = Manufacturer::whereHas('products',function($query){
            $query->where('is_active', 1);
        })->whereNull('associated_manufacturer_id')
            ->select('id','company as name','image')->orderBy('manufacturer_name','asc')->get();
        
        foreach($manufacturers as $manufacturer){
            if($manufacturer->image != null){
                $manufacturer->image  = asset('/storage/uploads/manufacturer/'.Hashids::encode(@$manufacturer->id).'/'.@$manufacturer->image);
                
            }else{
                $manufacturer->image = asset('backend/dist/img/placeholder-products.jpg');
            }
            // $manufacturer->image = checkImage(asset('/storage/uploads/manufacturer/'.Hashids::encode(@$manufacturer->id).@$manufacturer->image),'placeholder-products.jpg');
        }
        return array(
            'success' => true,
            'manufacturers' => $manufacturers,
            'message' => __('Manufacturers')
        );
    }
    public function comparator($object1, $object2) {
        return $object1->product_price > $object2->product_price;
    }

    public function get_product_price_range($product_id){
        $productObj = Products::where('id', $product_id)->first();
        $product_price = $productObj->reseller_price_without_vat['total_price_exclusive_vat'];
        $end_product_price = 0;

        $product_variations = ProductVariation::where('product_id', $product_id)->get();
        if(count($product_variations) != 0){
            foreach($product_variations as $ind => $product_variation){
                if( $ind == 0 ){
                    $product_price = $product_variation->reseller_sales_price;
                }
                if($product_variation->reseller_sales_price != ''  && $product_variation->reseller_sales_price != null ){
                    if($product_variation->reseller_sales_price < $product_price ){
                        $product_price = $product_variation->reseller_sales_price;
                    }
                    if($product_variation->reseller_sales_price > $end_product_price ){
                        $end_product_price = $product_variation->reseller_sales_price;
                    }
    
                }
                else{
                    if($product_variation->variation_sales_price < $product_price ){
                        $product_price = $product_variation->variation_sales_price;
                    }
                    if($product_variation->variation_sales_price > $end_product_price ){
                        $end_product_price = $product_variation->variation_sales_price;
                    }
                }
            }
        }
        return [$product_price, $end_product_price];
    }
    /**
     * Recieves search_query as optional paramater
     * Recieves sort_type as optional paramater // Default Name A-Z
     * sort type = 0: Sales Price A-Z , 1: Sales Price Z-A, 2: Product Name A-Z , 3: Product Name Z-A
     */
    public function getAllProducts(Request $request)
    {

        $distributor = $this->verifyRequest($request);
        if($distributor['success'] == 'false'){
            $distributor['success'] = false;
            return $distributor;
        }
        $input = $request->all();

        $product_query = DistributorProductDetail::join( 'products', 'products.id', 'distributor_product_details.product_id' )
                ->join( 'product_general_informations', 'product_general_informations.product_id', 'products.id' )
                ->join( 'product_sales', 'product_sales.product_id', 'products.id' )
                ->where('distributor_product_details.distributor_id', $distributor->id)
                ->where('distributor_product_details.is_active', 1)
                ->select(
                    'products.id',
                    'products.product_name',
                    'products.image',
                    'product_general_informations.sales_price',
                    'distributor_product_details.extra_price',
                    'products.secondary_project_ids',
                    'products.manufacturer_id as manufacturer_id'
                );
        if( isset($input['search_query']) && $input['search_query'] != null && $input['search_query'] != '' )
        {
            $product_query->where('products.product_name', 'LIKE', '%'.$input['search_query'].'%');
        }
        if( isset($input['manufacturer_id']) && $input['manufacturer_id'] != null && $input['manufacturer_id'] != '' )
        {
            $product_query->where('products.manufacturer_id', $input['manufacturer_id']);
        }
        // $product_query->orderBy('products.product_name', 'asc');
        if(isset($request->sort_type)){
            switch ($request->sort_type) {
                case '2':
                    $product_query->orderBy('products.product_name', 'asc');
                    break;
                case '3':
                    $product_query->orderBy('products.product_name', 'desc');
                    break;
                default:
                    break;
            }
        }else{
            $product_query->orderBy('products.product_name', 'asc');
        }
        $products = $product_query->get();
        $products = $products->sortByDesc('product_name');
        if( count($products) < 0 )
        {
            return array(
                'success' => false,
                'products' => [],
                'message' => __('No product found')
            );
        }
        // Manupilating the products array to add prices
        foreach($products as $index => $product)
        {
            
            $extra_price = $product->extra_price;
            $product->image = checkImage(asset('storage/uploads/sales-management/products/' . $product->image), 'placeholder-products.jpg');
           
            $product->variations_count = ProductVariation::where('product_id', $product->id)->count();
            $price_range = $this->get_product_price_range($product->id);
            $product->product_price = str_replace(',','',currency_format($price_range[0] + $extra_price,'','',1));
            $product->end_product_price = str_replace(',','',currency_format($price_range[1 ] + $extra_price,'','',1));
            
            
            $product->description = $product->description == null ? '' : $product->description;
            $project_ids = array_filter(explode(',',$product->secondary_project_ids));
            $product->secondary_platforms = Project::whereIn('id', $project_ids)->pluck('name','id')->toArray();
            $product->sales_price = str_replace(',','',currency_format($price_range[0] + $extra_price,'','',1));
        }
        $products = $products->toArray();
        if(isset($request->sort_type)){
            switch ($request->sort_type) {
                case '0':
                    usort($products, function ($item1, $item2) {
                        return $item1['product_price'] <=> $item2['product_price'];
                    });
                    break;
                case '1':
                        usort($products, function ($item1, $item2) {
                            return $item2['product_price'] <=> $item1['product_price'];
                        });
                    break;
                default:
                    break;
            }
        }
        return array(
                'success' => true,
                'products' => $products,
                'message' => __('Product[s] found')
            );
    }

    /**
     * Recieves product_id as mandatory paramater
     */
    public function getVariationDetails(Request $request)
    {
        $distributor = $this->verifyRequest($request);
        if($distributor['success'] == 'false'){
            $distributor['success'] = false;
            return $distributor;
        }
        $input = $request->all();
        $locale = @$input['locale'];
        $locale = $locale == null ? 'de' : $locale;
        if(!isset($input['product_id'])){
            return array(
                'success' => false,
                'message' => __('Product ID is required')
            );
        }
        $product_variations = ProductVariation::where('product_variations.product_id', $input['product_id'])
                ->where('product_variations.is_active', 1)
                ->join('products','products.id','product_variations.product_id')
                ->join('product_sales','products.id','product_sales.product_id')
                ->select(
                    'product_variations.id as variation_id',
                    'products.id as product_id',
                    'product_variations.reseller_sales_price as reseller_sales_price',
                    'product_variations.variation_sales_price as variation_sales_price',
                    'product_variations.is_active',
                    'products.product_name'
                    )
                ->get();
        $distributor_product = DistributorProductDetail::where('distributor_id', $distributor->id)
            ->where('product_id', $input['product_id'])->first();;
        foreach($product_variations as $ind => $product_variation)
        {
            $productObj = Products::where('id', $product_variation->product_id)->first();
            $productVariationObj = ProductVariation::where('id', $product_variation->variation_id)->first();

<<<<<<< HEAD
            $product_variations[$ind]->product_name = $productVariationObj->variation_name_full;
=======
            $product_variations[$ind]->product_name = $product_variation->product_name.' '.$productVariationObj->variation_name_full;
>>>>>>> df569aa5 (API changes)

            if($product_variation->reseller_sales_price != null){
                $product_variation->sales_price = $product_variation->reseller_sales_price;
            }
            else{
                if($product_variation->variation_sales_price == null){
                    $extra_price = $productVariationObj->extra_price;
                    $product_variation->sales_price = $product_variation->product->reseller_price_without_vat['total_price_exclusive_vat_tax'] + $extra_price;
                }else{
                    $product_variation->sales_price = $product_variation->variation_sales_price;
                }
            }
            $extra_price = $distributor_product->extra_price;
            $product_variation->sales_price = str_replace(',','',currency_format($product_variation->sales_price + $extra_price,'','',1));

            unset($product_variation->variation_sales_price);
            unset($product_variation->reseller_sales_price);

            $project_ids = array_filter(explode(',',$productObj->secondary_project_ids));
            $product_variation->secondary_platforms = Project::whereIn('id', $project_ids)->pluck('name','id')->toArray();
            
        }
        
        ///// Images Start  /////
        $productObj = Products::where('id', $input['product_id'])->first();
        $product_sales = ProductSale::where('product_id', $input['product_id'])->select('description', 'long_description')->first();
        $product_sales->product_name = $productObj->product_name;
        $product_sales->description = translation( $product_sales->id,11,$locale,'description',$product_sales->description ) ;
        $product_sales->long_description = translation( $product_sales->id,11,$locale,'description',$product_sales->long_description ) ;
        $price_range = $this->get_product_price_range($input['product_id']);
        $product_sales->product_price = $price_range[0];
        $product_sales->end_product_price = $price_range[1];

        $eccomerce_images_sent = [
            checkImage(asset('storage/uploads/sales-management/products/' . $productObj->image), 'placeholder-products.jpg')
        ];
        foreach($productObj->eccomerce_images as $product_image){
            array_push($eccomerce_images_sent, url('storage/uploads/sales-management/products/eccomerce').'/'.$product_image->image);
        }
        ///// Images End /////
    
        return array(
            'success' => true,
            'product_variations' => $product_variations,
            'product_details' => $product_sales,
            'images' => $eccomerce_images_sent,
            'message' => __('Variation Details')
        );
    }

    public function changeProductExtraPrices(Request $request)
    {
        $distributor = $this->verifyRequest($request);
        if($distributor['success'] == 'false'){
            $distributor['success'] = false;
            return $distributor;
        }
        $input = $request->all();
        if(!isset($input['products'])){
            return array(
                'success' => false,
                'message' => __('Parameters missing is required')
            );
        }
        foreach($input['products'] as $product)
        {
             DistributorProductDetail::updateOrCreate([
                'product_id'   => $product['id'],
                'distributor_id'   => $distributor->id,
            ],[
                'extra_price' => $product['extra_price']
            ]);
        }
        return array(
            'success' => true,
            'message' => __('Data updated')
        );
    }
}
