<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use PDF;
// use mPDF;
use File;

class VoucherPayment extends Model
{
    use HasFactory;

    public function reseller(){
        return $this->belongsTo(User::class,'reseller_id');
    }
    public function voucher_payment_references(){
        return $this->hasMany(VoucherPaymentReference::class,'voucher_payment_id');
    }
    public function details(){
        return $this->hasMany(VoucherPaymentOrderDetail::class,'voucher_payment_id');
    }
    public function getInvoicePdfAttribute(){
        $payment_id = $this->id;
        $data['model'] = VoucherPayment::where('id', $payment_id)->first();
        $customPaper = 'A3';
        $pdf = PDF::loadView('frontside.reseller.pdf.invoice', $data,['mode' => 'utf-8', 'format' => 'A4-L']);

        $upload_path = public_path() . '/storage/voucherPayments/' ;
        $fileName =  'VoucherPaymentInvoice'.str_pad($payment_id, 5, '0', STR_PAD_LEFT). '.' . 'pdf' ;

        if (File::exists($upload_path . $fileName)) {
            unlink($upload_path.$fileName);
        }
        if (!File::exists(public_path() . '/storage/voucherPayments/')) {
            File::makeDirectory($upload_path, 0777, true);
        }

        $pdf->save($upload_path . $fileName);
        return public_path('/storage/voucherPayments/'.$fileName);
        return $upload_path . $fileName;
    }
    public function getInvoicePdfAssetAttribute(){
        $payment_id = $this->id;
        $data['model'] = VoucherPayment::where('id', $payment_id)->first();
        $customPaper = 'A3';
        $customPaper = array(0,0,567.00,283.80);
        // $pdf = PDF:: setPaper($customPaper, 'portrait')->loadView('frontside.reseller.pdf.invoice', $data);
        $pdf = PDF::loadView('frontside.reseller.pdf.invoice', $data,['mode' => 'utf-8', 'format' => 'A4-L']);
        // $pdf = new PDF();
        // $pdf->loadView('frontside.reseller.pdf.invoice', $data);
        $upload_path = public_path() . '/storage/voucherPayments/' ;
        $fileName =  'VoucherPaymentInvoice'.str_pad($payment_id, 5, '0', STR_PAD_LEFT). '.' . 'pdf' ;

        if (File::exists($upload_path . $fileName)) {
            unlink($upload_path.$fileName);
        }
        if (!File::exists(public_path() . '/storage/voucherPayments/')) {
            File::makeDirectory($upload_path, 0777, true);
        }

        // dd($pdf->Output($upload_path . $fileName));
        $pdf->save($upload_path . $fileName);
        return asset('/storage/voucherPayments/'.$fileName);
        return $upload_path . $fileName;
    }
    public function getTotalPayableAttribute()
    {
        $total = 0;
        $details = $this->details;
        foreach($details as $detail)
        {
            $voucher_order = $detail->voucher_order;
            $quantity = count(explode(',', $detail->voucher_ids));
            $vat_percentage = $voucher_order->vat_percentage;
            $product_unit_price = $voucher_order->unit_price;
            $discount = $voucher_order->discount_percentage == null ? 0 : $voucher_order->discount_percentage;
            $sub_total_amount = $product_unit_price * $quantity;
            $sub_total_amount = $sub_total_amount - ($sub_total_amount * $discount / 100);
            $total += $sub_total_amount ;
            foreach($voucher_order->voucher_taxes as $ind => $voucher_tax){
                if($voucher_tax->tax->type == 1)
                {
                    $total += $sub_total_amount * $voucher_tax->tax->amount / 100;
                }
                else
                {
                    $total += $voucher_tax->tax->amount ;
                }
            }
            $total += $sub_total_amount * $vat_percentage / 100;
        }
        return $total;
    }
    public function getTaxAmountAttribute()
    {
        $total = 0 ;
        $details = $this->details;
        foreach($details as $detail)
        {
            $quantity = count(explode(',', $detail->voucher_ids));
            $vat_percentage = $detail->voucher_order->vat_percentage;
            $product_unit_price = $detail->voucher_order->unit_price;
            $discount = $detail->voucher_order->discount_percentage == null ? 0 : $detail->voucher_order->discount_percentage;
            $sub_total_amount = $product_unit_price * $quantity;
            $sub_total_amount = $sub_total_amount - ($sub_total_amount * $discount / 100);
            foreach($detail->voucher_order->voucher_taxes as $ind => $voucher_tax){
                if($voucher_tax->tax->type == 1)
                {
                    $total += $sub_total_amount * $voucher_tax->tax->amount / 100;
                }
                else
                {
                    $total += $voucher_tax->tax->amount ;
                }
            }

            $total += $sub_total_amount * $vat_percentage / 100;
        }
        return $total;
    }
}
