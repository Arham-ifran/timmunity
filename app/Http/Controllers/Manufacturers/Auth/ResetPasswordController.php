<?php

namespace App\Http\Controllers\Manufacturers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Manufacturer;

class ResetPasswordController extends Controller
{
    public function showResetForm(Request $request,  $token = null){

        return view('manufacturers.auth.password.reset')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }


    public function reset(Request $request){

        $email    = $request->email;
        $password = bcrypt($request->password);
        
        $update_password = Manufacturer::where('manufacturer_email', $email)->update([
            'password' => $password,
        ]);
        
        return redirect()->route('manufacturers.login.index')->with(session()->flash('alert-success',__("Your password is updated")));
    }
}
