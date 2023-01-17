<?php

namespace App\Http\Controllers\Distributor\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Providers\RouteServiceProvider;
use App\Models\Distributor;
use Auth;
use App\Models\EmailTemplate;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    use AuthenticatesUsers;
    protected $redirectTo = RouteServiceProvider::DISTRIBUTOR_HOME;
    public function showLoginForm(Request $request){
        return view('distributor.auth.login');
    }
    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    public function login(Request $request)
    {
        $user = Distributor::where('email', $request->input('email'))->first();

        if(!empty($user)){
            if($user->is_email_verified  != 0) {

                if(Auth::guard('distributor')->attempt(['email' => $request->email, 'password' => $request->password]))
                {
                    $auth_user   = Auth::guard('distributor')->user();
                    return  redirect()->route('distributor.dashboard');
                }
                else
                {
                    return redirect()->back()->with(session()->flash('alert-error',__("The credentials doesn't match our records")));
                }
            }else{

                $key                       = Str::random(30);
                $user_update_invitaion_code = Distributor::where('email', $request->input('email'))->update([
                    'invitation_code' => $key,
                ]);

                $name       = $user->name;
                $email      = $user->email;

                $link = route('distributor.verify', ['code' => $key]);

                $email_template = EmailTemplate::where('type','distributor_verify_email')->first();

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

        Auth::guard('distributor')->logout();
        return redirect()->route('distributor.login.index');
    }
}
