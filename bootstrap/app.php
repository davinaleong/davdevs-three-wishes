<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Trust all proxies for Heroku HTTPS detection
        $middleware->trustProxies(at: '*');
        
        $middleware->web(append: [
            \App\Http\Middleware\ShareActiveTheme::class,
        ]);
    })
    ->withSchedule(function (Schedule $schedule): void {
        // Check daily if it's December 31st and send annual wish emails
        $schedule->command('wishes:send-annual-emails-if-december-31st')
                 ->dailyAt('10:00')
                 ->timezone('UTC')
                 ->onOneServer()
                 ->runInBackground();
    })
    ->withEvents(function (): void {
        // Send welcome email when user verifies their email
        \Illuminate\Support\Facades\Event::listen(
            \Illuminate\Auth\Events\Verified::class,
            \App\Listeners\SendWelcomeEmail::class
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
