<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Auth;

class Localization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (  $request->segment(1) != null ) {
            App::setLocale($request->segment(1) );
        }
        else if (isset(Auth::user()->languages->iso_code)) {
            App::setLocale(Auth::user()->languages->iso_code);
        }
        else if (session()->has('locale')) {
            App::setLocale(session()->get('locale'));
        }
        return $next($request);
    }
}