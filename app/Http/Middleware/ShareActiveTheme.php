<?php

namespace App\Http\Middleware;

use App\Services\ThemeService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class ShareActiveTheme
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $activeTheme = ThemeService::getActiveTheme() ?: ThemeService::getCurrentYearTheme();
        
        View::share('activeTheme', $activeTheme);
        View::share('themeColors', $activeTheme->getColors());
        View::share('themeCssVariables', ThemeService::getCssVariables($activeTheme));
        
        return $next($request);
    }
}
