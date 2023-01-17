<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;

class TrackLastActiveAt
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
        if (! $request->user()) {
            return $next($request);
        }

        if (! $request->user()->last_active_at || Carbon::parse($request->user()->last_active_at)->isPast()) {
            $request->user()->last_active_at = now();
            $request->user()->save();
            // $request->user()->update([
            //     'last_active_at' => now(),
            // ]);
        }
        // die($request->user());

        return $next($request);
    }
}
