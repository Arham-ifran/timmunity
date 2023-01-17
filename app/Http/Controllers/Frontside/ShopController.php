<?php

namespace App\Http\Controllers\Frontside;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Products;
use App\Models\ProductAttachedAttributeValue;
use App\Models\ProductVariation;
use App\Models\ProductVariationDetails;
use App\Models\ContactCountry;
use Hashids;
use Auth;
use Session;

class ShopController extends Controller
{
    public function __construct(){

    }
    /**
     * Shop Page
     *
     */
    public function index(Request $request){
        $data = [];
        $ip_info = ip_info();
        $default_vat = \App\Models\SiteSettings::first()->defualt_vat;

        if( $request->ajax() ){
            $product_query = Products::withCount('variations')
                    ->with('generalInformation')
                    ->where('is_active', 1)
                    ->where('can_be_sale', 1)
                    ->where('can_be_purchase', 1);

            if(isset($request->sort_type)){
                switch ($request->sort_type) {
                    case '2':
                        $product_query->orderBy('product_name', 'asc');
                        break;
                    case '3':
                        $product_query->orderBy('product_name', 'desc');
                        break;
                    default:
                    break;
                }
            }else{
                $product_query->orderBy('order_number','asc');
            }
            $data['products'] = $product_query->get();
            if(isset($request->sort_type)){
                switch ($request->sort_type) {
                    case '0':
                        $data['products'] = $data['products']->sortBy('generalInformation.sales_price');
                        break;
                    case '1':
                        $data['products'] =  $data['products']->sortByDesc('generalInformation.sales_price');
                        break;
                        
                    default:
                    break;
                }
            }else{
                $product_query->sortBy('products.order_number');

            }
            if(Auth::user()){
                $data['vat_percentage'] = $default_vat;
                $data['vat_percentage'] = Auth::user()->contact->contact_countries->vat_in_percentage;
                if(Auth::user()->contact->contact_countries->is_default_vat == 1)
                {
                    $data['vat_percentage'] = $default_vat;
                }
            }
            else
            {
                // dd($ip_info);
                $data['vat_percentage'] = $default_vat;
                if(isset($ip_info['country_code'])){
                    $data['vat_percentage'] = ContactCountry::where('country_code', $ip_info['country_code'])->first() ? ContactCountry::where('country_code', $ip_info['country_code'])->first()->vat_in_percentage : $default_vat;

                }
            }
            $data['default_currency'] = \App\Models\Currency::where('is_active', 1)->where('is_default', 1)->first();
            $returnHTML = view('frontside.shop.partials.shop-products', $data)->render();
            return response()->json(array('success' => true, 'html'=>$returnHTML));
        }
        $data['default_currency'] = \App\Models\Currency::where('is_active', 1)->where('is_default', 1)->first();
        $data['countries'] = ContactCountry::all();
        $data['products'] = Products::withCount('variations')
            ->with('generalInformation')
            ->where('is_active', 1)
            ->where('can_be_sale', 1)
            ->where('can_be_purchase', 1)
            ->orderBy('order_number','asc')
            ->get()
            ->sortBy('order_number');
        // return $data['products'];
        if(Auth::user()){
            $data['vat_percentage'] = $default_vat;
            $data['vat_percentage'] = Auth::user()->contact->contact_countries->vat_in_percentage;
            if(Auth::user()->contact->contact_countries->is_default_vat == 1)
            {
                $data['vat_percentage'] = $default_vat;
            }
        }
        else
        {
            $data['vat_percentage'] = $default_vat;
            if(isset($ip_info['country_code'])){
                $data['vat_percentage'] = ContactCountry::where('country_code', $ip_info['country_code'])->first() ? ContactCountry::where('country_code', $ip_info['country_code'])->first()->vat_in_percentage : $default_vat;
            }
        }
        if(Auth::user()){
            $data['default_vat'] = 0;
        }else{
            $data['default_vat'] = \App\Models\SiteSettings::first()->defualt_vat;
        }
        return view('frontside.shop.index',$data);
    }
    /**
     * Search Suggestions Auto-complete
     *
     */
    public function shopSearchSuggestions(Request $request){
        $data = [];

        $query = Products::with('generalInformation')->where('is_active', 1)->where('can_be_sale', 1)->where('can_be_purchase', 1);
        if( Auth::user()  )
        {
            if( Auth::user()->contact->type != 3 )      // Reseller
            {
                $query->where('project_id', null);
            }
        }
        else
        {

            $query->where('project_id', null);
        }
        if( isset($request->q) && $request->q != null )
        {
            $query->where('product_name', 'LIKE', '%'.$request->q.'%');
        }
        $data['products'] = $query->get();
        foreach($data['products'] as $ind => $p){
            $p->hashed_id = Hashids::encode($p->id);
        }

        return response()->json(array('success' => true, 'data' => $data['products']));

        // $returnHTML = view('frontside.shop.suggestions', $data)->render();
        // return response()->json(array('success' => true, 'html'=>$returnHTML));

    }


