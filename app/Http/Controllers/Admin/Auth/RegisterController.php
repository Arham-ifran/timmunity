<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Hashids;
use Alert;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::ADMIN_HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'firstname' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:admins'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Admin
     */
    protected function create(array $data)
    {
        return Admin::create([
            'firstname' => $data['firstname'],
            'lastname' => $data['lastname'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'is_term_condition' => $data['is_term_condition'] ?? '0',
            'is_active'=> '1',
            'account_status'=> '1'
        ]);
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showRegistrationForm()
    {

        return redirect()->route('admin.login')->with(session()->flash('alert-success', 'Registration not allowed. Enter your credentials or contact the admin.'));
        return view('admin.auth.register');
    }

    /**
     * Get the guard to be used during registration.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard('admin');
    }

    /**
     * Verify admin user by Invitation email link.
     *
     * @param  array  $data
     * @return \App\Models\Admin
     */


     public function verifyAdminUser(Request $request){
        $data = [];
        $invitation_code = $request->code;
        // $data['admin'] = Admin::first();
        // dd('a');
        $data['admin'] = Admin::where(['invitation_code' => $invitation_code])->first();
        return view('admin.invitation.reset_password', $data);

    }

    // Confirm Account

    public function resetPassword(Request $request)
    {
        $input = $request->all();
        $id = Hashids::decode($input['id']);
        $this->validate($request, [

                'password' => 'required|string|min:8|confirmed',
            ]);

            $admin = Admin::find($id)->first();
            $admin->password =  Hash::make($input['password']);
            $admin->account_status =  1;
            $admin->is_active =  1;
            $admin->email_verified_at = date('Y-m-d H:i:s');
            $admin->invitation_code = '';
            $admin->save();
            // Auth::guard('admin')->logout();
            Auth::guard('admin')->login($admin);
            return redirect()->route('admin.login')->with(session()->flash('alert-success', __('Your account has been confirmed! Please Login Here.')));
    }
}
