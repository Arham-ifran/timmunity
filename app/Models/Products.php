<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Auth;
use Illuminate\Database\Eloquent\SoftDeletes;

class Products extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'product_name',
        'product_category_id',
        'product_type_id',
        'can_be_purchase',
        'can_be_sale',
        'product_type',
        'prefix',
        'project_id',
        'created_by',
        'updated_by'
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->created_by = Auth::user()->id;
            $model->updated_by = Auth::user()->id;
        });
        static::updating(function ($model) {
            $model->updated_by = Auth::user()->id;
        });
    }

    public function voucherOrders()
    {
        return $this->hasMany('App\Models\VoucherOrder'::class,'id');
    }
    public function distributors()
    {
        return $this->hasMany('App\Models\DistributorProductDetail','id');
    }
    public function project()
    {
        return $this->belongsTo('App\Models\Project', 'project_id');
    }
    public function generalInformation()
    {
        return $this->hasOne('App\Models\ProductGeneralInformation', 'product_id');
    }
    public function variations()
    {
        return $this->hasMany('App\Models\ProductVariation', 'product_id');
    }
    public function attributes()
    {
        return $this->hasMany('App\Models\ProductAttachedAttribute', 'product_id');
    }
    public function attached_attributes(){
        return $this->belongsToMany(ProductAttribute::class, ProductAttachedAttribute::class,'product_id','attribute_id');
    }
    public function alternative_products()
    {
        return $this->hasMany('App\Models\ProductAlternativeProduct', 'product_id');
    }
    public function optional_products()
    {
        return $this->hasMany('App\Models\ProductOptionalProduct', 'product_id');
    }
    public function sales()
    {
        return $this->hasOne('App\Models\ProductSale', 'product_id');
    }
    public function customer_taxes()
    {
        return $this->hasMany('App\Models\ProductTax', 'product_id')->where('type',0);
    }
    public function vendor_taxes()
    {
        return $this->hasMany('App\Models\ProductTax', 'product_id')->where('type',1);
    }
    public function eccomerce_images()
    {
        return $this->hasMany('App\Models\ProductEccomerceImage', 'product_id');
    }
    // Schedule Activity and Activity Relations
    public function schedule_activities(){
        return $this->hasMany(ScheduleActivities::class,'product_id');
    }
    public function activity_attachments(){
        return $this->hasMany(ActivityAttachments::class,'product_id');
    }
    public function activity_log_notes(){
        return $this->hasMany(ActivityLogNotes::class,'product_id');
    }
    public function activity_messages(){
        return $this->hasMany(ActivityMessages::class,'product_id');
    }
    public function licenses()
    {
        return $this->hasMany('App\Models\License', 'product_id');
    }
    public function manufacturer(){

        return $this->hasOne('App\Models\Manufacturer','id','manufacturer_id');
    }
    public function getSecondaryProjectIdArrayAttribute()
    {
        return array_filter(explode(',',$this->secondary_project_ids));
    }
    public function getSecondaryProjectsArrayAttribute()
    {
        $project_ids = array_filter(explode(',',$this->secondary_project_ids));
        $projects = Project::whereIn('id', $project_ids)->pluck('name')->toArray();
        return $projects;
    }
    // this attribute will return the product price inclusive of all customer taxes applied but excluding of an VAT (which will be country wise)
    public function getPriceWithoutVatAttribute()
    {
        $data = [];
        $sales_price = $this->generalInformation->sales_price;
        $total_tax = 0;
        // print_r(count($this->variations) );
        if(count($this->variations) == 0)     // If the product is simple
        {
            $data['is_variable'] = 0;
            // compiling the Product Tax
            foreach($this->customer_taxes as $customer_tax)
            {

                if($customer_tax->tax != null){
                    switch($customer_tax->tax->computation)
                    {
                        case 0:
                            $total_tax += $customer_tax->tax->amount;
                            break;
                        case 1:
                            $total_tax += $sales_price * $customer_tax->tax->amount / 100;
                            break;
                    }
                }
            }
            $data['total_price_exclusive_vat'] = $sales_price + $total_tax;
            $data['total_price_exclusive_vat_tax'] = $sales_price;
        }
        else
        {
            foreach($this->customer_taxes as $customer_tax)
            {

                if($customer_tax->tax != null){
                    switch($customer_tax->tax->computation)
                    {
                        case 0:
                            $total_tax += $customer_tax->tax->amount;
                            break;
                        case 1:
                            $total_tax += $sales_price * $customer_tax->tax->amount / 100;
                            break;
                    }
                }
            }
            $data['is_variable'] = 1;
            $data['total_price_exclusive_vat'] = $sales_price + $total_tax;
            $data['end_price_without_tax'] = $sales_price + $total_tax;
            $data['total_price_exclusive_vat_tax'] = $sales_price;
            $data['end_price'] = $sales_price + $total_tax;

            foreach($this->variations as $variation)
            {
                if( $variation->variation_sales_price == null ){
                    if( ($data['total_price_exclusive_vat'] + $variation->extra_price) > $data['end_price']  ){
                        $data['end_price'] = $sales_price + $total_tax + $variation->extra_price;
                        $data['end_price_without_tax'] = $sales_price + $variation->extra_price;
                    }
                }else{
                    if( $variation->variation_sales_price > $data['end_price']  ){
                        $data['end_price'] = $variation->variation_sales_price;
                        $data['end_price_without_tax'] = $variation->variation_sales_price;
                    }
                }
            }
        }
        return $data;
    }
    // Reseller based price
    public function getResellerPriceWithoutVatAttribute()
    {
        $data = [];
        $sales_price = $this->generalInformation->sales_price;
        $total_tax = 0;
        // print_r(count($this->variations) );
        if(count($this->variations) == 0)     // If the product is simple
        {
            $data['is_variable'] = 0;
            // compiling the Product Tax
            foreach($this->customer_taxes as $customer_tax)
            {

                if($customer_tax->tax != null){
                    switch($customer_tax->tax->computation)
                    {
                        case 0:
                            $total_tax += $customer_tax->tax->amount;
                            break;
                        case 1:
                            $total_tax += $sales_price * $customer_tax->tax->amount / 100;
                            break;
                    }
                }
            }
            $data['total_price_exclusive_vat'] = $sales_price + $total_tax;
            $data['total_price_exclusive_vat_tax'] = $sales_price;
        }
        else
        {
            foreach($this->customer_taxes as $customer_tax)
            {
                if($customer_tax->tax != null){
                    switch($customer_tax->tax->computation)
                    {
                        case 0:
                            $total_tax += $customer_tax->tax->amount;
                            break;
                        case 1:
                            $total_tax += $sales_price * $customer_tax->tax->amount / 100;
                            break;
                    }
                }
            }
            $data['is_variable'] = 1;
            $data['total_price_exclusive_vat'] = $sales_price + $total_tax;
            $data['end_price_without_tax'] = $sales_price + $total_tax;
            $data['total_price_exclusive_vat_tax'] = $sales_price;
            $data['end_price'] = $sales_price + $total_tax;

            foreach($this->variations as $variation)
            {
                if( $variation->reseller_sales_price != null ){
                    // if( ($data['total_price_exclusive_vat'] + $variation->reseller_sales_price) ){
                    if( $variation->reseller_sales_price >  $data['end_price']){
                        $data['end_price'] = $variation->reseller_sales_price;
                        $data['end_price_without_tax'] = $variation->reseller_sales_price;
                    }
                }else{
                    if( $variation->variation_sales_price == null ){
                        if( ($data['total_price_exclusive_vat'] + $variation->extra_price) > $data['end_price']  ){
                            $data['end_price'] = $sales_price + $total_tax + $variation->extra_price;
                            $data['end_price_without_tax'] = $sales_price + $variation->extra_price;
                        }
                    }else{
                        if( $variation->variation_sales_price > $data['end_price']  ){
                            $data['end_price'] = $variation->variation_sales_price;
                            $data['end_price_without_tax'] = $variation->variation_sales_price;
                        }
                    }
                }
            }
        }
        return $data;
    }

    public function getAvailableLicenseCountAttribute(){
        $id = $this->id;
        return License::where('product_id', $id)->where('variation_id', null)->where('is_used', 0)->count();
    }

    public function getVouchersOrderedCountAttribute(){
        $product_id = $this->id;
        $voucher_orders_vouchers_count = Voucher::whereHas('voucherOrder',function($query) use($product_id){
            $query->where('product_id', $product_id);
        })->count();
        $vouchers_quotations = QuotationOrderLineVoucher::whereHas('order_line', function($query) use($product_id){
            $query->where('product_id', $product_id);
        })->count();
        return $voucher_orders_vouchers_count + $vouchers_quotations;
    }
    public function getUnUsedVouchersCountAttribute(){
        $product_id = $this->id;
        $voucher_orders_vouchers_count = Voucher::whereHas('voucherOrder',function($query) use($product_id){
            $query->where('product_id', $product_id);
        })->where('redeemed_at',null)->count();
        $vouchers_quotations = QuotationOrderLineVoucher::whereHas('order_line', function($query) use($product_id){
            $query->where('product_id', $product_id);
        })->where('redeemed_at',null)->count();
        return $voucher_orders_vouchers_count + $vouchers_quotations;
    }

    public function distributorExtraPrice($distributor_id)
    {
        $detail = DistributorProductDetail::where('distributor_id', $distributor_id)->where('product_id', $this->id)->first();
        return $detail->extra_price == null ? 0 : $detail->extra_price;
    }
    public function distributorProduct($distributor_id)
    {
        $detail = DistributorProductDetail::where('distributor_id', $distributor_id)->where('product_id', $this->id)->first();
        // $detail = DistributorProductDetail::where('distributor_id', $distributor_id)->get();
        return $detail;
    }

}
