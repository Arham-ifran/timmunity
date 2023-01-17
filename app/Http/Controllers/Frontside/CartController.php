<?php

namespace App\Http\Controllers\Frontside;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Products;
use App\Models\ProductVariation;
use App\Models\ProductVariationDetail;
use App\Models\ProductAttachedAttribute;
use App\Models\ProductAttachedAttributeValue;
use App\Models\ProductAttributeValue;
use App\Models\Contact;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\CartItemVariationDetail;
use App\Models\ProductPricelistConfiguration;
use App\Models\ProductPriceList;
use App\Models\ProductPricelistRule;
use App\Models\License;
use App\Models\ContactCountry;
use App\Http\Traits\AdminNotificationTrait;
use App\Models\Quotation;
use Session;
use Auth;
use Hashids;
class CartController extends Controller
{
    use AdminNotificationTrait;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Add new item to cart
     *
     */
    public function addToCart(Request $request)
    {
        // $this->validate($input
        $input = $request->all();
        $product = Products::with('attached_attributes','generalInformation')->where('id', $input['product_id'])->first();
        $unit_price = (double) $product->generalInformation->sales_price;
        $is_variable_product = count($product->attached_attributes) > 0 ? 1 : 0;
        $variation_id = null;
        $product_variation = null;
        // Check if the product is variable
        if($is_variable_product != 0)
        {
            if(!isset($input['attribute_values']))
            {
                return redirect()->back()->with(session()->flash('alert-error', __('Variation is mandatory')));
            }
            else if(count($input['attribute_values']) < count($product->attached_attributes))
            {
                return redirect()->back()->with(session()->flash('alert-error', __('All variations are mandatory')));
            }
            foreach($input['attribute_values'] as $attribute_id => $attribute_value_id)
            {
                $product_attached_atribute = ProductAttachedAttribute::where('product_id', $product->id)->where('attribute_id', $attribute_id)->first();
                $attribute_value = ProductAttachedAttributeValue::where('product_attached_atribute_id', $product_attached_atribute->id)
                    ->where('value_id',$attribute_value_id)
                    ->first();
                // Calculating Extra Price for variation Selected
                $unit_price += $attribute_value ? $attribute_value->extra_price : 0;
                $input['attribute_values'][$attribute_id] = (int)$attribute_value_id;
            }

            $variations = ProductVariation::where('product_id', $product->id)->get();

            foreach ($variations as $id => $variation)
            {
                // Array of [aatribute_id] => attribute_value_id saved while creating new variation in the product variations table
                $variation_json = json_decode($variation->variation_detail_json, TRUE);
                // Array of [aatribute_id] => attribute_value_id selected in the product detail page
                $selected_attr_json = json_decode(json_encode($input['attribute_values']),TRUE);
                // Check the difference of both above arrays
                $result_array = array_diff($variation_json,$selected_attr_json);

                // If both arrays are equal
                if(count($result_array) == 0)
                {
                    $variation_id = $variation->id; // Set the variation_id
                    break;
                }
            }
            // If the variation is not present i.e. the variation_id is null create a new variation and save it in product_variations table and product_variation_details table
            if($variation_id == null)
            {
                $product_variation =  new ProductVariation;
                    $product_variation->product_id = $product->id;
                    $product_variation->is_active = 1;
                $product_variation->save();
                $variation_details_json = array();
                foreach($input['attribute_values'] as $attribute_id => $attribute_value_id)
                {
                    $product_attached_atribute = ProductAttachedAttribute::where('product_id', $product->id)->where('attribute_id', $attribute_id)->first();
                    $attribute_value = ProductAttributeValue::where('id', $attribute_value_id)->first();
                    $variation_details_json[$attribute_id] = $attribute_value_id;
                    // if the given attribute and values are set
                    if($product_attached_atribute && $attribute_value)
                    {
                        $product_variation_detail = new ProductVariationDetail;
                            $product_variation_detail->product_variation_id = $product_variation->id;
                            $product_variation_detail->product_attached_attribute_id = $product_attached_atribute->id;
                            $product_variation_detail->attribute_id = $attribute_id;
                            $product_variation_detail->attribute_value = $attribute_value->attribute_value;
                            $product_variation_detail->attribute_value_id = $attribute_value_id;
                        $product_variation_detail->save();
                    }
                    // if the given attributes or value is not present delete the variation and redirect to shop page
                    else
                    {
                        $product_variation->delete();
                        return redirect()->route('frontside.shop.index')->with(session()->flash('alert-error', __('Something went wrong. Please try again')));
                    }
                }
                $product_variation->variation_detail_json = $variation_details_json;
                $product_variation->save();
                $variation_id = $product_variation->id;
            }
            $product_variation = ProductVariation::where('id',$variation_id)->first();
            if($product_variation->variation_sales_price != null)
            {
                $unit_price = $product_variation->variation_sales_price;
            }

        }
        if($product_variation->is_active != 1 )
        {
            return redirect()->back()->with(session()->flash('alert-error', __('The variation is not active. Try another one')));
        }
        if( $product  && $input['quantity'])
        {

            // Licenses Count for the item added if the product is not licensable through api
            if($product->product_type != 2){
                $licenses_count_query = License::where('product_id', $product->id);
                $licenses_count_query->where('variation_id',$variation_id);
                $licenses_count_query->where('status',1);
                $licenses_count_query->where('is_used',0);
                $licenses_count = $licenses_count_query->count();

                // If the availble unsused active license count is greater than or equal to the required amount
                if( $licenses_count < $input['quantity']  ){

                    $body = "One of the customers tried to purchase the mentioned product but the licenses were out of stock. Kindly purchase upload more vouchers to avoid further issues.";
                    $this->requestAdmintoUploadMoreVouchers($product->id, $variation_id, $body);
                    return redirect()->back()->with(session()->flash('alert-info', __("License key against the selected item isn't available. Admin has been requested to upload more license. Try again later.")));
                }
            }


            // If User is logged in set the Cart Table
            if(Auth::user()){
                $currency_acceptance = checkCurrencyAcceptibility(Session::get('currency_code'));
                // Check if the cart of the user isset if not create it
                $cart = Cart::firstOrCreate([
                    'customer_id' => Auth::user()->id,
                    'is_checkout' => 0
                ]);
                $cart->currency_symbol = $currency_acceptance ? Session::get('currency_symbol') == null ? \App\Models\Currency::where('is_active', 1)->where('is_default', 1)->first()->symbol : Session::get('currency_symbol') : "€";
                $cart->currency = $currency_acceptance ? Session::get('currency_code') == null ? \App\Models\Currency::where('is_active', 1)->where('is_default', 1)->first()->code : Session::get('currency_code') : "EUR";
                $cart->exchange_rate = $currency_acceptance ? Session::get('exchange_rate') == null ? \App\Models\Currency::where('is_active', 1)->where('is_default', 1)->first()->exchange_rate : Session::get('exchange_rate') : 1;
                $cart->save();

                $check_cart_item = CartItem::where('cart_id',$cart->id)
                        ->where('cart_id',$cart->id)
                        ->where('product_id',$product->id)
                        ->where('variation_id',$variation_id)
                        ->where('is_variable',$variation_id == null ? 0 : 1)
                        ->first();

                if($check_cart_item){
                    // If the availble unsused active license count if greater than or equal to the required amount
                    if($product->product_type != 2){
                        if( $licenses_count >= ($check_cart_item->qty + $input['quantity'] ) ||  $product->project_id != null ){
                            //If item is already in the cart update its quantity
                            $check_cart_item->qty += $input['quantity'];
                            $check_cart_item->save();
                        }
                        else
                        {
                            $body = "One of the customers tried to purchase the mentioned product but the licenses were out of stock. Kindly purchase upload more vouchers to avoid further issues.";
                            $this->requestAdmintoUploadMoreVouchers($product->id, $variation_id, $body);
                            return redirect()->back()->with(session()->flash('alert-info',  __("License key against the selected item isn't available. Admin has been requested to upload more license. Try again later.")));
                        }
                    }
                    else{
                        //If item is already in the cart update its quantity
                        $check_cart_item->qty += $input['quantity'];
                        $check_cart_item->save();
                    }
                }else{
                    $cart_item = new CartItem;
                    $cart_item->cart_id = $cart->id;
                    $cart_item->product_id = $product->id;
                    $cart_item->variation_id = $variation_id;
                    $cart_item->is_variable = $variation_id == null ? 0 : 1;
                    $cart_item->unit_price = $unit_price;
                    $cart_item->qty = $input['quantity'];
                    $cart_item->save();

                }
                if(Auth::user()->cart->coupon_code != null){
                    return $this->applyCoupon(Auth::user()->cart->coupon_code);
                }
            }
            // If the user is not logged in set the Cart session
            else{
                $session_cart_item = (object)array();
                $session_cart_item->product_id = $product->id;
                $session_cart_item->variation_id = $variation_id;
                $session_cart_item->is_variable = $variation_id == null ? 0 : 1;
                $session_cart_item->unit_price = $unit_price;
                $session_cart_item->qty = $input['quantity'];

                // Get the current cart session if not instantiate with [] and push items
                $current_session_cart_items  = Session::get('cart_items');
                $current_session_cart_items  = $current_session_cart_items != null ? $current_session_cart_items : [] ;
                $check_current_session = false;
                foreach($current_session_cart_items as $key => $value)
                {
                    if( $value->product_id == $product->id && $value->variation_id == $variation_id )
                    {
                        $current_session_cart_items[$key]->qty += $input['quantity'];
                        $check_current_session = true;
                        break;
                    }
                }
                if(!$check_current_session){
                    array_push($current_session_cart_items, $session_cart_item);
                }
                Session::put('cart_items', $current_session_cart_items );
                if(Session::get('coupon_code') != null){
                    return $this->applyCoupon(Session::get('coupon_code'));
                }
            }

        }
        else
        {
            return redirect()->back()->with(session()->flash('alert-error', __('Something went wrong. Please try again')));
        }
        if(Auth::user()){
            if(Auth::user()->email_verified_at == null) {
               // Session::flash('alert-warning', __('Your email is unverified! Kindly verify your email.'));
            }
        }
        // Session::flash('alert-success', __('Product added to cart.'));

        return redirect()->route('frontside.shop.cart');
    }

