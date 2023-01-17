<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\OldKssSubscription;
use App\Models\OldKssCompanyLicense;
use Yajra\DataTables\DataTables;
use Illuminate\Http\Request;
use Carbon\Carbon;
class KSSController extends Controller
{

    public function licenses(Request $request)
    {
        if ($request->ajax()) {
            $data = OldKssSubscription::orderBy('id','asc');
            if($request->license_code != null && $request->license_code != ''){
                $data->where('name', $request->license_code);
            }
            if($request->is_exchanged === 0){
                $data->whereNull('coupon');
            }elseif($request->is_exchanged === 1){
                $data->whereNotNull('coupon');
            }
            if($request->customer != null && $request->customer != ''){
                $data->where(function($query) use($request){
                    $query->where('customer_name','LIKE','%'.$request->customer.'%');
                    $query->orWhere('customer_email','LIKE','%'.$request->customer.'%');
                });
            }
            if($request->coupon != null && $request->coupon != ''){
                $data->where('coupon', $request->coupon);
            }
            if($request->type != null && $request->type != ''){
                $data->where('is_new', $request->type);
            }
            if(isset($request->expiry_start_date) && $request->expiry_start_date != '' ){
                $data->whereBetween('end_date', [Carbon::parse($request->expiry_start_date), Carbon::parse($request->expiry_end_date)->addDay()]);
            }

            if(isset($request->start_date) && $request->start_date != '' ){
                $data->whereBetween('updated_at', [Carbon::parse($request->start_date), Carbon::parse($request->end_date)->addDay()]);
            }
            $datatable = Datatables::of($data);
            $datatable->addColumn('license', function ($row) {
                return $row->name;
            });
            $datatable->addColumn('customer', function ($row) {
                return $row->customer_name.'<br>'.$row->customer_email;
            });
            $datatable->addColumn('is_exchanged', function ($row) {
                return $row->is_exchanged == 1 ? 'Exchanged At<br>'.Carbon::parse($row->updated_at)->format('d-M-Y').'<br>'.$row->coupon : 'No';
            });
            $datatable->addColumn('expiry', function ($row) {
                
                return $row->end_date ? Carbon::parse($row->end_date)->format('d-M-Y') : '';
            });
            $datatable = $datatable->rawColumns(['license','customer','is_exchanged']);
            return $datatable->make(true);
        }

        return view('admin.kss.licenses');
    }
    public function vouchers(Request $request)
    {
        if ($request->ajax()) {
            $data = OldKssCompanyLicense::orderBy('id','asc');
            if($request->vouchers != null && $request->vouchers != ''){
                $data->where('name', $request->vouchers);
            }
            if($request->customer != null && $request->customer != ''){
                $data->where(function($query) use($request){
                    $query->where('customer_name','LIKE','%'.$request->customer.'%');
                    $query->orWhere('customer_email','LIKE','%'.$request->customer.'%');
                });
            }
            if($request->is_exchanged === 0){
                $data->whereNull('coupon');
            }elseif($request->is_exchanged === 1){
                $data->whereNotNull('coupon');
            }

            if($request->coupon != null && $request->coupon != ''){
                $data->where('coupon', $request->coupon);
            }
            if($request->type != null && $request->type != ''){
                $data->where('is_new', $request->type);
            }
            if(isset($request->start_date) && $request->start_date != '' ){
                $data->whereBetween('updated_at', [Carbon::parse($request->start_date), Carbon::parse($request->end_date)->addDay()]);

            }
            $datatable = Datatables::of($data);
            $datatable->addColumn('vouchers', function ($row) {
                return $row->name;
            });
            $datatable->addColumn('customer', function ($row) {
                return $row->customer_name.'<br>'.$row->customer_email;
            });
            $datatable->addColumn('is_exchanged', function ($row) {
                return $row->is_exchanged == 1 ? 'Exchanged At<br>'.Carbon::parse($row->updated_at)->format('d-M-Y').'<br>'.$row->coupon : 'No';
            });
            
            $datatable->addColumn('expiry', function ($row) {
                return $row->liecense ? Carbon::parse($row->liecense->end_date)->format('d-M-Y') : '';
            });
            $datatable = $datatable->rawColumns(['license','customer','is_exchanged']);
            return $datatable->make(true);
        }

        return view('admin.kss.vouchers');
    }
}
