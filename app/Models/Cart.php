<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Session;

class Cart extends Model
{
    use HasFactory;
    protected $fillable = ['customer_id','is_checkout'];

    public function cart_items(){
        return $this->hasMany(CartItem::class,'cart_id');
    }
    public function user(){
        return $this->belongsTo(User::class,'customer_id');
    }
    public function getAbandonedAtAttribute(){
        $cart_items = $this->cart_items;
        $count = count($cart_items);
        return \Carbon\Carbon::parse($cart_items[$count-1]->created_at)->format('d-M-Y H:m A');
    }
    public function getTotalAttribute(){
        $cart_items = $this->cart_items;
        $data = [];
        $data['subtotal'] = 0;
        $data['grandtotal'] = 0;
        $data['taxtotal'] = 0;
        $default_vat_percentage = $default_vat_percentage = \App\Models\SiteSettings::first()->defualt_vat;
        // dd($default_vat_percentage);

        foreach( $cart_items as $cart_item )
        {
            $unit_price = currency_format($cart_item->unit_price * (Session::get('exchange_rate') ? Session::get('exchange_rate') : 1),'','',1);
            $subtotal = $cart_item->qty * $unit_price;
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
            $data['subtotal'] += $subtotal;
            $vat_percentage =  \App\Models\SiteSettings::first()->defualt_vat;
            if(auth()->user()){
                $vat_percentage = auth()->user()->contact->contact_countries->vat_in_percentage;
                if(auth()->user()->contact->contact_countries->is_default_vat == 1)
                {
                    $vat_percentage = $default_vat_percentage;
                }
            }
            $data['taxtotal'] += $subtotal * $vat_percentage  / 100;
            $data['grandtotal'] += $subtotal * $vat_percentage / 100;
        }
        // $data['cart'] = (object)array();
        // $data['cart']->total = $total;
        // $data['cart']->id = 0;
        return $data;
    }

}
