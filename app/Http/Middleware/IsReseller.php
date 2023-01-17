<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsReseller
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        $user = $request->user();
        if ($user) {
            if ($user->contact->type != 3) {
                auth()->logout();
                return redirect()->route('login');
            }
        }
        return $next($request);
    }
}