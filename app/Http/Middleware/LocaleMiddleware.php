<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;

class LocaleMiddleware
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
        $locale = 'en';
        if ($request->has('locale')) {
            $locale = $request->input('locale');
            if (!in_array($locale, $this->getValidLocations())) {
                $locale = 'en';
            }
        }
        App::setLocale($locale);

        return $next($request);
    }

    private function getValidLocations()
    {
        $locales = [
            'en',
            'ru'
        ];

        return $locales;
    }
}
