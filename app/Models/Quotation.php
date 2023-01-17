<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\QuotationOrderLineTax;


class Quotation extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = ['customer_id','pricelist_id','expires_at','invoice_address','delivery_address','payment_due_day','payment_terms','invoice_status','status','terms_and_conditions'];


    public function customer(){
        return $this->belongsTo(Contact::class,'customer_id')->withTrashed();
    }
    public function pricelist(){
        return $this->belongsTo(ProductPriceList::class,'pricelist_id')->withTrashed();
    }
    public function order_lines(){
        return $this->hasMany(QuotationOrderLine::class,'quotation_id');
    }
    public function optional_products(){
        return $this->hasMany(QuotationOptionalProduct::class,'quotation_id');
    }
    public function other_info(){
        return $this->hasOne(QuotationOtherInfo::class,'quotation_id');
    }
    public function text_templates(){
        return $this->hasMany(QuotationTextTemplate::class,'quotation_id');
    }
    public function payment_term_detail(){
        return $this->belongsTo(PaymentTerm::class,'payment_terms')->withTrashed();;
    }
    public function invoice_address_detail(){
        return $this->belongsTo(ContactAddress::class,'invoice_address')->withTrashed();;
    }
    public function delivery_address_detail(){
        return $this->belongsTo(ContactAddress::class,'delivery_address')->withTrashed();;
    }
    public function tags(){
        return $this->belongsToMany(ContactTag::class, QuotationOtherInfoTag::class,'quotation_id','tag_id')->withTrashed();;
    }
    public function invoices(){
        return $this->hasMany(Invoice::class,'quotation_id');
    }
    public function getTotalAttribute()
    {
        $orderlines = $this->order_lines;
        $total=0;
        foreach($orderlines as $o){
            $subtotal = $o->qty * $o->unit_price;
            $total += $subtotal;;
            $taxes = QuotationOrderLineTax::with('tax')->where('quotation_order_line_id',$o->id)->get();

            foreach($taxes as $o_tax)
            {
                if($o_tax->tax != null){
                    switch($o_tax->tax->computation)
                    {
                        case 0:
                            $total += $o_tax->tax->amount;
                            break;
                        case 1:
                            $total += $subtotal * $o_tax->tax->amount / 100;
                            break;
                    }
                }
            }
            $vat_amount = $subtotal * $this->vat_percentage / 100;

            $total += $vat_amount;
        }
        return currency_format($total,'','',1);
    }
    public function getTotalCurrencyAttribute()
    {
        $orderlines = $this->order_lines;
        $total=0;
        foreach($orderlines as $o){
            $unit_price = currency_format($o->unit_price*$this->exchange_rate,'','',1);
            $subtotal = $o->qty * $unit_price;
            $total += $subtotal;;
            $taxes = QuotationOrderLineTax::with('tax')->where('quotation_order_line_id',$o->id)->get();

            foreach($taxes as $o_tax)
            {
                if($o_tax->tax != null){
                    switch($o_tax->tax->computation)
                    {
                        case 0:
                            $total += $o_tax->tax->amount;
                            break;
                        case 1:
                            $total += $subtotal * $o_tax->tax->amount / 100;
                            break;
                    }
                }
            }
            $vat_amount = $subtotal * $this->vat_percentage / 100;

            $total += $vat_amount;
        }
        return currency_format($total,'','',1);
    }
    public function getTotalTaxAttribute()
    {
        $orderlines = QuotationOrderLine::all();
        $total_tax = 0;
        foreach($orderlines as $o){
            $subtotal = $o->qty * $o->unit_price;
            $taxes = QuotationOrderLineTax::with('tax')->where('quotation_order_line_id',$o->id)->get();

            foreach($taxes as $o_tax)
            {
                if($o_tax->tax != null){
                    switch($o_tax->tax->computation)
                    {
                        case 0:
                            $total_tax += $o_tax->tax->amount;
                            break;
                        case 1:
                            $total_tax += $subtotal * $o_tax->tax->amount / 100;
                            break;
                    }
                }
            }

        }
        return currency_format($total_tax,'','',1);
    }
    public function getTotalTaxCurrencyAttribute()
    {
        $currency = $this->currency;
        $orderlines = QuotationOrderLine::whereHas('quotation',function($query) use($currency){
            $query->where('currency',$currency);
        })->get();
        $total_tax = 0;
        foreach($orderlines as $o){
            $subtotal = $o->qty * $o->unit_price;
            $taxes = QuotationOrderLineTax::with('tax')->where('quotation_order_line_id',$o->id)->get();

            foreach($taxes as $o_tax)
            {
                if($o_tax->tax != null){
                    switch($o_tax->tax->computation)
                    {
                        case 0:
                            $total_tax += $o_tax->tax->amount;
                            break;
                        case 1:
                            $total_tax += $subtotal * $o_tax->tax->amount / 100;
                            break;
                    }
                }
            }
        }
        return currency_format($total_tax,'','',1);
    }
    public function getAllQuotationsTotalAttribute()
    {
        $orderlines = QuotationOrderLine::all();
        $total=0;
        foreach($orderlines as $o){
            $subtotal = $o->qty * $o->unit_price;
            $total += $subtotal;;
            $taxes = QuotationOrderLineTax::with('tax')->where('quotation_order_line_id',$o->id)->get();

            foreach($taxes as $o_tax)
            {
                if($o_tax->tax != null){
                    switch($o_tax->tax->computation)
                    {
                        case 0:
                            $total += $o_tax->tax->amount;
                            break;
                        case 1:
                            $total += $subtotal * $o_tax->tax->amount / 100;
                            break;
                    }
                }
            }
        }
        return currency_format($total,'','',1);
    }
    public function getAllQuotationsTotalCurrencyAttribute()
    {
        $currency = $this->currency;
        $orderlines = QuotationOrderLine::whereHas('quotation',function($query) use($currency){
            $query->where('currency',$currency);
        })->get();
        $total=0;
        foreach($orderlines as $o){
            $subtotal = $o->qty * $o->unit_price;
            $total += $subtotal;
            $taxes = QuotationOrderLineTax::with('tax')->where('quotation_order_line_id',$o->id)->get();

            foreach($taxes as $o_tax)
            {
                if($o_tax->tax != null){
                    switch($o_tax->tax->computation)
                    {
                        case 0:
                            $total += $o_tax->tax->amount;
                            break;
                        case 1:
                            $total += $subtotal * $o_tax->tax->amount / 100;
                            break;
                    }
                }
            }
        }
        return currency_format($total,'','',1);
    }
    public function getAllQuotationsUntaxedTotalAttribute()
    {
        $orderlines = QuotationOrderLine::all();
        $total=0;
        foreach($orderlines as $o){
            $subtotal = $o->qty * $o->unit_price;
            $total += $subtotal;;
            $taxes = QuotationOrderLineTax::with('tax')->where('quotation_order_line_id',$o->id)->get();
        }
        return currency_format($total,'','',1);
    }
    public function getAllQuotationsUntaxedTotalCurrencyAttribute()
    {
        $currency = $this->currency;
        $orderlines = QuotationOrderLine::whereHas('quotation',function($query) use($currency){
            $query->where('currency',$currency);
        })->get();
        $total=0;
        foreach($orderlines as $o){
            $subtotal = $o->qty * $o->unit_price;
            $total += $subtotal;;
            $taxes = QuotationOrderLineTax::with('tax')->where('quotation_order_line_id',$o->id)->get();
        }
        return currency_format($total,'','',1);
    }
    public function getInvoicedAmountAttribute(){
        $invoices = $this->invoices;
        $invoiced_total = 0;
        foreach ($invoices as $key => $invoice) {
            if($invoice->status == 1 && $invoice->is_paid == 1){
                $invoiced_total += $invoice->amount_paid;
            }
        }
        return $invoiced_total;
    }
    public function getIsRefundedAttribute(){
        $invoices = $this->invoices;
        $refunded = false;
        foreach ($invoices as $invoice) {
            if($invoice->refunded_at != null){
                $refunded = true;
                break;
            }
        }
        return $refunded;
    }

    public function getAllLicencesAttachedAttribute()
    {
        $order_lines = $this->order_lines;
        foreach($order_lines as $order_line){
            if($order_line->qty > count($order_line->licenses)){
                return false;
            }
        }
        return true;
    }
    public function getAllVouchersGeneratedAttribute()
    {
        $order_lines = $this->order_lines;
        foreach($order_lines as $order_line){
            if($order_line->qty > count($order_line->vouchers)){
                return false;
            }
        }
        return true;
    }

     // Schedule Activity and Activity Relations
    public function schedule_activities(){
        return $this->hasMany(ScheduleActivities::class,'quotation_id');
    }
    public function activity_attachments(){
        return $this->hasMany(ActivityAttachments::class,'quotation_id');
    }
    public function activity_log_notes(){
        return $this->hasMany(ActivityLogNotes::class,'quotation_id');
    }
    public function activity_messages(){
        return $this->hasMany(ActivityMessages::class,'quotation_id');
    }
}
