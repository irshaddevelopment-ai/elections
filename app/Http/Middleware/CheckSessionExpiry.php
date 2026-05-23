<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckSessionExpiry
{
    public function handle(Request $request, Closure $next)
    {
        // Check if the session has expired
        if ($request->session()->has('expiry_time') && time() > $request->session()->get('expiry_time')) {
            // If session has expired, redirect to welcome page
            return redirect()->route('welcome');
        }

        // If session is active, proceed with the request
        return $next($request);
    }
}
