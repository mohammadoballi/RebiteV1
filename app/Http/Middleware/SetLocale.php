<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    protected array $supported = ['en', 'ar'];

    public function handle(Request $request, Closure $next): Response
    {
        $locale = session('locale', $request->user()?->locale);

        if ($locale && in_array($locale, $this->supported)) {
            App::setLocale($locale);
        }

        return $next($request);
    }
}
