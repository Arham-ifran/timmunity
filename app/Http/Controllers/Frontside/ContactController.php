<?php

namespace App\Http\Controllers\Frontside;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Mail\ContactToSupportCenter;
use App\Mail\ContactUsInquiryReceivedEmail;
use App\Models\SiteSettings;
use App\Models\ContactUsQueries;
use App\Models\EmailTemplate;
use Illuminate\Support\Facades\Mail;
use Alert;
use Auth;

class ContactController extends Controller
{
	public function index(Request $request){
        $data['site_settings'] = SiteSettings::first();
        return (!empty(Auth::user()) && Auth::user()->email_verified_at == null) ? view('frontside.contact.index',$data)->with(session()->flash('alert-warning', __('Your email is unverified! Kindly verify your email.'))) : view('frontside.contact.index',$data);
    }

    // Send Mail on Submit Contact us form

    public function submit(Request $request){
        $this->validate($request, [
            'g-recaptcha-response' => 'required|recaptcha'
        ], [
                'g-recaptcha-response.recaptcha' => 'Captcha verification failed',
                'g-recaptcha-response.required' => 'Please complete the captcha'
        ]);
        $input = $request->all();
        $model = new ContactUsQueries();
        $model->fill($input)->save();
        // dd($model->name, $model->email);
        $date = date('d M, Y', strtotime($model->created_at));
        $name = $model->name;
        $email = $model->email;
        $phone = $model->phone;
        $subject = $model->email;
        $message = $model->message;
        $submitted_temp = EmailTemplate::where('type','contact_us_inquiry_submitted')->first();
        $received_temp = EmailTemplate::where('type','contact_us_inquiry_received')->first();
        $lang = app()->getLocale();
        $submitted_temp = transformEmailTemplateModel($submitted_temp,$lang);
        $received_temp = transformEmailTemplateModel($received_temp,$lang);
        $submitted_content = $submitted_temp['content'];
        $submitted_subject = $submitted_temp['subject'];
        $received_content = $received_temp['content'];
        $received_subject = $received_temp['subject'];
        // Submitted Content
        $sub_search = array("{{name}}","{{date}}","{{fullname}}","{{email}}","{{phone}}","{{subject}}","{{message}}","{{app_name}}");
        $sub_replace = array($name,$date,$name,$email,$phone,$subject,$message,env('APP_NAME'));
        $submitted_content = str_replace($sub_search,$sub_replace,$submitted_content);
        // Received Content
        $received_search = array("{{name}}","{{app_name}}");
        $received_replace = array($name,env('APP_NAME'));
        $received_content = str_replace($received_search,$received_replace,$received_content);
        // Contact Us Inquiry Email Submitted
        // dd($received_content);
    	Mail::queue(new ContactToSupportCenter($email,$submitted_subject,$submitted_content));
        // // Contact Us Inquiry Email Received
        Mail::queue(new ContactUsInquiryReceivedEmail($email,$received_subject,$received_content));
        return redirect()->route('frontside.contact.index')->with(session()->flash('alert-success', __('Thanks for contacting us! Our Representative will contact you shortly on your given information.')));
    }
}
