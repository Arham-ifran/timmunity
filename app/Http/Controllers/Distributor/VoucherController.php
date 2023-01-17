<?php

namespace App\Http\Controllers\Distributor;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use App\Models\VoucherOrder;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Auth;
use Hashids;


class VoucherController extends Controller
{
    public function voucherOrders(Request $request)
    {
        if ($request->ajax()) {

            $data = VoucherOrder::with('vouchers',
                                        'voucher_taxes',
                                        'product',
                                        'variation',
                                        'product.generalInformation',
                                        'product.customer_taxes',
                                        'product.customer_taxes.tax',
                                        'contact_country'
                                    )->where('distributor_id', Auth::guard('distributor')->user()->id)->orderBy('created_at','desc');

            $datatable = Datatables::of($data);
            $datatable->addColumn('ids', function ($row) {
                return '';
            });
            $datatable->addColumn('order_id', function ($row) {
                return str_replace(' ','',@$row->distributor->name).'-'.str_pad(@$row->id,3,'0',STR_PAD_LEFT).'-'.date('dmY',strtotime(@$row->created_at));
            });
            $datatable->addColumn('action', function ($row)  use($request) {
                $html = '' ;
                $html = '<a title='.__('Vouchers').' class="" href="'.route("distributor.voucher.order.vouchers", Hashids::encode($row->id)).'">';
                    $html .= '<button class="btn btn-secondary btn-sm">';
                        $html .= '<i class="fa fa-gift" aria-hidden="true"></i>';
                    $html .= '</button>';
                $html .= '</a>&nbsp;';
                $html .= '<a title='.__('Payments').' href="'.route("distributor.voucher.payment", Hashids::encode($row->id)) .'" class="">';
                    $html .= '<button class="btn btn-info btn-sm">';
                        $html .= '<i class="fa fa-credit-card" aria-hidden="true"></i>';
                    $html .= '</button>';
                $html .= '</a>&nbsp;';
                $html .= '<a title='.__('Download').' href="'.route('distributor.voucher.order.vouchers.export', Hashids::encode($row->id)).'" class="">';
                    $html .= '<button class="btn btn-success btn-sm">';
                        $html .= '<i class="fa fa-download" aria-hidden="true"></i>';
                    $html .= '</button>';
                $html .= '</a>&nbsp;';
                return $html;
            });
            $datatable->addColumn('active_status', function ($row) {
                if($row->is_active == 0 || $row->is_active == '0'){
                    return '<span class="badge  bg-red">'.__('Inactive').'</span>';
                }
                if($row->status == 1 ||$row->is_active == '1'){
                    return $html ='<span class="badge  bg-green">'.__('Active').'</span>';
                }

            });
            $datatable->addColumn('statuss', function ($row) {
                if($row->status == 0)
                    return '<span class="badge  bg-yellow">'.__('Pending').'</span>';
                if($row->status == 1)
                    return $html ='<span class="badge  bg-green">'.__('Approved').'</span>';
                if($row->status == 2)
                    return $html ='<span class="badge badge-danger bg-red">'.__('Rejected').'</span>';

            });
            $datatable->addColumn('product_name', function ($row) {
                $html = $row->product->product_name.' ' ;
                $html .= $row->product->project == null ? @$row->variation->variation_name : '' ;

                if($row->product->secondary_project_ids != ''){
                    $projects = $row->product->secondary_projects_array;
                    $html .= '<br><strong>Secondary Platforms</strong><br>'.implode(',',$projects);
                }
                return $html;
            });
            $datatable->addColumn('date', function ($row) {
                return \Carbon\Carbon::parse($row->created_at)->format('d/M/Y');
            });
            $datatable->addColumn('discount', function ($row) {
                return @$row->discount_percentage;
            });
            $datatable->editColumn('quantity', function ($row) {
                return @$row->quantity;
            });
            $datatable->editColumn('used_quantity', function ($row) {
                return @$row->used_quantity;
            });
            $datatable->editColumn('remaining_quantity', function ($row) {
                return @$row->remaining_quantity;
            });
            $datatable->addColumn('unit_price', function ($row) {
                return currency_format($row->unit_price*$row->exchange_rate,$row->currency_symbol,$row->currency);
            });
            $datatable->addColumn('taxes', function ($row) {
                $html = '';
                $count = count($row->voucher_taxes);
                foreach($row->voucher_taxes as $ind => $voucher_tax){
                    $html .= $voucher_tax->tax->amount;
                    $html .= $voucher_tax->tax->type==1 ? ' %' : '';
                    if($ind < $count-1){
                        $html.=', ';
                    }
                }
                if($count > 0){
                    $html .= ', '.$row->vat_percentage.'% VAT';

                }else{
                    $html .= $row->vat_percentage.'% VAT';

                }
                return $html;
            });
            $datatable->addColumn('total_payable_amount', function ($row) {
                return currency_format($row->total_payable,$row->currency_symbol,$row->currency);
            });
            $datatable->addColumn('paid_amount', function ($row) {
                return currency_format(($row->total_payable-$row->remaining_total),$row->currency_symbol,$row->currency);
            });
            $datatable->addColumn('pending_payment', function ($row) {
                return currency_format($row->remaining_total,$row->currency_symbol,$row->currency);
            });
            $datatable->editColumn('street_address', function ($row) {
                return $row->street_address;
            });
            $datatable->editColumn('city', function ($row) {
                return $row->city;
            });
            $datatable->addColumn('country', function ($row) {
                return @$row->contact_country->name;
            });
            $datatable = $datatable->rawColumns(['product_name','reseller','active_status','statuss','action']);
            return $datatable->make(true);
        }

        return view('distributor.voucher.orders');
    }
    public function vouchers($id, Request $request)
    {

        $voucher_order_id = Hashids::decode($id)[0];

        if ($request->ajax()) {
            $data = Voucher::with('customer')->where('order_id', $voucher_order_id);
            $datatable = Datatables::of($data);
            $datatable->editColumn('code', function ($row) {
                return $row->code;
            });
            $datatable->addColumn('statuss', function ($row) {
                switch ($row->status) {
                    case 0:
                        return '<span class="badge  bg-green">'.__('Redeemed').'</span>';
                        break;
                    case 1:
                        return '<span class="badge  badge-light">'.__('Active').'</span>';
                        break;
                    case 2:
                        return '<span class="badge badge-danger">'.__('Disabled').'</span>';
                        break;
                    case 3:
                        return '<span class="badge bg-yellow">'.__('Pending').'</span>';
                        break;
                };
            });
            $datatable->addColumn('customer', function ($row) {
                return $row->customer != null ? @$row->customer->email : $row->email;
            });
            $datatable->addColumn('redeemed_time', function ($row) {
                return $row->redeemed_at ? \Carbon\Carbon::parse($row->redeemed_at)->format('d/M/Y') : '';
            });
            $datatable = $datatable->rawColumns(['input','statuss','action']);
            return $datatable->make(true);
        }
        $data = [];
        $data['ajax_url'] = route("distributor.voucher.order.vouchers", Hashids::encode($voucher_order_id));
        $data['code'] = isset($request->code) ? $request->code : '';
        return view('distributor.voucher.vouchers',$data);
    }
}
