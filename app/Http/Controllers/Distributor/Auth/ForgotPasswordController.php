<?php

namespace App\Http\Controllers\Distributor\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Distributor;
use Illuminate\Support\Str;
use App\Models\EmailTemplate;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm(){

        return view('distributor.auth.password.email');
    }

    public function sendResetLinkEmail(Request $request){

        $email = $request->email;
        $manufacture_details = Distributor::where('email',$email)->first();


        $key                       = Str::random(30);
        $expFormat = mktime(date("H") + 1, date("i") , date("s") , date("m") , date("d") , date("Y"));
        $expDate   = date("Y-m-d H:i:s", $expFormat);



        $manufacture_details_update_expire_link = Distributor::where('email',$email)->update([
            'expire_link' => $expDate,
        ]);



        $name       = $manufacture_details->name ?? '';
        $email      = $manufacture_details->email ?? '';



        $link = route('manufacturers.password.reset', ['token' => $key,'email' => $request->email]);

        $email_template = EmailTemplate::where('type','reset_password_manufacturer')->first();

        $lang = app()->getLocale();
        $email_template = transformEmailTemplateModel($email_template,$lang);

        $content = $email_template['content'];
        $subject = $email_template['subject'];

        $search = array("{{name}}","{{link}}","{{app_name}}");
        $replace = array($name,$link,env('APP_NAME'));
        $content = str_replace($search,$replace,$content);

        dispatch(new \App\Jobs\RegistrationEmailJob($email,$subject,$content));

        return redirect()->route('distributor.password.request')->with(session()->flash('status',__('We have emailed you password reset email.')));
    }
}
