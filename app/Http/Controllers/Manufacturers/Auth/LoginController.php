<?php

namespace App\Http\Controllers\Manufacturers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Providers\RouteServiceProvider;
use App\Models\Manufacturer;
use Auth;
use Alert;
use App\Models\EmailTemplate;
use Session;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    use AuthenticatesUsers;
    protected $redirectTo = RouteServiceProvider::MANUFACTURER_HOME;




    public function showLoginForm(Request $request){


        return view('manufacturers.auth.login');
    }
    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */

    public function login(Request $request)
    {

        // return $request->all();

        $user = Manufacturer::where('manufacturer_email', $request->input('email'))->first();

        if(!empty($user)){
            if($user->is_verify_email  != 0) {

                if(Auth::guard('manufacture')->attempt(['manufacturer_email' => $request->email, 'password' => $request->password]))
                {
                    $auth_user   = Auth::guard('manufacture')->user();
                    // dd( $auth_user);
                    return  redirect()->route('manufacturers.dashboard');
                }
                else
                {
                    return redirect()->back()->with(session()->flash('alert-error',__("The credentials doesn't match our records")));
                }

            }else{

                $key                       = Str::random(30);
                $user_update_invitaion_code = Manufacturer::where('manufacturer_email', $request->input('email'))->update([
                    'invitation_code' => $key,
                ]);

                $name       = $user->manufacturer_name;
                $email      = $user->manufacturer_email;

                $link = route('manufacturers.verify', ['code' => $key]);

                $email_template = EmailTemplate::where('type','manufacturer_verify_email')->first();

                $lang = app()->getLocale();
                $email_template = transformEmailTemplateModel($email_template,$lang);

                $content = $email_template['content'];
                $subject = $email_template['subject'];

                $search = array("{{name}}","{{link}}","{{app_name}}");
                $replace = array($name,$link,env('APP_NAME'));
                $content = str_replace($search,$replace,$content);

                dispatch(new \App\Jobs\RegistrationEmailJob($email,$subject,$content));

                return redirect()->back()->with(session()->flash('alert-error',__("We sent you an email. Please Verify Your Email First!")));
            }
        }else{

            return redirect()->back()->with(session()->flash('alert-error',__("User Not Found")));
        }
    }


    public function logout(){

        Auth::guard('manufacture')->logout();
        return redirect()->route('manufacturers.login.index');
    }
}
