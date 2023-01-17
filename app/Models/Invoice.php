<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use PDF;
use File;
use App\Classes\GrabzItClient;

class Invoice extends Model
{
    use HasFactory;

    public function invoice_order_lines(){
        return $this->hasMany(InvoiceOrderLine::class,'invoice_id');
    }
    public function invoice_payment_history(){
        return $this->hasMany(InvoicePaymentHistory::class,'invoice_id');
    }
    public function quotation(){
        return $this->belongsTo(Quotation::class,'quotation_id')->withTrashed();
    }
    public function clean_quotation(){
        return $this->belongsTo(Quotation::class,'quotation_id');
    }
    public function getTotalAttribute()
    {
        $invoice_order_lines = $this->invoice_order_lines;
        $total=0;
        foreach($invoice_order_lines as $invoice_order_line){
            $quotation_order_line = QuotationOrderLine::where('id', $invoice_order_line->quotation_order_line_id)->first();

            if($quotation_order_line){
                $subtotal = $invoice_order_line->invoiced_qty * $quotation_order_line->unit_price;
                $total += $subtotal;;
                $taxes = QuotationOrderLineTax::with('tax')->where('quotation_order_line_id',$quotation_order_line->id)->get();

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
                $total += $subtotal * $this->quotation->vat_percentage / 100;
            }
        }
        return $total;
    }
    public function getTotalTaxAttribute()
    {
        $invoice_order_lines = $this->invoice_order_lines;
        $total_tax = 0;
        foreach($invoice_order_lines as $invoice_order_line){
            $quotation_order_line = QuotationOrderLine::where('id', $invoice_order_line->quotation_order_line_id)->first();

            if($quotation_order_line){
                $subtotal = $invoice_order_line->invoiced_qty * $quotation_order_line->unit_price;
                $taxes = QuotationOrderLineTax::with('tax')->where('quotation_order_line_id',$quotation_order_line->id)->get();

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
                $total_tax += $subtotal * $this->quotation->vat_percentage / 100;
            }
        }
        return $total_tax;
    }
    public function getTotalInvoicedAmountAttribute()
    {
        $invoice_order_lines = InvoiceOrderLine::all();
        $total_tax = 0;
        foreach($invoice_order_lines as $invoice_order_line){
            $quotation_order_line = QuotationOrderLine::where('id', $invoice_order_line->quotation_order_line_id)->first();

            if($quotation_order_line){
                $subtotal = $invoice_order_line->invoiced_qty * $quotation_order_line->unit_price;
                $taxes = QuotationOrderLineTax::with('tax')->where('quotation_order_line_id',$quotation_order_line->id)->get();

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
                $total_tax += $subtotal * $this->quotation->vat_percentage / 100;
            }
        }
        return $total_tax;
    }
    public function getTotalInvoicedAmountCurrencyAttribute()
    {
        $currency = $this->quotation->currency;
        $exchange_rate = $this->quotation->exchange_rate;
        $invoice_order_lines = InvoiceOrderLine::whereHas('invoice',function($q){
            $q->where('refunded_at',null);
        })->whereHas('quotation_order_line.quotation', function($query) use($currency){
            $query->where('currency', $currency);
        })->get();
        $total = 0;
        foreach($invoice_order_lines as $invoice_order_line){
            $quotation_order_line = QuotationOrderLine::where('id', $invoice_order_line->quotation_order_line_id)->first();

            if($quotation_order_line){
                $subtotal = $invoice_order_line->invoiced_qty * $quotation_order_line->unit_price * $exchange_rate;
                $taxes = QuotationOrderLineTax::with('tax')->where('quotation_order_line_id',$quotation_order_line->id)->get();
                $total += $subtotal;
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
                $total += $subtotal * $this->quotation->vat_percentage / 100;
            }
        }
        return $total;
    }
    public function getTotalRefundedAmountCurrencyAttribute()
    {
        $currency = $this->quotation->currency;
        $exchange_rate = $this->quotation->exchange_rate;
        $invoice_order_lines = InvoiceOrderLine::whereHas('invoice',function($q){
            $q->where('refunded_at','!=',null);
        })->whereHas('quotation_order_line.quotation', function($query) use($currency){
            $query->where('currency', $currency);
        })->get();
        $total = 0;
        foreach($invoice_order_lines as $invoice_order_line){
            $quotation_order_line = QuotationOrderLine::where('id', $invoice_order_line->quotation_order_line_id)->first();

            if($quotation_order_line){
                $subtotal = $invoice_order_line->invoiced_qty * $quotation_order_line->unit_price * $exchange_rate;
                $taxes = QuotationOrderLineTax::with('tax')->where('quotation_order_line_id',$quotation_order_line->id)->get();
                $total += $subtotal;
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
                $total += $subtotal * $this->quotation->vat_percentage / 100;
            }
        }
        return $total;
    }
    public function getInvoicePdfAttribute(){
        $invoice_id = $this->id;
        $data['model'] = Invoice::with(
            'quotation',
            'quotation.customer',
            'quotation.customer.contact_addresses',
            'quotation.customer.contact_addresses.contact_countries',
            'invoice_order_lines.quotation_order_line',
            'invoice_order_lines.quotation_order_line.product',
            'invoice_order_lines.quotation_order_line.variation',
            'invoice_order_lines.quotation_order_line.quotation_taxes',
            'invoice_order_lines.quotation_order_line.quotation_taxes.tax',
            'quotation.other_info',
            'quotation.other_info.sales_person',
            'quotation.other_info.sales_team',
        )->where('id', $invoice_id)->first();
        $html = view('admin.sales.pdf.invoice')->with($data)->render();
        $upload_path = public_path() . '/storage/invoice/' ;

        $fileName = 'TIM-'.\Carbon\Carbon::parse($data['model']->created_at)->format('Y').'-'.str_pad($invoice_id, 3, '0', STR_PAD_LEFT). '.' . 'pdf' ;
     
        $customPaper = 'A3';
        $pdf = PDF::loadView('admin.sales.pdf.invoice', $data);

        if (File::exists($upload_path . $fileName)) {
            unlink($upload_path.$fileName);
        }
        if (!File::exists(public_path() . '/storage/invoice/')) {
            File::makeDirectory($upload_path, 0777, true);
        }

        $pdf->save($upload_path . $fileName);
        return public_path('/storage/invoice/'.$fileName);
    }
    public function getInvoicePdfLinkAttribute(){
        $invoice_id = $this->id;
        $data['model'] = Invoice::with(
            'quotation',
            'quotation.customer',
            'quotation.customer.contact_addresses',
            'quotation.customer.contact_addresses.contact_countries',
            'invoice_order_lines.quotation_order_line',
            'invoice_order_lines.quotation_order_line.product',
            'invoice_order_lines.quotation_order_line.variation',
            'invoice_order_lines.quotation_order_line.quotation_taxes',
            'invoice_order_lines.quotation_order_line.quotation_taxes.tax',
            'quotation.other_info',
            'quotation.other_info.sales_person',
            'quotation.other_info.sales_team',
        )->where('id', $invoice_id)->first();
        $html = view('admin.sales.pdf.invoice')->with($data)->render();
        $upload_path = public_path() . '/storage/invoice/' ;

        $fileName = 'TIM-'.\Carbon\Carbon::parse($data['model']->created_at)->format('Y').'-'.str_pad($invoice_id, 3, '0', STR_PAD_LEFT). '.' . 'pdf' ;
        $customPaper = 'A3';
        $pdf = PDF::loadView('admin.sales.pdf.invoice', $data);

        if (File::exists($upload_path . $fileName)) {
            unlink($upload_path.$fileName);
        }
        if (!File::exists(public_path() . '/storage/invoice/')) {
            File::makeDirectory($upload_path, 0777, true);
        }

        $pdf->save($upload_path . $fileName);
        return asset('/storage/invoice/'.$fileName);
    }
}
