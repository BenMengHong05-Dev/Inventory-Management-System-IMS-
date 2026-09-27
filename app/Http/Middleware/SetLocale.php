<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request and set application locale.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = session('locale') ?? $request->cookie('locale') ?? config('app.locale', 'km');

        if (!in_array($locale, ['km', 'en'])) {
            $locale = 'km';
        }

        app()->setLocale($locale);
        \Carbon\Carbon::setLocale($locale);

        return $next($request);
    }
}
