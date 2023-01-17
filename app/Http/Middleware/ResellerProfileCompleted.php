<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\LogActivity;
use App\Models\LogVisitor;
use Illuminate\Http\Request;

class ResellerProfileCompleted
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {

        if(auth()->user())
        {
            $user = auth()->user();
            $contact = auth()->user()->contact;
            if($user->is_approved != 1){
                return redirect()->route('user.dashboard.profile')->with(session()->flash('alert-warning',__("Your profile isn't approved yet. Complete your profile and wait for admin approval.")));
            }
        }
        return $next($request);
    }

}
