<?php

namespace App\Http\Middleware;

use App\Services\MainCore\TranslationService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $translationService = app(TranslationService::class);
        $locale = $translationService->getCurrentLanguageCode();
        
        app()->setLocale($locale);
        
        return $next($request);
    }
}

