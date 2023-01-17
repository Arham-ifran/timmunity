<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ContactCountry;
use App\Models\AppsCountry;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware(['auth','verified']);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {

        return view('home');
    }

    public function countriesLatLng()
    {
        $countries = ContactCountry::all();
        echo('array(<br>');
        foreach($countries as $country){
            echo('   array("id" => '.$country->id.', "name" => "'.$country->name.'", "country_code" => "'.$country->country_code.'", "country_calling_code" => "'.$country->country_calling_code.'", "is_active" => "'.$country->is_active.'", "latitude" => "'.$country->latitude.'", "longitude" => "'.$country->longitude.'"),<br>');
        }
        echo(');');
    }
}
