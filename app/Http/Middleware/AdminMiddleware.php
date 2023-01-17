<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, ...$guards)
    {
        $user = Admin::all()->count();
        if (!($user == 1)) {
            if (!Auth::user()->hasRole('Administrator')) {
                access_denied();
            }
        }
        
        return $next($request);
    }
}