<?php

namespace App\Http\Controllers\Manufacturers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Manufacturer;
use Session;

class VerificationController extends Controller
{


    public function verifyUser(Request $request){

        $code = $request->code;
        $verify_email_details = Manufacturer::where('invitation_code', $code)->update([

            'is_verify_email' => 1,
        ]);
        Session::put('code', $code);
        return redirect()->route('manufacturers.password');

    }
    public function getPassword(Request $request){

        if($request->isMethod('post')){
            $code      = Session::get('code');
            $password  = $request->password;

            $get_details_of_manufacturer  = Manufacturer::where('invitation_code', $code)->update([
                'password' => bcrypt($password),
            ]);

            return redirect()->route('manufacturers.login.index');
        }

        return view('manufacturers.auth.password');
    }
}
