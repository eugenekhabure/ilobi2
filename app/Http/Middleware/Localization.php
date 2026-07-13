<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

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
        // Check session for locale
        if (Session::has('locale')) {
            App::setLocale(Session::get('locale'));
        } else {
            // Check user preference if logged in
            if (auth()->check() && auth()->user()->language) {
                App::setLocale(auth()->user()->language);
                Session::put('locale', auth()->user()->language);
            } else {
                // Fallback to default
                App::setLocale(config('app.locale', 'en'));
            }
        }

        return $next($request);
    }
}