    /**
     * Product Detail Page
     *
     */
    public function productDetails($slug, Request $request){

        $data = [];
        $data['product'] = Products::withCount('variations')->with(
                                'generalInformation',
                                'attributes',
                                'attributes.attributeValue',
                                'attributes.attributeDetail',
                                'attributes.attributeValue.attributeValueDetail',
                                'sales'
                            )->where('slug', $slug)->where('is_active', 1)->first();
        if($data['product']){
            $ip_info = ip_info();
            $default_vat = \App\Models\SiteSettings::first()->defualt_vat;
            if(Auth::user()){
                $data['vat_percentage'] = $default_vat;
                $data['vat_percentage'] = Auth::user()->contact->contact_countries->vat_in_percentage;
                if(Auth::user()->contact->contact_countries->is_default_vat == 1)
                {
                    $data['vat_percentage'] = $default_vat;
                }
            }
            else
            {
                $data['vat_percentage'] = $default_vat;
                if(isset($ip_info['country_code'])){
                    $vat_percentage = ContactCountry::where('country_code', $ip_info['country_code'])->first() ? ContactCountry::where('country_code', $ip_info['country_code'])->first()->vat_in_percentage : $default_vat;
                    $cc = ContactCountry::where('country_code', $ip_info['country_code'])->first();
                    if($cc){
                        if($cc->is_default_vat == 1)
                        {
                            $vat_percentage = $default_vat;
                            // $vat_label = __($vat_label);
                        }
                    }
                    // $data['vat_percentage'] = ContactCountry::where('country_code', $ip_info['country_code'])->first() ? ContactCountry::where('country_code', $ip_info['country_code'])->first()->vat_in_percentage : $default_vat;
                    $data['vat_percentage'] = $vat_percentage;

                }
            }

            return view('frontside.shop.product_details',$data);
        }else{
            return redirect()->route('frontside.shop.index');
        }
    }
    public function getIP(){
        if (getenv('HTTP_CLIENT_IP'))
            return getenv('HTTP_CLIENT_IP');
        else if(getenv('HTTP_X_FORWARDED_FOR'))
            return getenv('HTTP_X_FORWARDED_FOR');
        else if(getenv('HTTP_X_FORWARDED'))
            return getenv('HTTP_X_FORWARDED');
        else if(getenv('HTTP_FORWARDED_FOR'))
            return getenv('HTTP_FORWARDED_FOR');
        else if(getenv('HTTP_FORWARDED'))
            return getenv('HTTP_FORWARDED');
        else if(getenv('REMOTE_ADDR'))
            return getenv('REMOTE_ADDR');
        else
            return 'UNKNOWN';
    }
    public function getExtraPrice(Request $request)
    {
        $ip_info = ip_info();
        $default_vat = \App\Models\SiteSettings::first()->defualt_vat;
        $vat_percentage = $default_vat;
        if(Auth::user())
        {
            $vat_percentage = $default_vat;
            $vat_percentage = Auth::user()->contact->contact_countries->vat_in_percentage;
            if(Auth::user()->contact->contact_countries->is_default_vat == 1)
            {
                $vat_percentage = $default_vat_percentage;
            }
        }
        else
        {
            if(isset($ip_info['country_code'])){
                $vat_percentage = ContactCountry::where('country_code', $ip_info['country_code'])->first() ? ContactCountry::where('country_code', $ip_info['country_code'])->first()->vat_in_percentage : $default_vat;
            }
        }
        $product_id = $request->product_id;
        $attribute_ids = (array)json_decode($request->attribute_ids);
        $attrbiute_ids_wild_card1 = '{';
        $i = 0;
        foreach($attribute_ids as $attribute_id => $attribute_value_id ){
            $attrbiute_ids_wild_card1 .= '"'.$attribute_id.'":'.$attribute_value_id;
            if($i < count($attribute_ids)-1){
                $attrbiute_ids_wild_card1 .= ",";
            }
            $i++;
        }
        $attrbiute_ids_wild_card1 .= "}";
        $attrbiute_ids_wild_card2 = '{';
        $i = 0;
        foreach(array_reverse($attribute_ids,true) as $attribute_id => $attribute_value_id ){
            $attrbiute_ids_wild_card2 .= '"'.$attribute_id.'":'.$attribute_value_id;
            if($i < count($attribute_ids)-1){
                $attrbiute_ids_wild_card2 .= ",";
            }
            $i++;
        }
        $attrbiute_ids_wild_card2 .= "}";

        $extra_price = ProductAttachedAttributeValue::whereHas('attachedAttribute', function ($query) use($product_id, $attribute_ids){
            $query->where('product_id', $product_id);
        })->whereIn('value_id', $attribute_ids)->sum('extra_price');
        $attrbiute_ids_wild_card = array($attrbiute_ids_wild_card1,$attrbiute_ids_wild_card2);
        $product_variation = ProductVariation::whereIn('variation_detail_json',$attrbiute_ids_wild_card)->where('product_id',$product_id)->first();
        if($product_variation){
            if($product_variation->variation_sales_price == null){
                $extra_price = $extra_price * (\Session::get('exchange_rate') ? \Session::get('exchange_rate') : 1) ;
                $extra_amount *= (Session::get('exchange_rate') ? Session::get('exchange_rate') : 1);
                $extra_price = $extra_price+($extra_price*$vat_percentage/100);
                $response = array(
                    'amount' => currency_format($extra_price ,'','',1 ),
                    'extra' => true
                );
            }
            else
            {
                $price = $product_variation->variation_sales_price;
                $price *= (Session::get('exchange_rate') ? Session::get('exchange_rate') : 1);
                $price += ($price * $vat_percentage / 100);
                $response = array(
                        'amount' => currency_format($price ,'','',1 ),
                        'extra' => false
                );
            }
        }
        else
        {
            $response = array(
                    'amount' => currency_format($extra_price+($extra_price*$vat_percentage/100),'','',1 ),
                    'extra' => true
            );
        }

        return $response;

    }


}
