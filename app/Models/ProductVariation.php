<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductVariation extends Model
{
    use HasFactory;
    use SoftDeletes;

    public function variation_details()
    {
        return $this->hasMany('App\Models\ProductVariationDetail', 'product_variation_id');
    }
    public function product()
    {
        return $this->belongsTo('App\Models\Products', 'product_id');
    }
    public function voucherOrder()
    {
        return $this->hasMany('App\Models\VoucherOrder', 'variation_id');
    }

    public function licenses()
    {
        return $this->hasMany('App\Models\License', 'variation_id');
    }
    public function getVariationNameAttribute()
    {
        $variation_details = json_decode($this->variation_detail_json, TRUE);
        $count = count($variation_details);
        $name = "";
        $name .= "( ";
        $index = 1;
        foreach($variation_details as $attribute_id => $attribute_value_id)
        {
            $attrbiute_value = @ProductAttributeValue::where('id', $attribute_value_id)->first()->attribute_value;
            $attrbiute_name = @ProductAttribute::where('id', $attribute_id)->first()->attribute_name;
            if($index < $count)
            {
                $name .= $attrbiute_value.', ';
            }
            else
            {
                $name .= $attrbiute_value.' )';
            }
            $index++;
        }
        return $name;
    }
    public function getVariationNameFullAttribute()
    {
        $variation_details = json_decode($this->variation_detail_json, TRUE);
        $count = count($variation_details);
        $name = "";
        // $name .= "( ";
        $index = 1;
        foreach($variation_details as $attribute_id => $attribute_value_id)
        {
            $attrbiute_value = @ProductAttributeValue::where('id', $attribute_value_id)->first()->attribute_value;
            $attrbiute_name = @ProductAttribute::where('id', $attribute_id)->first()->attribute_name;
            if($index < $count)
            {
                $name .= $attrbiute_value.' '.$attrbiute_name.', ';
            }
            else
            {
                // $name .= $attrbiute_value.' '.$attrbiute_name.' )';
                $name .= $attrbiute_value.' '.$attrbiute_name;
            }
            $index++;
        }
        return $name;
    }

    public function getExtraPriceAttribute()
    {
        $variation_details = $this->variation_details;
        $extra_price = 0;
        foreach($variation_details as $ind => $variation_detail)
        {
            foreach($variation_detail->attached_attribute->attributeValue as $av)
            {
               if($av->value_id == $variation_detail->attribute_value_id){
                    $extra_price += $av->extra_price;
                }
            }
        }
        return $extra_price;
    }

    public function getAvailableLicenseCountAttribute(){
        $id = $this->id;
        return License::where('variation_id', $id)->where('is_used', 0)->count();
    }
    public function getVouchersOrderedCountAttribute(){
        $variation_id = $this->id;
        $voucher_orders_vouchers_count = Voucher::whereHas('voucherOrder',function($query) use($variation_id){
            $query->where('variation_id', $variation_id);
        })->count();
        $vouchers_quotations = QuotationOrderLineVoucher::whereHas('order_line', function($query) use($variation_id){
            $query->where('variation_id', $variation_id);
        })->count();
        return $voucher_orders_vouchers_count + $vouchers_quotations;
    }
    public function getUnUsedVouchersCountAttribute(){
        $variation_id = $this->id;
        $voucher_orders_vouchers_count = Voucher::whereHas('voucherOrder',function($query) use($variation_id){
            $query->where('variation_id', $variation_id);
        })->where('redeemed_at',null)->count();
        $vouchers_quotations = QuotationOrderLineVoucher::whereHas('order_line', function($query) use($variation_id){
            $query->where('variation_id', $variation_id);
        })->where('redeemed_at',null)->count();
        return $voucher_orders_vouchers_count + $vouchers_quotations;
    }
}
