<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Support\Facades\Password;
use Illuminate\Http\Request;
use App\Models\User;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;
        /**
     * Display the form to request a password reset link.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }
    public function sendResetLinkEmail(Request $request)
    {
        // dd('a');
        $user = User::whereHas('contact',function($query){
            $query->where('type','!=', 4);
        })->where('email', $request->get('email'))->get();

        if (!isset($user[0]) ) {
            // return $this->sendResetLinkFailedResponse($request,'passwords.user');
            return redirect()->back()->with(session()->flash('alert-error',__('Email ID not found in our system')));
            // return redirect()->back()->withErrors('passwords.user');
        }
        $this->validateEmail($request);

        $response = $this->broker()->sendResetLink(
            $request->only('email')
        );
        return $response == Password::RESET_LINK_SENT
                    ? redirect()->route('password.request')->with(session()->flash('status',__('We have emailed you password reset email.')))
                    : redirect()->back()->with(session()->flash('alert-error',__('Email ID not found in our system')));
                    // ? $this->sendResetLinkResponse($request,$response)
                    // : $this->sendResetLinkFailedResponse($request, $response);
    }
    /**
     * Get the broker to be used during password reset.
     *
     * @return \Illuminate\Contracts\Auth\PasswordBroker
    */
    public function broker()
    {
        return Password::broker();
    }
}
