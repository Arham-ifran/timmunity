<?php

namespace App\Http\Controllers\Distributor\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Distributor;
use Session;

class VerificationController extends Controller
{


    public function verifyUser(Request $request){

        $code = $request->code;
        $verify_email_details = Distributor::where('invitation_code', $code)->update([

            'is_email_verified' => 1,
        ]);
        Session::put('code', $code);
        return redirect()->route('distributor.password');

    }
    public function getPassword(Request $request){

        if($request->isMethod('post')){
            $code      = Session::get('code');
            $password  = $request->password;

            $get_details_of_distributor  = Distributor::where('invitation_code', $code)->update([
                'password' => bcrypt($password),
            ]);

            return redirect()->route('distributor.login.index');
        }

        return view('distributor.auth.password');
    }
}
