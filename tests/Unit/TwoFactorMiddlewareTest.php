<?php

use App\Models\User;
use App\Models\Theme;
use App\Http\Middleware\TwoFactorMiddleware;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use PragmaRX\Google2FA\Google2FA;

describe('TwoFactorMiddleware', function () {
    beforeEach(function () {
        $this->user = User::factory()->create(['email_verified_at' => now()]);
        $this->theme = Theme::factory()->create(['year' => 2026, 'is_active' => true]);
    });

    it('allows access when user is not authenticated', function () {
        $middleware = new TwoFactorMiddleware();
        $request = Request::create('/test', 'GET');
        
        $response = $middleware->handle($request, function ($request) {
            return new Response('Protected content');
        });
        
        expect($response->getContent())->toBe('Protected content');
    });

    it('allows access when user has 2FA disabled', function () {
        $this->actingAs($this->user);
        
        $middleware = new TwoFactorMiddleware();
        $request = Request::create('/test', 'GET');
        
        $response = $middleware->handle($request, function ($request) {
            return new Response('Protected content');
        });
        
        expect($response->getContent())->toBe('Protected content');
    });

    it('allows access when 2FA is verified in session', function () {
        $google2fa = new Google2FA();
        $this->user->update([
            'two_factor_secret' => encrypt($google2fa->generateSecretKey()),
            'two_factor_enabled_at' => now(),
            'two_factor_recovery_codes' => encrypt(['code1', 'code2'])
        ]);
        
        $this->actingAs($this->user);
        session(['2fa_verified' => true]);

        $middleware = new TwoFactorMiddleware();
        $request = Request::create('/test', 'GET');
        
        $response = $middleware->handle($request, function ($request) {
            return new Response('Protected content');
        });
        
        expect($response->getContent())->toBe('Protected content');
    });

    it('redirects to 2FA challenge when user has 2FA enabled but not verified', function () {
        $google2fa = new Google2FA();
        $this->user->update([
            'two_factor_secret' => encrypt($google2fa->generateSecretKey()),
            'two_factor_enabled_at' => now(),
            'two_factor_recovery_codes' => encrypt(['code1', 'code2'])
        ]);
        
        // Refresh the user model to get updated attributes
        $this->user->refresh();
        
        // Verify 2FA is enabled
        expect($this->user->hasTwoFactorEnabled())->toBeTrue();
        
        $this->actingAs($this->user);
        
        // Make sure 2fa_verified is not in session
        session()->forget('2fa_verified');

        $middleware = new TwoFactorMiddleware();
        $request = Request::create('/test', 'GET');
        
        $response = $middleware->handle($request, function ($request) {
            return new Response('Protected content');
        });
        
        expect($response)->toBeInstanceOf(\Illuminate\Http\RedirectResponse::class);
        expect($response->getTargetUrl())->toContain('/two-factor/challenge');
        expect(session('2fa_required'))->toBeTrue();
    });

    it('allows access to 2FA routes to avoid redirect loops', function () {
        $google2fa = new Google2FA();
        $this->user->update([
            'two_factor_secret' => encrypt($google2fa->generateSecretKey()),
            'two_factor_enabled_at' => now(),
            'two_factor_recovery_codes' => encrypt(['code1', 'code2'])
        ]);
        
        $this->actingAs($this->user);

        $middleware = new TwoFactorMiddleware();
        
        // Test 2FA challenge route
        $request = Request::create('/two-factor/challenge', 'GET');
        $request->setRouteResolver(function () use ($request) {
            $route = new \Illuminate\Routing\Route(['GET'], '/two-factor/challenge', function () {});
            $route->name('two-factor.challenge');
            return $route;
        });
        
        $response = $middleware->handle($request, function ($request) {
            return new Response('2FA Challenge');
        });
        
        expect($response->getContent())->toBe('2FA Challenge');
        
        // Test logout route
        $request = Request::create('/logout', 'POST');
        $request->setRouteResolver(function () use ($request) {
            $route = new \Illuminate\Routing\Route(['POST'], '/logout', function () {});
            $route->name('logout');
            return $route;
        });
        
        $response = $middleware->handle($request, function ($request) {
            return new Response('Logout');
        });
        
        expect($response->getContent())->toBe('Logout');
    });

    it('allows logout route to avoid redirect loops', function () {
        $google2fa = new Google2FA();
        $this->user->update([
            'two_factor_secret' => encrypt($google2fa->generateSecretKey()),
            'two_factor_enabled_at' => now(),
            'two_factor_recovery_codes' => encrypt(['code1', 'code2'])
        ]);
        
        $this->actingAs($this->user);

        $middleware = new TwoFactorMiddleware();
        $request = Request::create('/logout', 'POST');
        $request->setRouteResolver(function () use ($request) {
            $route = new \Illuminate\Routing\Route(['POST'], '/logout', function () {});
            $route->name('logout');
            return $route;
        });
        
        $response = $middleware->handle($request, function ($request) {
            return new Response('Logout processed');
        });
        
        expect($response->getContent())->toBe('Logout processed');
    });
});