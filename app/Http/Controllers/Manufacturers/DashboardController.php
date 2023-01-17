<?php

namespace App\Http\Controllers\Manufacturers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Models\Products;
use App\Models\Manufacturer;
use App\Models\Contact;
use App\Models\VoucherOrder;
use App\Models\Voucher;
use App\Models\Quotation;
use Session;
use Hashids;
use Alert;

class DashboardController extends Controller
{
    public function __construct()
    {
        // dd('a');
        // $this->middleware(['manufacture']); 
    }


    public function dashboard(){
        $manufacturer_id = Auth::guard('manufacture')->user()->id;

        $total_products             = Products::with('manufacturer')->where('manufacturer_id',$manufacturer_id)->get();
        $data['count_products']     = count($total_products);
        $data['order_products']          = Quotation::whereHas('order_lines.product', function($query) use($manufacturer_id){
            $query->where('manufacturer_id', $manufacturer_id);
        })->count();
        $data['voucher_orders']     = VoucherOrder::whereHas('product.manufacturer', function($query) use($manufacturer_id){
            $query->where('id', $manufacturer_id);
        })->count();

        $resellers = Products::whereHas('voucherOrders.reseller', function($query) {
            $query->where('reseller_id',24);
        })->with(['voucherOrders','manufacturer'])->where('manufacturer_id',$manufacturer_id)->get();


        $data['products_by_manufacturers'] = Products::with(['manufacturer','generalInformation'])->has('manufacturer')->where('manufacturer_id',$manufacturer_id)->get();


        return view('manufacturers.dashboard')->with($data);
    }

    public function manufacturerProfile(Request $request){

        $manufacturer_id = Auth::guard('manufacture')->user()->id;
        $data['model'] = Manufacturer::where('id', $manufacturer_id)->first();
        
        if($request->isMethod('post')){
           
            $id = Hashids::decode($request->id)[0];
            $manufacturer_details = Manufacturer::where('id',$id)->first();

            if($request->password != null){

                $password  = bcrypt($request->password);
                
            }else{

                $password  = $manufacturer_details->password;
               
            }
           
            $manufacturer_details = Manufacturer::where('id',$id)->update([
                    
                'manufacturer_name' => $request->manufacturer_name,
                'manufacturer_email' => $request->manufacturer_email,
                'company' => $request->company,
                'password' => $password
            ]);
            Alert::success(__('Success'), __('Manufacturer Details Updated Successfully!'))->persistent('Close')->autoclose(5000);
            return redirect()->back();
        }
        
        return view('manufacturers.profile.profile', $data);
    }

}