    /**
     * Remove the cart item from cart
     *
     */

    public function removeFromCart($id)
    {
        try {
            $id = Hashids::decode($id)[0];
        } catch (\Throwable $th) {
            return redirect()->route('frontside.shop.cart')->with(session()->flash('alert-error', __('Something went wrong. Please try again')));
        }
        if(Auth::user()){
            CartItem::where('id', $id)->delete();
        }else{
            $session_cart_items = Session::get('cart_items');
            unset($session_cart_items[$id]);
            Session::put('cart_items',$session_cart_items);
        }
        return redirect()->route('frontside.shop.cart')->with(session()->flash('alert-success', __('Cart item removed.')));
    }

    /**
     * Update cart item quantity
     *
     */

    public function updateCartItemQty($id,$qty)
    {
        $data = [];
        $data['success'] = 'false';
        $data['item_removed'] = 'false';
        try {
            $id = Hashids::decode($id)[0];
        } catch (\Throwable $th) {
            $data['success'] = 'false';
            return $data;
        }
        if(Auth::user()){
            CartItem::where('id', $id)->update(['qty'=>$qty]);
            if($qty == 0)
            {
                CartItem::where('id', $id)->delete();
                $data['item_removed'] = 'true';
                $data['item_id'] = Hashids::encode($id);
            }
            $data['success'] = 'true';

        }else{
            $session_cart_items =  Session::get('cart_items');
            $session_cart_items[$id]->qty = $qty;
            if($session_cart_items[$id]->qty == 0){
                unset($session_cart_items[$id]);
                $data['item_removed'] = 'true';
                $data['item_id'] = Hashids::encode($id);
            }
            $data['success'] = 'true';
            Session::put('cart_items', $session_cart_items);
        }

        return $data;
    }

