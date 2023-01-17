<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\SentInvitationMail;
use App\Mail\SentResetPasswordMail;
use App\Models\Admin;
use App\Models\Contact;
use App\Models\User;
use App\Models\EmailTemplate;
use Hashids;

class InvitationMailController extends Controller
{
     public static function sendInvitationMail($name, $email, $invitation_code, $type) {
         if($type == "admin"){
             $link = route('admin.verify.admin', ['code' => $invitation_code]);
        }else{
            $link = route('verify.user', ['code' => $invitation_code]);
         }
        $email_template = EmailTemplate::where('type','sign_up_confirmation')->first();
        $lang = app()->getLocale();
        $email_template = transformEmailTemplateModel($email_template,$lang);
        $content = $email_template['content'];
        $subject = $email_template['subject'];
        $search = array("{{name}}","{{link}}","{{app_name}}");
        $replace = array($name,$link,env('APP_NAME'));
        $content = str_replace($search,$replace,$content);
        dispatch(new \App\Jobs\RegistrationEmailJob($email,$subject,$content));
    }

    public static function resendInvitationEmail(Request $request) {

        $input = $request->all();
    	$id = Hashids::decode($input['id']);
        $isReset = $input['is_reset_password'];
        $data = [];
        $user = null;
        if(isset($input['user'])){
            $model = Contact::where('id', $id)->first();
            if($model->user_id == null || $model->user_id == 0){
                $user = new User();
                $user->fill($input)->save();

                $user->invitation_code = sha1(time());
                $user->save();

                $model->user_id = $user->id;
                $model->save();
            } else {
                $user = User::find($model->user_id)->first();
                $user->invitation_code = sha1(time());
                $user->save();
            }


            $name = $user->name;
            $link = route('verify.user', ['code' => $user->invitation_code]);
            $email_template = EmailTemplate::where('type','sign_up_confirmation')->first();
            $lang = app()->getLocale();
            $email_template = transformEmailTemplateModel($email_template,$lang);
            $content = $email_template['content'];
            $subject = $email_template['subject'];
            $search = array("{{name}}","{{link}}","{{app_name}}");
            $replace = array($name,$link,env('APP_NAME'));
            $content = str_replace($search,$replace,$content);

            if($isReset == 1)
               Mail::to($user->email)->send(new SentResetPasswordMail($data));
            else
               dispatch(new \App\Jobs\RegistrationEmailJob($user->email,$subject,$content));
        }else{
            $admin = Admin::find($id)->first();
            $name = $admin->firstname .' '. $admin->lastname;
            $link = route('admin.verify.admin', ['code' => $input['invitation_code']]);
            if($isReset == 1)
                $email_template = EmailTemplate::where('type','reset_password')->first();
            else
                $email_template = EmailTemplate::where('type','sign_up_confirmation')->first();
            $lang = app()->getLocale();
            $email_template = transformEmailTemplateModel($email_template,$lang);
            $content = $email_template['content'];
            $subject = $email_template['subject'];
            $search = array("{{name}}","{{link}}","{{app_name}}");
            $replace = array($name,$link,env('APP_NAME'));
            $content = str_replace($search,$replace,$content);
            if($isReset == 1)
            {
                $admin->invitation_code = $input['invitation_code'];
                $admin->save();
                Mail::to($admin->email)->send(new SentResetPasswordMail($subject,$content));
            }
            else {

                dispatch(new \App\Jobs\RegistrationEmailJob($admin->email,$subject,$content));
            }
        }
    }

}

