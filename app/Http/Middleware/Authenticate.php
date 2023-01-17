<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Auth\AuthenticationException;
use Closure;
use Illuminate\Http\Request;
use Auth;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        return $request;
        // return route('login');
    }


    /**
     * Handle an unauthenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     *  @param  Closure  $next
     * @param  array  $guards
     * @return void
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */


    protected function unauthenticated($request, array $guards)
    {
        // dd('a');
        $route = route('login');
        if(in_array('admin', $guards)){
            $route = route('admin.login');
        }
        // dd($route);
        throw new AuthenticationException(
            // 'Unauthenticated.', $guards, $this->redirectTo($request, $guards),
            'Unauthenticated.', $guards, $this->redirectTo($route, $guards),
        );
    }




}
