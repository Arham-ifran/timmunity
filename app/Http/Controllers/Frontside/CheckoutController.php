<?php

namespace App\Http\Controllers\Frontside;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Products;
use App\Models\ProductVariation;
use App\Models\Contact;
use App\Models\User;
use App\Models\ContactCountry;
use App\Models\Quotation;
use App\Models\QuotationOrderLine;
use App\Models\QuotationOrderLineTax;
use App\Models\QuotationOtherInfo;
use App\Models\ContactFedState;
use App\Models\ContactAddress;
use App\Models\Invoice;
use App\Models\License;
use App\Models\InvoiceOrderLine;
use App\Models\EmailTemplate;
use App\Models\InvoicePaymentHistory;
use Auth;
use Session;
use Hashids;
use PDF;
use File;
use App\Http\Traits\PaymentTrait;
use App\Models\ProductPriceList;

class CheckoutController extends Controller
{
    use PaymentTrait;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware(['auth','verified']);
    }
    public function checkout(Request $request)
    {
        $data = [];
        $data['countries'] = ContactCountry::all();
        $data['contact_fed_states'] = ContactFedState::all();
        $ip_info = ip_info();
        $default_vat = \App\Models\SiteSettings::first()->defualt_vat;
        $vat_percentage = $default_vat;
        $vat_label = __('VAT');
        
        if(Auth::user()){
            $vat_percentage = Auth::user()->contact->contact_countries->vat_in_percentage;
            // $vat_label = ( Auth::user()->contact->contact_countries->vat_label != null && Auth::user()->contact->contact_countries->vat_label != '' ) ? translation(  Auth::user()->contact->contact_countries->id,32,app()->getLocale(),'vat_label',$vat_label) : __('VAT');
            $vat_label = ( Auth::user()->contact->contact_countries->vat_label != null && Auth::user()->contact->contact_countries->vat_label != '' ) ? Auth::user()->contact->contact_countries->vat_label : __('VAT');
            
            if(Auth::user()->contact->contact_countries->is_default_vat == 1)
            {
                $vat_percentage = $default_vat;
                $vat_label = __('VAT');
            }
            $data['coupon_code'] =  @Auth::user()->cart->coupon_code;
        }
        else
        {
            if(isset($ip_info['country_code'])){
                $vat_percentage = ContactCountry::where('country_code', $ip_info['country_code'])->first() ? ContactCountry::where('country_code', $ip_info['country_code'])->first()->vat_in_percentage : $default_vat;
                $vat_label = ContactCountry::where('country_code', $ip_info['country_code'])->first()->vat_label ? ContactCountry::where('country_code', $ip_info['country_code'])->first()->vat_label : 'VAT';
                $cc = ContactCountry::where('country_code', $ip_info['country_code'])->first();
               
                if($cc){
                    // $vat_label  = translation(  $cc->id,32,app()->getLocale(),'vat_label',$vat_label);
                    if($cc->is_default_vat == 1)
                    {
                        $vat_percentage = $default_vat;
                        $vat_label = 'VAT';
                        // $vat_label = __($vat_label);
                    }
                }
            }
        }
        if(!Auth::user()){
            $data['cart'] = (object)array( "cart_items" => Session::get('cart_items') ? Session::get('cart_items') : [] );
            $total = [];
            $data['subtotal'] = 0;
            $data['grandtotal'] = 0;
            $data['taxtotal'] = 0;
            foreach($data['cart']->cart_items as $key => $cart_item){
                $cart_item->id = $key;
                $cart_item->product = Products::where('id', $cart_item->product_id)->first();
                $cart_item->variation = ProductVariation::where('id', $cart_item->variation_id)->first();
                $unit_price = currency_format($cart_item->unit_price* (Session::get('exchange_rate') ? Session::get('exchange_rate') : 1),'','',1);
                $subtotal = $cart_item->qty * $unit_price ;
                $data['grandtotal'] += $subtotal;

                $taxes = $cart_item->product->customer_taxes;

                foreach($taxes as $tax)
                {
                    if($tax->tax != null){
                        switch($tax->tax->computation)
                        {
                            case 0:
                                $data['taxtotal'] += $tax->tax->amount;
                                $data['grandtotal'] += $tax->tax->amount;
                                break;
                            case 1:
                                $data['taxtotal'] += $subtotal * $tax->tax->amount  / 100;
                                $data['grandtotal'] += $subtotal * $tax->tax->amount / 100;
                                break;
                        }
                    }
                }
                $data['vat_percentage'] = $vat_percentage;
                $data['taxtotal'] += $subtotal * $vat_percentage  / 100;
                $data['grandtotal'] += $subtotal * $vat_percentage / 100;

                $data['subtotal'] += $subtotal;
            }
            $data['coupon_code'] = Session::get('coupon_code');
        }
        $data['coupon'] = null;
        $data['discount_percentage'] = 0;
        if( isset($data['coupon_code']) && $data['coupon_code'] != null )
        {
            $data['coupon'] = ProductPriceList::with('rules')->whereHas('configuration',function($q) use($data){
                $q->where('promotion_code', $data['coupon_code']);
            })->first();
            if($data['coupon']->parent_id == null){
                $data['discount_percentage'] = $data['coupon']->rules[0]->percentage_value;
            }else{
                $c = ProductPricelist::with('rules')->where('id',$data['coupon']->parent_id)->first();
                $data['discount_percentage'] = $c->rules[0]->percentage_value;
            }
        }
        $data['vat_percentage'] = $vat_percentage;
        $data['vat_label'] = __($vat_label);
        return Auth::user() ? Auth::user()->email_verified_at == null ? view('frontside.shop.checkout',$data)->with(session()->flash('alert-warning', __('Your email is unverified! Kindly verify your email.'))) : view('frontside.shop.checkout',$data) : view('frontside.shop.checkout',$data);
    }
    public function checkoutPost(Request $request)
    {
        // dd('a');
        $quotation = Quotation::first();
        $quotation = null;
        $input = $request->all();
        $vat_label = 'VAT';
        $addresses['billing_id'] = null;
        $addresses['shipping_id'] = null;
        if(Auth::user()){
            // Customer Billing/Shipping Adress details and setting the address_ids for both addresses;
            if($input['existing_address'] == '1')
            {
                $addresses['billing_id'] = (int)$input['address_id'];
                $addresses['shipping_id'] = (int)$input['address_id'];
            }
            // Add new addresses
            else
            {

                $contact_addresses_arr = array(
                    'contact_id' => Auth::user()->contact->id,
                    'type' => 1,
                    'contact_name' => $input['billing']['firstname'],
                    'email' => $input['billing']['email'],
                    'job_position' => null,
                    'phone' => $input['billing']['phone_no'],
                    'mobile' => Auth::user()->mobile,
                    'street_1' => $input['billing']['address'],
                    'street_2' => '',
                    'notes' => '',
                    'city' => $input['billing']['city'],
                    'zipcode' => $input['billing']['zip'],
                    'country_id' => $input['billing']['country_id'] ? $input['billing']['country_id'] : null,
                     'title_id' => null,
                    'state_id' => $input['billing']['state'] ? $input['billing']['state'] : null,
                    'contact_image' => null,

                );
                $address = new ContactAddress();
                $address->fill($contact_addresses_arr)->save();
                $addresses['billing_id']  = $address->id;
                $addresses['shipping_id']  = $address->id;
            }
            if(!isset($input['sameadr']))
            {
                // If user has selected address from provided address
                if($input['existing_address'] == '1')
                {
                    $addresses['shipping_id'] = (int)$input['shipping_address_id'];
                }
                // Add new addresses
                else
                {
                    $contact_addresses_arr = array(
                        'contact_id' => Auth::user()->contact->id,
                        'type' => 2,
                        'contact_name' => $input['shipping']['firstname'],
                        'email' => $input['shipping']['email'],
                        'job_position' => null,
                        'phone' => $input['shipping']['phone_no'],
                        'mobile' => Auth::user()->mobile,
                        'street_1' => $input['shipping']['address'],
                        'street_2' => '',
                        'notes' => '',
                        'city' => $input['shipping']['city'],
                        'zipcode' => $input['shipping']['zip'],
                        'country_id' => $input['shipping']['country_id'] ? $input['shipping']['country_id'] : null,
                         'title_id' => null,
                        'state_id' => $input['shipping']['state'] ? $input['shipping']['state'] : null,
                        'contact_image' => null,

                    );
                    $address = new ContactAddress();
                    $address->fill($contact_addresses_arr)->save();
                    $addresses['shipping_id']  = $address->id;
                }
            }
            $cart = Auth::user()->cart;
            // check if the cart is empty
            if($cart != null)
            {
                // Check if there are cart items in the cart
                if(count($cart->cart_items) > 0)
                {

                    $ip_info = ip_info();
                    $default_vat = \App\Models\SiteSettings::first()->defualt_vat;
                    $vat_percentage = $default_vat;
                    if(Auth::user()){
                        $vat_percentage = Auth::user()->contact->contact_countries->vat_in_percentage;
                        $vat_label = ( Auth::user()->contact->contact_countries->vat_label != null && Auth::user()->contact->contact_countries->vat_label != '' ) ? Auth::user()->contact->contact_countries->vat_label : 'VAT';
                        if(Auth::user()->contact->contact_countries->is_default_vat == 1)
                        {
                            $vat_percentage = $default_vat;
                        }
                    }
                    else
                    {
                        if(isset($ip_info['country_code'])){
                            $vat_percentage = ContactCountry::where('country_code', $ip_info['country_code'])->first() ? ContactCountry::where('country_code', $ip_info['country_code'])->first()->vat_in_percentage : $default_vat;
                            $vat_label = ContactCountry::where('country_code', $ip_info['country_code'])->first()->vat_label ? ContactCountry::where('country_code', $ip_info['country_code'])->first()->vat_label : 'VAT';
                            $cc = ContactCountry::where('country_code', $ip_info['country_code'])->first();
                
                            if($cc){
                                // $vat_label  = translation(  $cc->id,32,app()->getLocale(),'vat_label',$vat_label);
                                if($cc->is_default_vat == 1)
                                {
                                    $vat_percentage = $default_vat;
                                    $vat_label = 'VAT';
                                    // $vat_label = __($vat_label);
                                }
                            }
                        }
                    }
                    // $currency_acceptance = $this->checkCurrencyAcceptibility(Session::get('currency_code'));
                    $currency_acceptance = checkCurrencyAcceptibility(Session::get('currency_code'));
                    // Make New Quotation
                    // dd($cart);
                    $quotation = new Quotation;
                        $quotation->customer_id = Auth::user()->contact->id;
                        $quotation->status = 0;
                        $quotation->invoice_address = $addresses['billing_id'];
                        $quotation->delivery_address = $addresses['shipping_id'];
                        $quotation->pricelist_id = $cart->pricelist_id == null ? 1 :$cart->pricelist_id;
                        $quotation->payment_terms = 1;
                        $quotation->invoice_status = 0;
                        $quotation->status = 0;
                        $quotation->is_quotation_sent = 0;
                        $quotation->is_proforma_quotation_sent = 0;
                        $quotation->is_proforma_quotation_sent = 0;
                        $quotation->is_confirmed_without_kss = 0;
                        $quotation->expires_at = \Carbon\Carbon::now()->format('Y-m-d') ;
                        $quotation->vat_percentage = $vat_percentage;
                        $quotation->currency_symbol = $currency_acceptance ? Session::get('currency_symbol') == null ? \App\Models\Currency::where('is_active', 1)->where('is_default', 1)->first()->symbol : Session::get('currency_symbol') : "€";
                        $quotation->currency = $currency_acceptance ? Session::get('currency_code') == null ? \App\Models\Currency::where('is_active', 1)->where('is_default', 1)->first()->code : Session::get('currency_code') : "EUR";
                        $quotation->exchange_rate = $currency_acceptance ? Session::get('exchange_rate') == null ? \App\Models\Currency::where('is_active', 1)->where('is_default', 1)->first()->exchange_rate : Session::get('exchange_rate') : 1;
                        $quotation->vat_label = $vat_label;
                    $quotation->save();

                    // Make Quotation Other Info instance and save the sales person and sales team data in it for the quotation created
                    $quotation_other_info = new QuotationOtherInfo;
                        $quotation_other_info->quotation_id = $quotation->id;
                        $quotation_other_info->salesperson_id = null;      // Website
                        $quotation_other_info->sales_team_id = 1;           // Website Sales Team to be created by seeder
                    $quotation_other_info->save();
                    // Iterate through all cart items and create quotation order lines coressponding to each cart item and quotation
                    foreach($cart->cart_items as $cart_item)
                    {
                        $product = null;
                        $product_json = '';
                        try {
                            $product = Products::with('customer_taxes')->where('id', $cart_item->product_id)->first();
                        } catch (\Throwable $th) {
                            //throw $th;
                        }
                        $product_varaitions = ProductVariation::with('variation_details')->where('id', $cart_item->variation_id )->first();
                        $product->variation = $product_varaitions;
                        $product_json = (string)$product;

                        // quotation order line
                        $quotation_order_line = new QuotationOrderLine;
                            $quotation_order_line->quotation_id = $quotation->id;
                            $quotation_order_line->product_id = $cart_item->product_id;
                            $quotation_order_line->variation_id = $cart_item->variation_id;
                            $quotation_order_line->description = $product ? $product->description : null;
                            $quotation_order_line->qty = $cart_item->qty;
                            $quotation_order_line->unit_price = $cart_item->unit_price;
                            $quotation_order_line->delivered_qty = 0;
                            $quotation_order_line->invoiced_qty = 0;
                            $quotation_order_line->product_json = $product_json;
                        $quotation_order_line->save();


                        // Customer Taxes corresponding to the product in cart item
                        foreach($product->customer_taxes as $tax)
                        {
                            // Make new Quotation Order Line Tax
                            $order_line_tax = new QuotationOrderLineTax;
                                $order_line_tax->quotation_order_line_id = $quotation_order_line->id;
                                $order_line_tax->tax_id = $tax->tax_id;
                            $order_line_tax->save();
                        }
                    }
                }
            }
            // if there are no cart items redirect to shop page
            else
            {
                return redirect()->route('frontside.shop.index')->with(session()->flash('alert-warning', __('Your cart is empty. Kindly add items in the cart to checkout.')));
            }
        }else{
            $registration_data['email'] = $input['billing']['email'];
            $registration_data['phone_no'] = $input['billing']['phone_no'];
            $registration_data['firstname'] = $input['billing']['firstname'];
            $registration_data['address'] = $input['billing']['address'];
            $registration_data['state'] = $input['billing']['state'];
            $registration_data['zip'] = $input['billing']['zip'];
            $registration_data['country_id'] = $input['billing']['country_id'];
            $registration_data['new_account'] = isset($input['new_account']) ? $input['new_account'] : null ;
            $registration_data['password'] = $input['password'];

            $check_registration_for_guest_and_register = check_registration_for_guest_and_register($registration_data);
            if($check_registration_for_guest_and_register['error'])
            {
                return redirect()->back()->with(session()->flash('alert-warning',__('User already registered.Login to continue')));
            }
            $user_id = $check_registration_for_guest_and_register['user_id'];
            $contact_id = $check_registration_for_guest_and_register['contact_id'];
            $user = $check_registration_for_guest_and_register['user'];


            $contact_addresses_arr = array(
                'contact_id' => $contact_id,
                'type' => 1,
                'contact_name' => $input['billing']['firstname'],
                'email' => $input['billing']['email'],
                'phone' => $input['billing']['phone_no'],
                'job_position' => null,
                'street_1' => $input['billing']['address'],
                'street_2' => '',
                'notes' => '',
                'city' => $input['billing']['city'],
                'zipcode' => $input['billing']['zip'],
                'country_id' => $input['billing']['country_id'] ? $input['billing']['country_id'] : null,
                'title_id' => null,
                'state_id' => $input['billing']['state'] ? $input['billing']['state'] : null,
                'contact_image' => null,

            );
            $address = new ContactAddress();
            $address->fill($contact_addresses_arr)->save();

            $addresses['billing_id']  = $address->id;
            $addresses['shipping_id']  = $address->id;

            $cart_items = Session::get('cart_items') ? Session::get('cart_items') : [];
            $coupon_code = Session::get('coupon_code');
            $pricelist_id = Session::get('pricelist_id');

            if(count($cart_items) > 0)
            {

                $default_vat_percentage = \App\Models\SiteSettings::first()->defualt_vat;
                $ip_info = ip_info();
                $default_vat = \App\Models\SiteSettings::first()->defualt_vat;
                $vat_percentage = $default_vat;
                if(Auth::user()){
                    $vat_percentage = Auth::user()->contact->contact_countries->vat_in_percentage;
                    $vat_label = ( Auth::user()->contact->contact_countries->vat_label != null && Auth::user()->contact->contact_countries->vat_label != '' ) ? Auth::user()->contact->contact_countries->vat_label : 'VAT';
                    if(Auth::user()->contact->contact_countries->is_default_vat == 1)
                    {
                        $vat_percentage = $default_vat_percentage;
                    }
                }
                else
                {
                    if(isset($ip_info['country_code'])){
                        $vat_percentage = ContactCountry::where('country_code', $ip_info['country_code'])->first() ? ContactCountry::where('country_code', $ip_info['country_code'])->first()->vat_in_percentage : $default_vat;
                        $vat_label = ContactCountry::where('country_code', $ip_info['country_code'])->first()->vat_label ? ContactCountry::where('country_code', $ip_info['country_code'])->first()->vat_label : 'VAT';
                        $cc = ContactCountry::where('country_code', $ip_info['country_code'])->first();
                
                        if($cc){
                            // $vat_label  = translation(  $cc->id,32,app()->getLocale(),'vat_label',$vat_label);
                            if($cc->is_default_vat == 1)
                            {
                                $vat_percentage = $default_vat;
                                $vat_label = 'VAT';
                                // $vat_label = __($vat_label);
                            }
                        }
                    }
                }
                // Make New Quotation
                $currency_acceptance = $this->checkCurrencyAcceptibility(Session::get('currency_code'));
                $quotation = new Quotation;
                    $quotation->customer_id = $contact_id;
                    $quotation->status = 0;
                    $quotation->invoice_address = $addresses['billing_id'];
                    $quotation->delivery_address = $addresses['shipping_id'];
                    $quotation->pricelist_id = $pricelist_id == null ? 1 :$pricelist_id;
                    $quotation->payment_terms = 1;
                    $quotation->invoice_status = 0;
                    $quotation->status = 0;
                    $quotation->is_quotation_sent = 0;
                    $quotation->is_proforma_quotation_sent = 0;
                    $quotation->is_proforma_quotation_sent = 0;
                    $quotation->is_confirmed_without_kss = 0;
                    $quotation->vat_percentage = $vat_percentage;
                    $quotation->currency_symbol = $currency_acceptance ? Session::get('currency_symbol') == null ? \App\Models\Currency::where('is_active', 1)->where('is_default', 1)->first()->symbol : Session::get('currency_symbol') : "€";
                    $quotation->currency = $currency_acceptance ? Session::get('currency_code') == null ? \App\Models\Currency::where('is_active', 1)->where('is_default', 1)->first()->code : Session::get('currency_code') : "EUR";
                    $quotation->exchange_rate = $currency_acceptance ? Session::get('exchange_rate') == null ? \App\Models\Currency::where('is_active', 1)->where('is_default', 1)->first()->exchange_rate : Session::get('exchange_rate') : 1;
                    $quotation->vat_label = $vat_label;
                $quotation->save();

                // Make Quotation Other Info instance and save the sales person and sales team data in it for the quotation created
                $quotation_other_info = new QuotationOtherInfo;
                    $quotation_other_info->quotation_id = $quotation->id;
                    $quotation_other_info->salesperson_id = null;      // Website
                    $quotation_other_info->sales_team_id = 1;       // Website Sales Team to be created by seeder
                $quotation_other_info->save();
                // Iterate through all cart items and create quotation order lines coressponding to each cart item and quotation
                foreach($cart_items as $cart_item)
                {
                    $product = null;
                    $product_json = '';
                    try {
                        $product = Products::with('customer_taxes')->where('id', $cart_item->product_id)->first();
                    } catch (\Throwable $th) {
                        //throw $th;
                    }
                    $product_varaitions = ProductVariation::with('variation_details')->where('id', $cart_item->variation_id )->first();
                    $product->variation = $product_varaitions;
                    $product_json = (string)$product;

                    // quotation order line
                    $quotation_order_line = new QuotationOrderLine;
                        $quotation_order_line->quotation_id = $quotation->id;
                        $quotation_order_line->product_id = $cart_item->product_id;
                        $quotation_order_line->variation_id = $cart_item->variation_id;
                        $quotation_order_line->description = $product ? $product->description : null;
                        $quotation_order_line->qty = $cart_item->qty;
                        $quotation_order_line->unit_price = $cart_item->unit_price;
                        $quotation_order_line->delivered_qty = 0;
                        $quotation_order_line->invoiced_qty = 0;
                        $quotation_order_line->product_json = $product_json;
                    $quotation_order_line->save();


                    // Customer Taxes corresponding to the product in cart item
                    foreach($product->customer_taxes as $tax)
                    {
                        // Make new Quotation Order Line Tax
                        $order_line_tax = new QuotationOrderLineTax;
                            $order_line_tax->quotation_order_line_id = $quotation_order_line->id;
                            $order_line_tax->tax_id = $tax->tax_id;
                        $order_line_tax->save();
                    }
                }
            }
        }

        $payment = $this->generatePaymentDetails($quotation);
        if($payment['success']){
            // save the transaction id with the quotation
            $quotation->transaction_id = $payment['payment']->id;
            $quotation->save();
            $name = '';
            $email = '';
            if(Auth::user()){
                $name = Auth::user()->name;
                $email = Auth::user()->email;
            }else{
                $name = $input['billing']['firstname'];
                $email = $input['billing']['email'];
            }
            // Transformation of Order Placed Email Template
            $order_number = "S".str_pad($quotation->id, 5, '0', STR_PAD_LEFT);
            $quotation_pdf = $this->generate_quotation_pdf_save($quotation->id);
            $email_template = EmailTemplate::where('type','sales_order_placed')->first();
            $lang = app()->getLocale();
            $email_template = transformEmailTemplateModel($email_template,$lang);
            $content = $email_template['content'];
            $subject = $email_template['subject'];
            $search = array("{{name}}","{{order_number}}","{{app_name}}");
            $replace = array($name,$order_number,env('APP_NAME'));
            $content = str_replace($search,$replace,$content);
            dispatch(new \App\Jobs\SendOrderEmailJob($email,$subject,$content,$quotation_pdf));
            // Redirect to tge payment bank hosted url
            return redirect($payment['payment']->getCheckoutUrl());
        }else{
            return redirect()->back()->with(session()->flash('alert-warning',$payment['message']));
        }
    }
    public function paymentRedirect($quotation_id, Request $request)
    {
        try {
            $quotation_id = Hashids::decode($quotation_id)[0];
        } catch (\Throwable $th) {
            //throw $th;
        }

        $data['quotation'] = Quotation::where('id', $quotation_id)->first();

        if($data['quotation']){
            $payment = $this->getMolliePaymentDetail($data['quotation']->transaction_id);
            if($data['quotation']->invoice_status == 1 || (!$payment['payment']->isPaid() && $payment['payment']->status != "settled")){
                return redirect()->route('frontside.shop.cart')->with(session()->flash('alert-warning',__('Something went wrong with the payment. Try again later')));
            }
            $data['quotation']->invoice_status = 1;
            $data['quotation']->status = 1;
            $data['quotation']->save();

            $request->session()->forget('cart_items');
            $request->session()->forget('coupon_code');
            $request->session()->forget('pricelist_id');
            if(Auth::user()){
                if(Auth::user()->cart){
                    Auth::user()->cart->is_checkout = 1;
                    Auth::user()->cart->save();
                }
            }else{
                $request->session()->forget('cart_items');
                $request->session()->forget('coupon_code');
                $request->session()->forget('pricelist_id');
            }
            $data['invoice'] = $this->create_invoice($quotation_id);

            $payment_history = new InvoicePaymentHistory;
            $payment_history->invoice_id = $data['invoice']->id;
            $payment_history->transaction_id = $data['quotation']->transaction_id;
            $payment_history->method = "Online Payment";
            $payment_history->amount = str_replace(",","",$data['quotation']->total * $data['quotation']->exchange_rate);
            $payment_history->save();

            // Transformation of Payment Success Email Template
            $order_number = "S".str_pad($data['quotation']->id, 5, '0', STR_PAD_LEFT);
            $quotation = $data['quotation'];
            $name = $data['quotation']->customer->user->name;
            $email = $data['quotation']->customer->user->email;
            $quotation_pdf = $data['invoice']->invoice_pdf;
            $transaction_id = $data['quotation']->transaction_id;
            $email_template = EmailTemplate::where('type','payment_success')->first();
            $lang = app()->getLocale();
            $email_template = transformEmailTemplateModel($email_template,$lang);
            $content = $email_template['content'];
            $subject = $email_template['subject'];
            $search = array("{{name}}","{{order_number}}","{{transaction_id}}","{{app_name}}");
            $replace = array($name,$order_number,$transaction_id,env('APP_NAME'));
            $content = str_replace($search,$replace,$content);
            dispatch(new \App\Jobs\PaymentSuccessEmailJob($email,$subject,$content,$quotation_pdf));
            $vouchers_generated = generateVouchers($quotation_id);
            if(Auth::user()){
                if(Auth::user()->email_verified_at == null) {
                    // Session::flash('alert-warning', __('Your email is unverified! Kindly verify your email.'));
                }
            }
            return redirect()->route('frontside.order.success', Hashids::encode($quotation_id))->with(session()->flash('alert-success',__('Order confirmed and vouchers dispatched.')));
        }
        else
        {
            return redirect()->route('frontside.home.index');
        }
    }
    public function attachLicenses($quotation_id){
        // Get all the quotation order lines
        $all_license_generated = true;
        $quotation_order_lines =  QuotationOrderLine::where( 'quotation_id', $quotation_id)->get();
        $licenses = [];
        // Iterate through all quotation orders lines, check and assign the licenses accordingly
        foreach($quotation_order_lines as $quotation_order_line)
        {

            if($quotation_order_line->product != null){

                $product_name = $quotation_order_line->product->product_name.' '.@$quotation_order_line->variation->variation_name;
                $licenses[$product_name] = [];
                // Count for the Licenses added for the QuotationOrderLine
                $check_license_count = License::where('quotation_order_line_id',$quotation_order_line->id)->count();
                // Licenses Count for the item added
                $licenses_count = License::where('product_id', $quotation_order_line->product->id);
                if($quotation_order_line->variation != null){
                    $licenses_count = $licenses_count->where('variation_id',$quotation_order_line->variation->id);
                }
                $licenses_count = $licenses_count->where('status',1);
                $licenses_count = $licenses_count->where('is_used',0);
                $licenses_count = $licenses_count->count();

                // If available license count is is less the ordered quantity
                if( $licenses_count < ( $quotation_order_line->qty - $check_license_count ) )
                {
                    $all_license_generated = false;
                }
                else
                {
                    // dd($quotation_order_line->qty - $check_license_count);
                    for($i = 0 ; $i < ( $quotation_order_line->qty - $check_license_count ); $i++)
                    {
                        $license = License::where('product_id', $quotation_order_line->product->id);
                        if($quotation_order_line->variation != null){
                            $license = $license->where('variation_id',$quotation_order_line->variation->id);
                        }
                        $license = $license->where('status',1);
                        $license = $license->where('quotation_order_line_id',null);
                        $license = $license->where('is_used',0);
                        $license = $license->inRandomOrder();
                        $license = $license->first();

                        $license->quotation_order_line_id = $quotation_order_line->id;
                        $license->is_used = 1;
                        $license->save();
                    }
                    $licenses[$product_name][] = $quotation_order_line->licenses;

                }
            }
        }
        // Transformation of Order Placed Email Template
        $licenses_arr = [];
        if(count($licenses) > 0) {
            foreach($licenses as $product => $licences) {
               $unorderd_list =  '<p style="font-size: 18px; line-height: 25px;"><span style="color: rgb(85, 85, 85); font-family: Segoe, &quot;Segoe UI&quot;, sans-serif; font-size: 17px;"><u><b>'.$product.'</b></u></span></p><ul>';
                array_push($licenses_arr,$unorderd_list);
                if(isset($licences[0])){
                    foreach($licences[0] as $license) {
                        $licenses_list = '<li>'.$license->license_key.'</li>';
                        array_push($licenses_arr,$licenses_list);
                    }
                }
               $unorderd_list = '</ul>';
               array_push($licenses_arr,$unorderd_list);
            }
            $licenses_html = implode(' ', $licenses_arr);
        }
        else {
            $licenses_html = "<p>There's no license</p>";
        }
        $name = $quotation_order_lines[0]->quotation->customer->name;
        $email = $quotation_order_lines[0]->quotation->customer->email;
        $order_number = "S".str_pad($quotation_order_lines[0]->quotation->id, 5, '0', STR_PAD_LEFT);
        $email_template = EmailTemplate::where('type','order_licenses_email')->first();
        $lang = app()->getLocale();
        $email_template = transformEmailTemplateModel($email_template,$lang);
        $content = $email_template['content'];
        $subject = $email_template['subject'];
        $search = array("{{name}}","{{order_number}}","{{licenses_list}}","{{app_name}}");
        $replace = array($name,$order_number,$licenses_html,env('APP_NAME'));
        $content = str_replace($search,$replace,$content);
        dispatch(new \App\Jobs\SendLicenseEmailJob($email,$subject,$content));
        return $all_license_generated;
    }
    public function orderSuccess($quotation_id)
    {
        try {
            $quotation_id = Hashids::decode($quotation_id)[0];
        } catch (\Throwable $th) {
            //throw $th;
        }
        $data['quotation'] = Quotation::where('id', $quotation_id)->first();
        if($data['quotation']){
            return view('frontside.shop.thankyou', $data);
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

        $invoice_total = 0;
        // Make a new Invoice for the quotation
        $check_invoice = Invoice::where('quotation_id', $quotation_id)->where(function($query){
            $query->where('is_paid',0);
            $query->where('is_partially_paid',0);
        })->first();
        $new_invoice = null;
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
                    $invoice_order_line_total = $order_line->invoicetotal;
                    $invoice_total += $invoice_order_line_total ;
                }
            }
            $invoice_total = $invoice_total * $quotation->exchange_rate;
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
            $invoice_total = $invoice_total * $quotation->exchange_rate;
        }
        $invoice_total = currency_format($invoice_total,'','',1);


        if($invoice_total > 0){
            // Update the Invoice Total in the invoice table
            $new_invoice->invoice_total = $invoice_total;
            $new_invoice->amount_paid = $invoice_total;
            $new_invoice->save();
            return $new_invoice;
        }
        return $new_invoice;
        return 'false';
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
        $data['order'] = true;
        $html = view('admin.sales.pdf.quotation')->with($data)->render();
        $upload_path = public_path() . '/storage/quotations/' ;
        $fileName =  'S'.str_pad($quotation_id, 5, '0', STR_PAD_LEFT). '_order_detail.' . 'pdf' ;
        // $options = new \App\Classes\GrabzItPDFOptions();
        // $options->setMarginTop(10);
        // $options->setMarginBottom(10);
        // $options->setMarginRight(20);
        // $options->setMarginLeft(20);

        // // $grabzIt->URLToPDF("https://www.tesla.com", $options);
        // $grabzIt = new GrabzItClient('NjgzMDI3NTE5Nzk3NGQwYjgzOGZjYmEwODBmZmNkNmY=', 'LAQ/HD8/P2l3LT8/PwJcVD8/eT8/Pz8/PyM4P2p7Pzc=');
        // $grabzIt->HTMLToPDF($html, $options);
        // $grabzIt->SaveTo($upload_path.$fileName);
        // return $upload_path.$fileName;
        // return view('admin.sales.pdf.quotation', $data);
        $pdf = PDF::loadView('admin.sales.pdf.quotation', $data);

        $upload_path = public_path() . '/storage/quotations/' ;
        $fileName =  'S'.str_pad($quotation_id, 5, '0', STR_PAD_LEFT). '_order.' . 'pdf' ;

        if (File::exists($upload_path . $fileName)) {
            unlink($upload_path.$fileName);
        }
        if (!File::exists(public_path() . '/storage/quotations/')) {
            File::makeDirectory($upload_path, 0777, true);
        }

        $pdf->save($upload_path . $fileName);
        return $upload_path . $fileName;
    }
    public function paymentPay($transaction_id)
    {
        $quotation = Quotation::where('transaction_id', $transaction_id)->first();
        // dd($quotation->invoices);
        $can_be_invoiced = 1;
        foreach($quotation->invoices as $invoice){
            if($invoice->is_paid == 1 || $invoice->is_partially_paid == 1){
                $can_be_invoiced = 0;
                break;
            }
        }
        // $payment = null;
        if($quotation && $can_be_invoiced == 1)
        {
            $payment = $this->getMolliePaymentDetail($transaction_id);
            // dd($payment['payment']->getCheckoutUrl());
            if($payment['payment']->getCheckoutUrl() == null){
                return view('frontside.shop.nopayment');
            }
            return redirect($payment['payment']->getCheckoutUrl());
        }
        return redirect()->route('frontside.home.index')->with(session()->flash('alert-warning',__('Payment has been paid or initiated.')));
    }

}
