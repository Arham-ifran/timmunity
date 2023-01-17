<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use App\Models\Contact;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\ContactCountry;
use App\Models\EmailTemplate;
use Hashids;
use Illuminate\Auth\Events\Registered;
use App\Http\Controllers\Admin\InvitationMailController;
use App\Mail\SentInvitationMail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use App\Models\ResellerRedeemedPage;
use Auth;
use Illuminate\Support\Facades\Mail;

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
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // dd('a');
        // $this->middleware('guest');
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }

    /**
     * Verify user by Invitation email link.
     *
     * @param  array  $data
     * @return \App\Models\Admin
     */


    public function verifyUser(Request $request)
    {
        // dd('a');
        $data = [];
        $invitation_code = $request->code;
        $data['user'] = User::with('contact')->where(['invitation_code' => $invitation_code])->first();
        if(isset($data['user']) && $data['user']->contact->type == 3 && $data['user']->password != null)
        {
            $user = User::find($data['user']->id)->where(['invitation_code' => $invitation_code])->first();
             $user->email_verified_at = date('Y-m-d H:i:s');
             $user->invitation_code = '';
             $user->account_status =  1;
             $user->save();
             Auth::login($user);
             return redirect()->route('login')->with(session()->flash('alert-success', __('Your account has been confirmed! You can log in once the account is active from admin.')));
        }
        else if(isset($data['user']) && $data['user']->contact->type == 2 && $data['user']->password != null) {
            $user = User::find($data['user']->id)->where(['invitation_code' => $invitation_code])->first();
             $user->email_verified_at = date('Y-m-d H:i:s');
             $user->invitation_code = '';
             $user->save();
             Auth::login($user);
             return redirect()->route('login')->with(session()->flash('alert-success', __('Your account has been confirmed! Please Login Here.')));
            // return view('auth.passwords.reset_password', $data);
        }
        else {
            return view('auth.passwords.reset_password', $data);

            return redirect()->route('login')->with(session()->flash('alert-info', __('Your Account Already Confirmed! Please Login Here.')));
        }
        return redirect()->route('frontside.home.shop')->with(session()->flash('alert-warning',__('Invalid User')));

    }

    // Confirm Account and Set Password

    public function resetPassword(Request $request)
    {
        $input = $request->all();
        $id = Hashids::decode($input['id']);
        $this->validate($request, [
            'password' => 'required|string|min:8|confirmed',
        ]);
        $user = User::find($id)->first();
        // if($user->contact->type != 3 || $user->password != null){
        // dd($user->password);
        if( $user->password == null){
            $user->password =  Hash::make($input['password']);
        }
        $user->email_verified_at = date('Y-m-d H:i:s');
        $user->invitation_code = '';
        $user->save();

        if($user->is_active != 1)
        {
            return redirect()->route('login')->with(session()->flash('alert-warning', __('Your account has been confirmed! You can login once account is approved from admin.')));
        }
        Auth::login($user);
        return redirect()->route('frontside.home.index');

        return redirect()->route('login')->with(session()->flash('alert-success', __('Your account has been confirmed! Please Login Here.')));
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showRegistrationForm()
    {
        $data['countries'] = ContactCountry::all();
        return view('auth.register',$data);
    }
    /**
     * Show the application registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showResellerRegistrationForm()
    {
        $data['countries'] = ContactCountry::all();
        return view('auth.reseller-register',$data);
    }
    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        // $this->validator($request->all())->validate();
        $validate = $this->validate($request, [
                'name' => 'required|string|max:50',
                // 'email' => 'required|string|email|max:255|regex:/^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/|unique:users',
                'email' => 'required|string|email|max:255|regex:/^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/',
                'password' => 'required|string|min:8|confirmed',
                'country_id' => 'required',
                'g-recaptcha-response' => 'required|recaptcha'
            ],
            [
                'g-recaptcha-response.recaptcha' => 'Captcha verification failed',
                'g-recaptcha-response.required' => 'Please complete the captcha'
            ]);
        $input = $request->all();
        $check_user = User::whereHas('contact',function($query) use($input){
            $query->where('type','!=', 4);
        })->where('email', $input['email'])->first();
        // $check_user = Contact::where('email', $input['email'])->first();
        if($check_user){
            return redirect()->back()->with(session()->flash('alert-error', __('Email already registered')));
        }

        $check_guest_user = User::whereHas('contact',function($query) use($input){
            $query->where('type', 4);
        })->where('email', $input['email'])->first();

        $user = $check_guest_user ? $check_guest_user : new User();
            $user->fill($input)->save();
            $user->password =  Hash::make($input['password']);
            $user->account_status =  1;
            $user->is_active =  1;
            $user->is_approved =  1;
            $user->email_verified_at = null;
            $user->invitation_code = sha1(time());
        $user->save();
        $contact = $check_guest_user ? Contact::where('user_id', $user->id)->first() : new Contact();
            $contact->fill($input);
            $contact->user_id = $user->id;
            $contact->type = $input['user_type'];
        $contact->save();

        // Tranformation of Reseller Email Template
        $name = $user->name;
        $email = $user->email;
        $link = route('verify.user', ['code' => $user->invitation_code]);
        $email_template = EmailTemplate::where('type','customer_sign_up_confirmation')->first();
        $lang = app()->getLocale();
        $email_template = transformEmailTemplateModel($email_template,$lang);
        $content = $email_template['content'];
        $subject = $email_template['subject'];
        $search = array("{{name}}","{{link}}","{{app_name}}");
        $replace = array($name,$link,env('APP_NAME'));
        $content = str_replace($search,$replace,$content);
        dispatch(new \App\Jobs\RegistrationEmailJob($email,$subject,$content));
        Auth::login($user);
        
        return redirect()->route('login')->with(session()->flash('alert-success', __('Verification email sent to').' '.$user->email.' '.__('Kindly verify your email.')));

    }
    public function registerReseller(Request $request)
    {
        $input = $request->all();
        $validate = $this->validate($request, [
            'name' => 'required|string|max:50',
            'email' => 'required|string|email|max:255|regex:/^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/',
            'password' => 'required|string|min:8|confirmed',
            'country_id' => 'required',
            'g-recaptcha-response' => 'required|recaptcha'
        ], [
                'g-recaptcha-response.recaptcha' => 'Captcha verification failed',
                'g-recaptcha-response.required' => 'Please complete the captcha'
        ]);
        $check_user = User::whereHas('contact',function($query) use($input){
            $query->where('type','!=', 4);
        })->where('email', $input['email'])->first();
        // $check_user = Contact::where('email', $input['email'])->first();
        if($check_user){
            return redirect()->back()->with(session()->flash('alert-error', __('Email already registered')));
        }

        $check_guest_user = User::whereHas('contact',function($query) use($input){
            $query->where('type', 4);
        })->where('email', $input['email'])->first();

        $user = $check_guest_user ? $check_guest_user : new User();
            $user->fill($input)->save();
            $user->password =  Hash::make($input['password']);
            $user->account_status =  1;
            $user->is_active =  1;
            $user->email_verified_at = null;
            $user->invitation_code = sha1(time());
        $user->save();
        // dd($input);
        $contact = $check_guest_user ? Contact::where('user_id', $user->id)->first() : new Contact();
       
            // $contact->fill($input);
            $contact->fill($input);
            $contact->user_id = $user->id;
            $contact->type = $input['user_type'];
            $contact->company_name = $input['company_name'];
        $contact->save();
       
        $site_settings = \App\Models\SiteSettings::all();

        // Reseller Redeem Page
        $reseller_id = $user->id;
        $redeem_model = new ResellerRedeemedPage();
        $redeem_model->title = $user->name;
        $redeem_model->url =  $input['redeem_page_url'].'/'.Hashids::encode($reseller_id);
        $redeem_model->description =  '<p>This is the voucher redeem page for <b>'.$user->name.'</b>.</p><p>Please add voucher redeem code in below field.</p><p>{{voucher_form}}</p>';
        $redeem_model->reseller_id = $reseller_id;
        $redeem_model->is_reseller_changed = 0;
        $redeem_model->logo = 'logo.png';
        $redeem_model->email = $user->email;
        $redeem_model->color = '#009b72';
        $redeem_model->save();
        // Tranformation of Reseller Email Template
        $name = $user->name;
        $email = $user->email;
        $link = route('verify.user', ['code' => $user->invitation_code]);

        $email_template = EmailTemplate::where('type','reseller_sign_up_confirmation')->first();
        $lang = app()->getLocale();
        $email_template = transformEmailTemplateModel($email_template,$lang);
        $content = $email_template['content'];
        $subject = $email_template['subject'];
        $search = array("{{name}}","{{link}}","{{app_name}}");
        $replace = array($name,$link,env('APP_NAME'));
        $content = str_replace($search,$replace,$content);
        dispatch(new \App\Jobs\RegistrationEmailJob($email,$subject,$content));
        // $data = new SentInvitationMail($subject,$content);
        // Mail::to($email)->send($data);
        $user = User::find($user->id);
        Auth::login($user);
        return redirect()->route('user.dashboard.profile')->with(session()->flash('alert-success', __('Verification email sent to').' '.$user->email.' '.__('Kindly verify your email.')));

        return redirect()->route('frontside.home.index')->with(session()->flash('alert-success', __('Verification email sent to').' '.$user->email.' '.__('Kindly verify your email.')));

    }

}
