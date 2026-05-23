<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Models\Profiles;

class CheckSessionTimeout
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
        // Check if the user is authenticated and if the session has expired
        $prf_code=session('profile_code','');
        if (isset($prf_code) && ! $request->session()->has('lastActivityTime')) {
            profiles::where('profile_code', $profile_code)
                                ->update(['isconnected' => 0]);
            // Redirect the user to the login page
            return redirect()->route('welcome');
        }

        return $next($request);
    }
}
