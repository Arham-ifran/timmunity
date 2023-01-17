<?php

namespace App\Http\Controllers\Distributor\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Distributor;

class ResetPasswordController extends Controller
{
    public function showResetForm(Request $request,  $token = null){

        return view('distributor.auth.password.reset')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }


    public function reset(Request $request){

        $email    = $request->email;
        $password = bcrypt($request->password);

        $update_password = Distributor::where('email', $email)->update([
            'password' => $password,
        ]);

        return redirect()->route('distributor.login.index')->with(session()->flash('alert-success',__("Your password is updated")));
    }
}
