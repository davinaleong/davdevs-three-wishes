<?php

use App\Models\User;
use App\Models\Theme;
use App\Http\Middleware\TwoFactorMiddleware;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;

describe('TwoFactorMiddleware', function () {
    beforeEach(function () {
        $this->user = User::factory()->create(['email_verified_at' => now()]);
        $this->theme = Theme::factory()->create(['year' => 2026, 'is_active' => true]);
        
        // Create test routes for middleware testing
        Route::middleware('two-factor')->get('/protected-route', function () {
            return response('Protected content');
        })->name('test.protected');
    });

    it('allows access when user is not authenticated', function () {
        $response = $this->get('/protected-route');
        
        // Should redirect to login (not 2FA challenge)
        $response->assertRedirect(route('login'));
    });

    it('allows access when user has 2FA disabled', function () {
        $response = $this->actingAs($this->user)->get('/protected-route');
        
        $response->assertStatus(200)
            ->assertSeeText('Protected content');
    });

    it('allows access when 2FA is verified in session', function () {
        $this->user->update([
            'two_factor_secret' => encrypt('TESTSECRET123456'),
            'two_factor_enabled_at' => now(),
            'two_factor_recovery_codes' => encrypt(['code1', 'code2'])
        ]);

        $response = $this->actingAs($this->user)
            ->withSession(['2fa_verified' => true])
            ->get('/protected-route');
        
        $response->assertStatus(200)
            ->assertSeeText('Protected content');
    });

    it('redirects to 2FA challenge when user has 2FA enabled but not verified', function () {
        $this->user->update([
            'two_factor_secret' => encrypt('TESTSECRET123456'),
            'two_factor_enabled_at' => now(),
            'two_factor_recovery_codes' => encrypt(['code1', 'code2'])
        ]);

        $response = $this->actingAs($this->user)->get('/protected-route');
        
        $response->assertRedirect(route('two-factor.challenge'));
        expect(session('2fa_required'))->toBeTrue();
    });

    it('allows access to 2FA routes to avoid redirect loops', function () {
        $this->user->update([
            'two_factor_secret' => encrypt('TESTSECRET123456'),
            'two_factor_enabled_at' => now(),
            'two_factor_recovery_codes' => encrypt(['code1', 'code2'])
        ]);

        // Test 2FA challenge route
        $response = $this->actingAs($this->user)
            ->withSession(['2fa_required' => true])
            ->get(route('two-factor.challenge'));
        
        $response->assertStatus(200);

        // Test 2FA verify route
        $response = $this->actingAs($this->user)->post(route('two-factor.verify'), [
            'code' => '000000',
            'recovery' => false
        ]);
        
        // Should process the request (even if code is invalid)
        $response->assertSessionHasErrors('code');
    });

    it('allows logout route to avoid redirect loops', function () {
        $this->user->update([
            'two_factor_secret' => encrypt('TESTSECRET123456'),
            'two_factor_enabled_at' => now(),
            'two_factor_recovery_codes' => encrypt(['code1', 'code2'])
        ]);

        $response = $this->actingAs($this->user)->post(route('logout'));
        
        $response->assertRedirect('/');
    });
});