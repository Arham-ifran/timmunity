<?php

namespace App\Http\Controllers\Frontside;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;

class AboutController extends Controller
{
    public function index(Request $request){
        $data = [];
        $testing = null;
        return (!empty(Auth::user()) && Auth::user()->email_verified_at == null) ? view('frontside.about.index',$data)->with(session()->flash('alert-warning', __('Your email is unverified! Kindly verify your email.'))) : view('frontside.about.index',$data);

    }
}
