<?php

namespace App\Http\Controllers\Distributor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Models\Distributor;
use Hashids;
use Alert;
use App\Models\DistributorProductDetail;

class DashboardController extends Controller
{



    public function dashboard()
    {
        $distributor_id = Auth::guard('distributor')->user()->id;
        $data = [];
        $data['distributor_products'] = DistributorProductDetail::where('distributor_id',$distributor_id)->get();
        $data['active_distributor_products'] = DistributorProductDetail::where('distributor_id',$distributor_id)->where('is_active',1)->get();
        $data['inactive_distributor_products'] = DistributorProductDetail::where('distributor_id',$distributor_id)->where('is_active',0)->get();
        return view('distributor.dashboard')->with($data);
    }

    public function distributorProfile(Request $request){

        $distributor_id = Auth::guard('distributor')->user()->id;
        $data['model'] = Distributor::where('id', $distributor_id)->first();

        if($request->isMethod('post')){

            $id = Hashids::decode($request->id)[0];
            $distributor_details = Distributor::where('id',$id)->first();

            if($request->password != null){

                $password  = bcrypt($request->password);

            }else{

                $password  = $distributor_details->password;

            }

            $distributor_details = Distributor::where('id',$id)->update([

                'name' => $request->name,
                'email' => $request->email,
                'company' => $request->company,
                'password' => $password
            ]);
            Alert::success(__('Success'), __('Distributor Details Updated Successfully!'))->persistent('Close')->autoclose(5000);
            return redirect()->back();
        }

        return view('distributor.profile.profile', $data);
    }

}