    /**
     * Get Cart Total
     *
     */

    public function getCartTotal($id)
    {

        $default_currency = \App\Models\Currency::where('is_active', 1)->where('is_default', 1)->first();
        $data = [];

        if(Auth::user()){
            $data['default_vat'] = 0;
        }else{
            $data['default_vat'] = \App\Models\SiteSettings::first()->defualt_vat;
        }
        $subtotal= 0;
        try {
            $id = Hashids::decode($id)[0];
        } catch (\Throwable $th) {
            $data['success'] = 'false';
            return $data;
        }
        $ip_info = ip_info();
        $default_vat = \App\Models\SiteSettings::first()->defualt_vat;
        if(Auth::user()){
            $vat_percentage = $default_vat;
            $vat_percentage = Auth::user()->contact->contact_countries->vat_in_percentage;
            if(Auth::user()->contact->contact_countries->is_default_vat == 1)
            {
                $vat_percentage = $default_vat;
            }
        }
        else
        {
            $vat_percentage = $default_vat;
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
            }
        }
        if(Auth::user()){
            $data['success'] = 'true';
            $cart = Cart::where('id', $id)->first();
            $data['cart_subtotal'] = currency_format($cart->total['subtotal'],'','',1);
            $data['cart_taxtotal'] = currency_format(($cart->total['taxtotal'] - ($cart->total['subtotal'] * $vat_percentage / 100 )),'','',1);
            $data['cart_vattotal'] = currency_format(($cart->total['subtotal'] * $vat_percentage / 100 ),'','',1);
            // $data['cart_grandtotal'] = currency_format(($cart->total['grandtotal'] + ($cart->total['subtotal'] * $vat_percentage / 100) ),'','',1);
            $data['cart_grandtotal'] = currency_format($cart->total['grandtotal'] ,'','',1);

            $data['currency'] = Session::get('currency_symbol') == null ? $default_currency->symbol : Session::get('currency_symbol');
            $data['currency_code'] = Session::get('currency_code') == null ? $default_currency->code : Session::get('currency_code');
        }else{
            $data['success'] = 'true';

            $data['cart_subtotal'] = 0;
            $data['cart_grandtotal'] = 0;
            $data['cart_taxtotal'] = 0;
            $data['cart'] = (object)array( "cart_items" => Session::get('cart_items') );

            foreach($data['cart']->cart_items as $key => $cart_item){
                $cart_item->id = $key;
                $cart_item->product = Products::where('id', $cart_item->product_id)->first();
                $cart_item->variation = ProductVariation::where('id', $cart_item->variation_id)->first();
                $unit_price = (currency_format($cart_item->unit_price * (Session::get('exchange_rate') ? Session::get('exchange_rate') : 1),'','',1));
                $subtotal = $cart_item->qty * $unit_price;
                $data['cart_grandtotal'] += $subtotal;
                $data['cart_subtotal'] += 0;

                $taxes = $cart_item->product->customer_taxes;
                foreach($taxes as $tax)
                {
                    if($tax->tax != null){
                        switch($tax->tax->computation)
                        {
                            case 0:
                                $data['cart_taxtotal'] += $tax->tax->amount;
                                $data['cart_grandtotal'] += $tax->tax->amount;
                                break;
                            case 1:
                                $data['cart_taxtotal'] += $subtotal * $tax->tax->amount  / 100;
                                $data['cart_grandtotal'] += $subtotal * $tax->tax->amount / 100;
                                break;
                        }
                    }
                }
                $data['cart_subtotal'] += $subtotal;
                $data['cart_taxtotal'] += $subtotal * $vat_percentage  / 100;
                $data['cart_grandtotal'] += $subtotal * $vat_percentage / 100;
            }
            $data['cart_taxtotal'] = currency_format(($data['cart_taxtotal'] -($data['cart_subtotal'] * $vat_percentage / 100 ) ) ,'','',1);
            $data['cart_grandtotal'] = currency_format($data['cart_grandtotal'],'','',1);
            $data['cart_subtotal'] = currency_format($data['cart_subtotal'],'','',1);
            $data['cart_vattotal'] = currency_format(($data['cart_subtotal'] * $vat_percentage / 100),'','',1);

            $data['currency'] = Session::get('currency_symbol') == null ? $default_currency->symbol : Session::get('currency_symbol');
            $data['currency_code'] = Session::get('currency_code') == null ? $default_currency->code : Session::get('currency_code');
        }
        return $data;
    }

    /**
     * Show cart page
     *
     */
    public function cart(Request $request)
    {
        $data = [];
        $subtotal = 0;
        $ip_info = ip_info($request->ip());
        $default_vat = \App\Models\SiteSettings::first()->defualt_vat;
        $vat_percentage = $default_vat;
        $vat_label = __('VAT');
        if(Auth::user()){

            if(Auth::user()->contact->contact_countries->is_default_vat != 1)
            {
                $vat_percentage = Auth::user()->contact->contact_countries->vat_in_percentage;
                $vat_label = Auth::user()->contact->contact_countries->vat_label ? Auth::user()->contact->contact_countries->vat_label : 'VAT';
                // $vat_label  = translation(  Auth::user()->contact->contact_countries->id,32,app()->getLocale(),'vat_label',$vat_label);
            }
        }
        else
        {
            $vat_percentage = $default_vat;
            if(isset($ip_info['country_code'])){
                
                $vat_percentage = ContactCountry::where('country_code', $ip_info['country_code'])->first() ? ContactCountry::where('country_code', $ip_info['country_code'])->first()->vat_in_percentage : $default_vat;
                $vat_label = ContactCountry::where('country_code', $ip_info['country_code'])->first()->vat_label ? ContactCountry::where('country_code', $ip_info['country_code'])->first()->vat_label : 'VAT';
                $cc = ContactCountry::where('country_code', $ip_info['country_code'])->first();
                if($cc){
                    if($cc->is_default_vat == 1)
                    {
                        $vat_percentage = $default_vat;
                        $vat_label = __('VAT');
                        // $vat_label = __($vat_label);
                    }
                }
            }
        }
        if(Auth::user()){
            $data['cart'] = Cart::with(
                        'cart_items',
                        'cart_items.product',
                        'cart_items.variation'
                    )->where('customer_id', Auth::user()->id)->where('is_checkout',0)->first();
            if($data['cart']){
                if(count($data['cart']->cart_items) == 0){
                    $data['cart']->coupon_code = null;
                    $data['cart']->save();
                }
                $data['coupon_code'] =  $data['cart']->coupon_code;
            }
        }else{
            $data['cart'] = (object)array( "cart_items" => Session::get('cart_items') ? Session::get('cart_items') : [] );
            if(count($data['cart']->cart_items) == 0){
                Session::put('coupon_code', null);
            }
            $data['coupon_code'] = Session::get('coupon_code');
            $total = [];
            $total['subtotal'] = 0;
            $total['grandtotal'] = 0;
            $total['taxtotal'] = 0;
            foreach($data['cart']->cart_items as $key => $cart_item){

                $cart_item->id = $key;

                $cart_item->product = Products::where('id', $cart_item->product_id)->first();
                $cart_item->variation = ProductVariation::where('id', $cart_item->variation_id)->first();

                $unit_price = $cart_item->unit_price * (Session::get('exchange_rate') ? Session::get('exchange_rate') : 1);
                $subtotal = $cart_item->qty * currency_format($unit_price,'','',1);

                $total['grandtotal'] += $subtotal;
                $total['subtotal'] += 0;

                $taxes = $cart_item->product->customer_taxes;
                foreach($taxes as $tax)
                {
                    if($tax->tax != null){
                        switch($tax->tax->computation)
                        {
                            case 0:
                                $total['taxtotal'] += $tax->tax->amount;
                                $total['grandtotal'] += $tax->tax->amount;
                                break;
                            case 1:
                                $total['taxtotal'] += $subtotal * $tax->tax->amount  / 100;
                                $total['grandtotal'] += $subtotal * $tax->tax->amount / 100;
                                break;
                        }
                    }
                }
                $total['taxtotal'] += $subtotal * $vat_percentage  / 100;

                $total['grandtotal'] += $subtotal * $vat_percentage / 100;
                $total['subtotal'] += $subtotal;
            }

            $data['vat_percentage'] = $vat_percentage;
            $data['cart']->total = $total;
            $data['cart']->id = 0;
        }
        if(!isset($data['cart']->cart_items) || count($data['cart']->cart_items) == 0){
            Session::flash('alert-warning', __('Add Products to fill the cart.'));
        }
        $data['vat_percentage'] = $vat_percentage;
        $data['coupon'] = null;
        $data['discount_percentage'] = 0;
        if( isset($data['coupon_code']) && $data['coupon_code'] != null )
        {
            $data['coupon'] = ProductPricelist::with('rules')->whereHas('configuration',function($q) use($data){
                $q->where('promotion_code', $data['coupon_code']);
            })->first();
            if($data['coupon']->parent_id == null){
                $data['discount_percentage'] = $data['coupon']->rules[0]->percentage_value;
            }else{
                $c = ProductPricelist::with('rules')->where('id',$data['coupon']->parent_id)->first();
                $data['discount_percentage'] = $c->rules[0]->percentage_value;
            }
        }
        $data['vat_label'] = __($vat_label);
        return view('frontside.shop.cart',$data);
    }

    /**
     * Apply Coupon
     *
     */
    public function applyCoupon($coupon_code)
    {
        $pricelistconfiguration_query = null;
        if(Auth::user())
        {
            $user_country_id = Auth::user()->contact->country_id;
            // dd($user_country_id);
            $cart = Cart::where('id', Auth::user()->cart->id)->where('is_checkout', 0)->first();

            // $pricelistconfiguration_query = ProductPricelistConfiguration::orderBy('product_pricelist_configurations.id', 'asc');
            $pricelistconfiguration_query = ProductPricelistConfiguration::where('promotion_code', $coupon_code);
            if($user_country_id ){
                $pricelistconfiguration_query->leftjoin('contact_country_groups','product_pricelist_configurations.country_group_id','contact_country_groups.id');
                $pricelistconfiguration_query->leftjoin('contact_countries_contact_countries_groups','contact_countries_contact_countries_groups.country_group_id','contact_country_groups.id');
                $pricelistconfiguration_query->where(function($q) use($user_country_id){
                    $q->where('product_pricelist_configurations.country_id', $user_country_id);
                    $q->orWhere('product_pricelist_configurations.country_id', null);
                    $q->orWhere('contact_countries_contact_countries_groups.country_id',$user_country_id);
                    $q->orWhere('contact_countries_contact_countries_groups.country_id',$user_country_id);
                    $q->orWhere('contact_countries_contact_countries_groups.country_id',$user_country_id);
                });
            }
            $pricelistconfiguration = $pricelistconfiguration_query->first();
            // If the coupon code is correct
            if($pricelistconfiguration){
                if( $cart )
                {
                    $pricelist_id = $pricelistconfiguration->pricelist_id;
                    if($pricelistconfiguration->priceList->parent_id != null){
                        $pricelist_id = $pricelistconfiguration->priceList->parent_id;
                        $check_usage = Quotation::where('pricelist_id', $pricelistconfiguration->priceList->id)->first();
                        if($check_usage){
                            if( $cart )
                            {
                                $cart_items = Auth::user()->cart->cart_items;
                                // Iterate through the given order lines
                                foreach( $cart_items as $cart_item )
                                {
                                    $original_price = $cart_item->product->generalInformation->sales_price;
                                    if($cart_item->variation->variation_sales_price == null ){
                                        foreach($cart_item->variation_details as $variation_detail)
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
                                        $original_price = $cart_item->variation->variation_sales_price;
                                    }
                                    CartItem::where('id', $cart_item->id)->update( [ 'unit_price' => $original_price ] );
                                }
                            }
                            return redirect()->route('frontside.shop.cart')->with(session()->flash('alert-success', __('Coupon has been used before')));
                        }
                    }
                    $pricelist = ProductPriceList::with('rules')->where('id', $pricelist_id)->first();

                    $cart_items = Auth::user()->cart->cart_items;
                    // Iterate through the given order lines
                    foreach( $cart_items as $cart_item )
                    {
                        // if the order line consists of variation
                        if( $cart_item->variation_id != null )
                        {
                            // PriceList rule with the @pricelist_id and @cart_item->variation_id
                            $pricelistrule = ProductPricelistRule::where('pricelist_id',$pricelist_id)->where('apply_on',3)->where('variation_id', $cart_item->variation_id)->orderBy('id','desc')->where('pricelist_id', $pricelist_id)->first();

                            // If the above price list is not available find product specific rule
                            if( !$pricelistrule )
                            {
                                // PriceList rule with the @pricelist_id and @cart_item->product_id
                                $pricelistrule = ProductPricelistRule::where('pricelist_id',$pricelist_id)->where('apply_on',2)->where('product_id', $cart_item->product_id)->where('pricelist_id', $pricelist_id)->orderBy('id','desc')->first();
                            }

                            // If the above price list is not available find product category rule
                            if( !$pricelistrule )
                            {
                                // PriceList rule with the @pricelist_id and @cart_item->product->generalInformation->eccomerce_category
                                $pricelistrule = ProductPricelistRule::where('pricelist_id',$pricelist_id)->where('apply_on',1)->where('category_id', $cart_item->product->generalInformation->eccomerce_category)->where('pricelist_id', $pricelist_id)->orderBy('id','desc')->first();
                            }

                            // If the above price list is not available find all product rule
                            if( !$pricelistrule )
                            {
                                // PriceList rule with the @pricelist_id and All Products
                                $pricelistrule = ProductPricelistRule::where('pricelist_id',$pricelist_id)->where('apply_on',0)->orderBy('id','desc')->where('pricelist_id', $pricelist_id)->first();
                            }
                            // If there is any applicable pricelist rule for the cart_item
                            if( $pricelistrule )
                            {
                                // If Price Computation is Fixed Price change the unit price of order line to the amount given
                                if( $pricelistrule->price_computation == 0 )
                                {
                                    $startDate = \Carbon\Carbon::parse($pricelist->start_date);
                                    $endDate = \Carbon\Carbon::parse($pricelist->end_date);
                                    $check = \Carbon\Carbon::now()->between($startDate,$endDate);

                                    if(
                                        ( $cart_item->qty >= $pricelistrule->min_qty )
                                        && $check == 1
                                    ){
                                        CartItem::where('id', $cart_item->id)->update( [ 'unit_price' => $pricelistrule->fixed_value ] );
                                    }else{

                                        CartItem::where('id', $cart_item->id)->update( [ 'unit_price' => $cart_item->product->generalInformation->sales_price ] );
                                    }
                                }
                                // Else if the Price Computation is percentage deduce the percentage and replace the unit price
                                elseif( $pricelistrule->price_computation == 1 )
                                {
                                    $startDate = \Carbon\Carbon::parse($pricelist->start_date)->subDay();
                                    $endDate = \Carbon\Carbon::parse($pricelist->end_date)->addDay();
                                    $check = \Carbon\Carbon::now()->between($startDate,$endDate);
                                    if(
                                        ( $cart_item->qty >= $pricelistrule->min_qty )
                                        && $check == 1
                                        ){
                                        $original_price = $cart_item->product->generalInformation->sales_price;
                                        if($cart_item->variation->variation_sales_price == null ){
                                            foreach($cart_item->variation_details as $variation_detail)
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
                                            $original_price = $cart_item->variation->variation_sales_price;
                                        }
                                        $new_price = $original_price - ( $original_price * $pricelistrule->percentage_value / 100 );
                                        CartItem::where('id', $cart_item->id)->update( [ 'unit_price' => $new_price ] );
                                    }else{
                                        $original_price = $cart_item->product->generalInformation->sales_price;
                                        if($cart_item->variation->variation_sales_price == null ){
                                            foreach($cart_item->variation_details as $variation_detail)
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
                                            $original_price = $cart_item->variation->variation_sales_price;
                                        }
                                        CartItem::where('id', $cart_item->id)->update( [ 'unit_price' => $original_price ] );

                                    }
                                }
                            }else{
                                $original_price = $cart_item->product->generalInformation->sales_price;
                                if($cart_item->variation->variation_sales_price == null ){
                                    foreach($cart_item->variation_details as $variation_detail)
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
                                    $original_price = $cart_item->variation->variation_sales_price;
                                }
                                CartItem::where('id', $cart_item->id)->update( [ 'unit_price' => $new_price ] );
                            }
                        }
                        // if the order line consists of product
                        elseif( $cart_item->product_id != null )
                        {
                            // PriceList rule with the @pricelist_id and @cart_item->product_id
                            $pricelistrule = ProductPricelistRule::where('pricelist_id',$pricelist_id)->where('apply_on',2)->where('product_id', $cart_item->product_id)->orderBy('id','desc')->first();

                            // If the above price list is not available find product category rule
                            if( !$pricelistrule )
                            {
                                // PriceList rule with the @pricelist_id and @cart_item->product->generalInformation->eccomerce_category
                                $pricelistrule = ProductPricelistRule::where('pricelist_id',$pricelist_id)->where('apply_on',1)->where('category_id', $cart_item->product->generalInformation->eccomerce_category)->orderBy('id','desc')->first();
                            }

                            // If the above price list is not available find all product rule
                            if( !$pricelistrule )
                            {
                                // PriceList rule with the @pricelist_id and All Products
                                $pricelistrule = ProductPricelistRule::where('pricelist_id',$pricelist_id)->where('apply_on',0)->orderBy('id','desc')->first();
                            }

                            // If there is any applicable pricelist rule for the cart_item
                            if( $pricelistrule )
                            {
                                // If Price Computation is Fixed Price change the unit price of order line to the amount given
                                if( $pricelistrule->price_computation == 0 )
                                {
                                    $startDate = \Carbon\Carbon::parse($pricelist->start_date)->subDay();
                                    $endDate = \Carbon\Carbon::parse($pricelist->end_date)->addDay();
                                    $check = \Carbon\Carbon::now()->between($startDate,$endDate);
                                    if(
                                        ( $cart_item->qty >= $pricelistrule->min_qty )
                                        && $check == 1
                                    ){
                                        CartItem::where('id', $cart_item->id)->update( [ 'unit_price' => $pricelistrule->fixed_value ] );
                                    }
                                }
                                // Else if the Price Computation is percentage deduce the percentage and replace the unit price
                                elseif( $pricelistrule->price_computation == 1 )
                                {
                                    $startDate = \Carbon\Carbon::parse($pricelist->start_date)->subDay();
                                    $endDate = \Carbon\Carbon::parse($pricelist->end_date)->addDay();
                                    $check = \Carbon\Carbon::now()->between($startDate,$endDate);
                                    if(
                                        ( $cart_item->qty >= $pricelistrule->min_qty )
                                        && $check == 1
                                    ){
                                        $original_price = $cart_item->product->generalInformation->sales_price;
                                        if($cart_item->variation->variation_sales_price == null ){
                                            foreach($cart_item->variation_details as $variation_detail)
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
                                            $original_price = $cart_item->variation->variation_sales_price;
                                        }
                                        $new_price = $original_price - ( $original_price * $pricelistrule->percentage_value / 100 );
                                        CartItem::where('id', $cart_item->id)->update( [ 'unit_price' => $new_price ] );
                                    }
                                }
                            }else{
                                $original_price = $cart_item->product->generalInformation->sales_price;
                                if($cart_item->variation->variation_sales_price == null ){
                                    foreach($cart_item->variation_details as $variation_detail)
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
                                    $original_price = $cart_item->variation->variation_sales_price;
                                }
                                CartItem::where('id', $cart_item->id)->update( [ 'unit_price' => $new_price ] );
                            }
                        }
                    }
                    Cart::where('id', Auth::user()->cart->id)->update(['coupon_code' => $coupon_code, 'pricelist_id' => $pricelistconfiguration->priceList->id]);
                    return redirect()->route('frontside.shop.cart')->with(session()->flash('alert-success', __('Coupon applied successfully')));
                }
            }
            else {
                if( $cart )
                {
                    $cart_items = Auth::user()->cart->cart_items;
                    // Iterate through the given order lines
                    foreach( $cart_items as $cart_item )
                    {
                        $original_price = $cart_item->product->generalInformation->sales_price;
                        if($cart_item->variation->variation_sales_price == null ){
                            foreach($cart_item->variation_details as $variation_detail)
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
                            $original_price = $cart_item->variation->variation_sales_price;
                        }
                        CartItem::where('id', $cart_item->id)->update( ['unit_price' => $original_price ] );
                    }
                    Cart::where('id', Auth::user()->cart->id)->update(['coupon_code' => null]);
                }
                return redirect()->route('frontside.shop.cart')->with(session()->flash('alert-error', __('Invalid coupon entered')));
            }
        }else{
            $pricelist_id = null;
            $cart = (object)array( "cart_items" => Session::get('cart_items') );
            foreach($cart->cart_items as $key => $cart_item){
                $cart_item->id = $key;
                $cart_item->product = Products::where('id', $cart_item->product_id)->first();
                $cart_item->variation = ProductVariation::where('id', $cart_item->variation_id)->first();
            }
            $pricelistconfiguration_query = ProductPricelistConfiguration::where('promotion_code', $coupon_code);
            $pricelistconfiguration_query->where(function($q){
                $q->whereHas('country',function($query){
                    $query->where('country_code', ip_info("202.166.170.106", "Country Code"));
                });
                $q->orWhere('country_id',null);
            });
            $pricelistconfiguration = $pricelistconfiguration_query->first();

            if( count($cart->cart_items) > 0 )
            {
                if($pricelistconfiguration){
                    $pricelist_id = $pricelistconfiguration->pricelist_id;
                    if($pricelistconfiguration->priceList->parent_id != null){
                        $pricelist_id = $pricelistconfiguration->priceList->parent_id;
                        $check_usage = Quotation::where('pricelist_id', $pricelistconfiguration->priceList->id)->first();
                        if($check_usage){
                            if($cart){
                                foreach( $cart->cart_items as $ind => $cart_item )
                                {
                                    $cart_item->variation_details = ProductVariationDetail::where('product_variation_id', $cart_item->variation_id)->get();
                                    $original_price = $cart_item->product->generalInformation->sales_price;
                                    if($cart_item->variation->variation_sales_price == null ){
                                        foreach($cart_item->variation_details as $variation_detail)
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
                                        $original_price = $cart_item->variation->variation_sales_price;
                                    }
                                    $cart->cart_items[$ind]->unit_price = $original_price;
                                    unset($cart_item->product);
                                    unset($cart_item->variation);
                                    unset($cart_item->variation_details);
                                }
                            }
                            Session::put('coupon_code', null);
                            return redirect()->route('frontside.shop.cart')->with(session()->flash('alert-success', __('Coupon has been used before')));
                        }
                    }
                    $pricelist = ProductPriceList::with('rules')->where('id', $pricelist_id)->first();
                    $cart_items = $cart->cart_items;

                    foreach( $cart_items as $ind => $cart_item ){
                        $cart_item->variation_details = ProductVariationDetail::where('product_variation_id', $cart_item->variation_id)->get();
                        if( $cart_item->variation_id != null )
                        {
                            // PriceList rule with the @pricelist_id and @cart_item->variation_id
                            $pricelistrule = ProductPricelistRule::where('pricelist_id',$pricelist_id)->where('apply_on',3)->where('variation_id', $cart_item->variation_id)->orderBy('id','desc')->first();

                            // If the above price list is not available find product specific rule
                            if( !$pricelistrule )
                            {
                                // PriceList rule with the @pricelist_id and @cart_item->product_id
                                $pricelistrule = ProductPricelistRule::where('pricelist_id',$pricelist_id)->where('apply_on',2)->where('product_id', $cart_item->product_id)->orderBy('id','desc')->first();
                            }

                            // If the above price list is not available find product category rule
                            if( !$pricelistrule )
                            {
                                // PriceList rule with the @pricelist_id and @cart_item->product->generalInformation->eccomerce_category
                                $pricelistrule = ProductPricelistRule::where('pricelist_id',$pricelist_id)->where('apply_on',1)->where('category_id', $cart_item->product->generalInformation->eccomerce_category)->orderBy('id','desc')->first();
                            }

                            // If the above price list is not available find all product rule
                            if( !$pricelistrule )
                            {
                                // PriceList rule with the @pricelist_id and All Products
                                $pricelistrule = ProductPricelistRule::where('pricelist_id',$pricelist_id)->where('apply_on',0)->orderBy('id','desc')->first();
                            }
                            // If there is any applicable pricelist rule for the cart_item
                            if( $pricelistrule )
                            {

                                // If Price Computation is Fixed Price change the unit price of order line to the amount given
                                if( $pricelistrule->price_computation == 0 )
                                {
                                    $startDate = \Carbon\Carbon::parse($pricelist->start_date);
                                    $endDate = \Carbon\Carbon::parse($pricelist->end_date);
                                    $check = \Carbon\Carbon::now()->between($startDate,$endDate);

                                    if(
                                        ( $cart_item->qty >= $pricelistrule->min_qty )
                                        && $check == 1
                                    ){
                                        $cart->cart_items[$ind]->unit_price = $pricelistrule->fixed_value;
                                        // CartItem::where('id', $cart_item->id)->update( [ 'unit_price' => $pricelistrule->fixed_value ] );
                                    }else{

                                        $cart->cart_items[$ind]->unit_price = $cart_item->product->generalInformation->sales_price;
                                        // CartItem::where('id', $cart_item->id)->update( [ 'unit_price' => $cart_item->product->generalInformation->sales_price ] );
                                    }
                                }
                                // Else if the Price Computation is percentage deduce the percentage and replace the unit price
                                elseif( $pricelistrule->price_computation == 1 )
                                {
                                    $startDate = \Carbon\Carbon::parse($pricelist->start_date)->subDay();
                                    $endDate = \Carbon\Carbon::parse($pricelist->end_date)->addDay();
                                    $check = \Carbon\Carbon::now()->between($startDate,$endDate);
                                    if(
                                        ( $cart_item->qty >= $pricelistrule->min_qty )
                                        && $check == 1
                                        ){
                                        $original_price = $cart_item->product->generalInformation->sales_price;
                                        if($cart_item->variation->variation_sales_price == null ){
                                            foreach($cart_item->variation_details as $variation_detail)
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
                                            $original_price = $cart_item->variation->variation_sales_price;
                                        }
                                        $new_price = $original_price - ( $original_price * $pricelistrule->percentage_value / 100 );
                                        $cart->cart_items[$ind]->unit_price = $new_price;
                                        // CartItem::where('id', $cart_item->id)->update( [ 'unit_price' => $new_price ] );
                                    }else{
                                        $original_price = $cart_item->product->generalInformation->sales_price;
                                        if($cart_item->variation->variation_sales_price == null ){
                                            foreach($cart_item->variation_details as $variation_detail)
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
                                            $original_price = $cart_item->variation->variation_sales_price;
                                        }
                                        $cart->cart_items[$ind]->unit_price = $original_price;
                                        // CartItem::where('id', $cart_item->id)->update( [ 'unit_price' => $original_price ] );

                                    }
                                }
                            }else{
                                $original_price = $cart_item->product->generalInformation->sales_price;
                                if($cart_item->variation->variation_sales_price == null ){
                                    foreach($cart_item->variation_details as $variation_detail)
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
                                    $original_price = $cart_item->variation->variation_sales_price;
                                }
                                $cart->cart_items[$ind]->unit_price = $original_price;
                                // CartItem::where('id', $cart_item->id)->update( [ 'unit_price' => $new_price ] );
                            }
                        }
                        elseif( $cart_item->product_id != null )
                        {
                            // PriceList rule with the @pricelist_id and @cart_item->product_id
                            $pricelistrule = ProductPricelistRule::where('pricelist_id',$pricelist_id)->where('apply_on',2)->where('product_id', $cart_item->product_id)->orderBy('id','desc')->first();

                            // If the above price list is not available find product category rule
                            if( !$pricelistrule )
                            {
                                // PriceList rule with the @pricelist_id and @cart_item->product->generalInformation->eccomerce_category
                                $pricelistrule = ProductPricelistRule::where('pricelist_id',$pricelist_id)->where('apply_on',1)->where('category_id', $cart_item->product->generalInformation->eccomerce_category)->orderBy('id','desc')->first();
                            }

                            // If the above price list is not available find all product rule
                            if( !$pricelistrule )
                            {
                                // PriceList rule with the @pricelist_id and All Products
                                $pricelistrule = ProductPricelistRule::where('pricelist_id',$pricelist_id)->where('apply_on',0)->orderBy('id','desc')->first();
                            }

                            // If there is any applicable pricelist rule for the cart_item
                            if( $pricelistrule )
                            {
                                // If Price Computation is Fixed Price change the unit price of order line to the amount given
                                if( $pricelistrule->price_computation == 0 )
                                {
                                    $startDate = \Carbon\Carbon::parse($pricelist->start_date)->subDay();
                                    $endDate = \Carbon\Carbon::parse($pricelist->end_date)->addDay();
                                    $check = \Carbon\Carbon::now()->between($startDate,$endDate);
                                    if(
                                        ( $cart_item->qty >= $pricelistrule->min_qty )
                                        && $check == 1
                                    ){
                                        $cart->cart_items[$ind]->unit_price = $pricelistrule->fixed_value;

                                        // CartItem::where('id', $cart_item->id)->update( [ 'unit_price' => $pricelistrule->fixed_value ] );
                                    }
                                }
                                // Else if the Price Computation is percentage deduce the percentage and replace the unit price
                                elseif( $pricelistrule->price_computation == 1 )
                                {
                                    $startDate = \Carbon\Carbon::parse($pricelist->start_date)->subDay();
                                    $endDate = \Carbon\Carbon::parse($pricelist->end_date)->addDay();
                                    $check = \Carbon\Carbon::now()->between($startDate,$endDate);
                                    if(
                                        ( $cart_item->qty >= $pricelistrule->min_qty )
                                        && $check == 1
                                    ){
                                        $original_price = $cart_item->product->generalInformation->sales_price;
                                        if($cart_item->variation->variation_sales_price == null ){

                                            foreach($cart_item->variation_details as $variation_detail)
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
                                            $original_price = $cart_item->variation->variation_sales_price;
                                        }
                                        $new_price = $original_price - ( $original_price * $pricelistrule->percentage_value / 100 );
                                        $cart->cart_items[$ind]->unit_price = $new_price;

                                        // CartItem::where('id', $cart_item->id)->update( [ 'unit_price' => $new_price ] );
                                    }
                                }
                            }else{
                                $original_price = $cart_item->product->generalInformation->sales_price;
                                if($cart_item->variation->variation_sales_price == null ){
                                    foreach($cart_item->variation_details as $variation_detail)
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
                                    $original_price = $cart_item->variation->variation_sales_price;
                                }
                                $cart->cart_items[$ind]->unit_price = $original_price;

                                // CartItem::where('id', $cart_item->id)->update( [ 'unit_price' => $new_price ] );
                            }
                        }

                        unset($cart_item->product);
                        unset($cart_item->variation);
                        unset($cart_item->variation_details);
                    }
                }else{
                    foreach( $cart->cart_items as $ind => $cart_item )
                    {
                        $cart_item->variation_details = ProductVariationDetail::where('product_variation_id', $cart_item->variation_id)->get();
                        $original_price = $cart_item->product->generalInformation->sales_price;
                        if($cart_item->variation->variation_sales_price == null ){
                            foreach($cart_item->variation_details as $variation_detail)
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
                            $original_price = $cart_item->variation->variation_sales_price;
                        }
                        $cart->cart_items[$ind]->unit_price = $original_price;
                        unset($cart_item->product);
                        unset($cart_item->variation);
                        unset($cart_item->variation_details);
                        return redirect()->route('frontside.shop.cart')->with(session()->flash('alert-error', __('Invalid coupon entered')));
                    }
                }
            }

            $pricelistconfiguration_query = ProductPricelistConfiguration::where('promotion_code', $coupon_code);
            $pricelist_id = $pricelistconfiguration->pricelist_id;
            Session::put('cart_items', $cart->cart_items);
            Session::put('coupon_code', $coupon_code);
            Session::put('pricelist_id', $pricelistconfiguration->priceList->id);


            // Cart::where('id', Auth::user()->cart->id)->update(['coupon_code' => $coupon_code, 'pricelist_id' => $pricelist_id]);
        }

        return redirect()->route('frontside.shop.cart')->with(session()->flash('alert-success', __('Coupon applied successfully')));

    }


}
