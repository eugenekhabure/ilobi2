<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class IsHasBackendPermission
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
        // 🚀 TEMPORARILY BYPASS PERMISSION CHECK FOR TESTING
        // This allows any logged-in user to access the admin dashboard.
        // RE-ENABLE THIS AFTER TESTING BY UNCOMMENTING THE ORIGINAL CODE BELOW.
        
        return $next($request);

        // ORIGINAL CODE (commented out for testing):
        // $permissionRole = [1, 2, 3];
        // if (Auth::user() && in_array(Auth::user()->myrole, $permissionRole)) {
        //     return $next($request);
        // }
        // return redirect('/');
    }
}