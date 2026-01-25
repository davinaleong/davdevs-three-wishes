<?php

use App\Models\User;
use App\Models\Theme;
use App\Models\UserActivityLog;
use PragmaRX\Google2FA\Google2FA;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;

describe('TwoFactorController', function () {
    beforeEach(function () {
        Mail::fake();
        $this->user = User::factory()->create(['email_verified_at' => now()]);
        $this->theme = Theme::factory()->create(['year' => 2026, 'is_active' => true]);
    });

    describe('enable 2FA', function () {
        it('requires authentication', function () {
            $response = $this->post(route('two-factor.enable'));
            $response->assertRedirect(route('login'));
        });

        it('requires email verification', function () {
            $unverifiedUser = User::factory()->create(['email_verified_at' => null]);
            
            $response = $this->actingAs($unverifiedUser)->post(route('two-factor.enable'));
            $response->assertRedirect(route('verification.notice'));
        });

        it('requires password confirmation', function () {
            $response = $this->actingAs($this->user)->post(route('two-factor.enable'));
            $response->assertRedirect(route('password.confirm'));
        });

        it('displays setup page when 2FA is not enabled', function () {
            $response = $this->actingAs($this->user)
                ->withSession(['auth.password_confirmed_at' => time()])
                ->post(route('two-factor.enable'));

            $response->assertStatus(200)
                ->assertViewIs('profile.two-factor.setup')
                ->assertViewHas(['qrCode', 'secret', 'user']);
        });

        it('redirects when 2FA is already enabled', function () {
            // First enable 2FA for this user
            $google2fa = new Google2FA();
            $this->user->update([
                'two_factor_secret' => encrypt($google2fa->generateSecretKey()),
                'two_factor_enabled_at' => now(),
                'two_factor_recovery_codes' => encrypt(['code1', 'code2'])
            ]);

            $response = $this->actingAs($this->user)
                ->withSession(['auth.password_confirmed_at' => time()])
                ->post(route('two-factor.enable'));

            $response->assertRedirect(route('profile.edit'))
                ->assertSessionHas('error');
        });
    });

    describe('confirm 2FA setup', function () {
        beforeEach(function () {
            $this->secret = (new Google2FA())->generateSecretKey();
            session(['2fa_secret' => $this->secret]);
        });

        it('requires valid 6-digit code', function () {
            $response = $this->actingAs($this->user)
                ->withSession(['auth.password_confirmed_at' => time()])
                ->post(route('two-factor.confirm'), [
                    'code' => '12345' // only 5 digits
                ]);

            $response->assertSessionHasErrors('code');
        });

        it('validates the verification code', function () {
            $google2fa = new Google2FA();
            $validCode = $google2fa->getCurrentOtp($this->secret);

            $response = $this->actingAs($this->user)
                ->withSession(['auth.password_confirmed_at' => time()])
                ->post(route('two-factor.confirm'), [
                    'code' => $validCode
                ]);

            $response->assertStatus(200)
                ->assertViewIs('profile.two-factor.recovery-codes')
                ->assertViewHas('recoveryCodes');
        });

        it('rejects invalid verification codes', function () {
            $response = $this->actingAs($this->user)
                ->withSession(['auth.password_confirmed_at' => time()])
                ->post(route('two-factor.confirm'), [
                    'code' => '000000' // invalid code
                ]);

            $response->assertSessionHasErrors('code');
        });

        it('enables 2FA and generates recovery codes on valid confirmation', function () {
            $google2fa = new Google2FA();
            $validCode = $google2fa->getCurrentOtp($this->secret);

            $this->actingAs($this->user)
                ->withSession(['auth.password_confirmed_at' => time()])
                ->post(route('two-factor.confirm'), [
                    'code' => $validCode
                ]);

            $this->user->refresh();
            
            expect($this->user->hasTwoFactorEnabled())->toBeTrue();
            expect($this->user->two_factor_secret)->not->toBeNull();
            expect($this->user->two_factor_recovery_codes)->not->toBeNull();
            
            $recoveryCodes = decrypt($this->user->two_factor_recovery_codes);
            expect($recoveryCodes)->toHaveCount(8);
        });

        it('logs activity when 2FA is enabled', function () {
            $google2fa = new Google2FA();
            $validCode = $google2fa->getCurrentOtp($this->secret);

            $this->actingAs($this->user)
                ->withSession(['auth.password_confirmed_at' => time()])
                ->post(route('two-factor.confirm'), [
                    'code' => $validCode
                ]);

            $this->assertDatabaseHas('user_activity_logs', [
                'user_id' => $this->user->id,
                'action' => 'two_factor_enabled'
            ]);
        });

        it('redirects when session expired', function () {
            session()->forget('2fa_secret');

            $response = $this->actingAs($this->user)
                ->withSession(['auth.password_confirmed_at' => time()])
                ->post(route('two-factor.confirm'), [
                    'code' => '123456'
                ]);

            $response->assertRedirect(route('profile.edit'))
                ->assertSessionHas('error');
        });
    });

    describe('disable 2FA', function () {
        beforeEach(function () {
            $google2fa = new Google2FA();
            $this->user->update([
                'two_factor_secret' => encrypt($google2fa->generateSecretKey()),
                'two_factor_enabled_at' => now(),
                'two_factor_recovery_codes' => encrypt(['code1', 'code2'])
            ]);
        });

        it('requires current password', function () {
            $response = $this->actingAs($this->user)
                ->withSession(['auth.password_confirmed_at' => time()])
                ->delete(route('two-factor.disable'), [
                    'password' => 'wrongpassword'
                ]);

            $response->assertSessionHasErrors('password');
        });

        it('disables 2FA with correct password', function () {
            $response = $this->actingAs($this->user)
                ->withSession(['auth.password_confirmed_at' => time()])
                ->delete(route('two-factor.disable'), [
                    'password' => 'password' // default factory password
                ]);

            $response->assertRedirect(route('profile.edit'))
                ->assertSessionHas('success');

            $this->user->refresh();
            expect($this->user->hasTwoFactorEnabled())->toBeFalse();
            expect($this->user->two_factor_secret)->toBeNull();
            expect($this->user->two_factor_recovery_codes)->toBeNull();
        });

        it('redirects when 2FA is not enabled', function () {
            $userWithout2FA = User::factory()->create(['email_verified_at' => now()]);

            $response = $this->actingAs($userWithout2FA)
                ->withSession(['auth.password_confirmed_at' => time()])
                ->delete(route('two-factor.disable'), [
                    'password' => 'password'
                ]);

            $response->assertRedirect(route('profile.edit'))
                ->assertSessionHas('error');
        });

        it('logs activity when 2FA is disabled', function () {
            $this->actingAs($this->user)
                ->withSession(['auth.password_confirmed_at' => time()])
                ->delete(route('two-factor.disable'), [
                    'password' => 'password'
                ]);

            $this->assertDatabaseHas('user_activity_logs', [
                'user_id' => $this->user->id,
                'action' => 'two_factor_disabled'
            ]);
        });
    });

    describe('recovery codes', function () {
        beforeEach(function () {
            $this->recoveryCodes = ['RECOVERY1111', 'RECOVERY2222', 'RECOVERY3333', 'RECOVERY4444', 'RECOVERY5555', 'RECOVERY6666', 'RECOVERY7777', 'RECOVERY8888'];
            $google2fa = new Google2FA();
            $this->user->update([
                'two_factor_secret' => encrypt($google2fa->generateSecretKey()),
                'two_factor_enabled_at' => now(),
                'two_factor_recovery_codes' => encrypt($this->recoveryCodes)
            ]);
        });

        it('shows recovery codes when 2FA is enabled', function () {
            $response = $this->actingAs($this->user)
                ->withSession(['auth.password_confirmed_at' => time()])
                ->get(route('two-factor.recovery-codes'));

            $response->assertStatus(200)
                ->assertViewIs('profile.two-factor.recovery-codes')
                ->assertViewHas('recoveryCodes', $this->recoveryCodes);
        });

        it('redirects when 2FA is not enabled', function () {
            $userWithout2FA = User::factory()->create(['email_verified_at' => now()]);

            $response = $this->actingAs($userWithout2FA)
                ->withSession(['auth.password_confirmed_at' => time()])
                ->get(route('two-factor.recovery-codes'));

            $response->assertRedirect(route('profile.edit'))
                ->assertSessionHas('error');
        });

        it('regenerates recovery codes', function () {
            $response = $this->actingAs($this->user)
                ->withSession(['auth.password_confirmed_at' => time()])
                ->post(route('two-factor.regenerate-codes'));

            $response->assertStatus(200)
                ->assertViewIs('profile.two-factor.recovery-codes')
                ->assertSessionHas('success');

            $this->user->refresh();
            $newCodes = decrypt($this->user->two_factor_recovery_codes);
            
            expect($newCodes)->toHaveCount(8);
            expect($newCodes)->not->toEqual($this->recoveryCodes);
        });

        it('logs activity when recovery codes are regenerated', function () {
            $this->actingAs($this->user)
                ->withSession(['auth.password_confirmed_at' => time()])
                ->post(route('two-factor.regenerate-codes'));

            $this->assertDatabaseHas('user_activity_logs', [
                'user_id' => $this->user->id,
                'action' => 'two_factor_recovery_codes_regenerated'
            ]);
        });
    });

    describe('2FA verification challenge', function () {
        beforeEach(function () {
            $google2fa = new Google2FA();
            $this->user->update([
                'two_factor_secret' => encrypt($google2fa->generateSecretKey()),
                'two_factor_enabled_at' => now(),
                'two_factor_recovery_codes' => encrypt(['RECOVERY1111', 'RECOVERY2222'])
            ]);
        });

        it('shows challenge page when 2FA is required', function () {
            session(['2fa_required' => true]);

            $response = $this->actingAs($this->user)->get(route('two-factor.challenge'));

            $response->assertStatus(200)
                ->assertViewIs('auth.two-factor-challenge');
        });

        it('redirects when 2FA is not required', function () {
            $response = $this->actingAs($this->user)->get(route('two-factor.challenge'));

            $response->assertRedirect(route('dashboard'));
        });

        it('verifies valid authentication code', function () {
            $google2fa = new Google2FA();
            $validCode = $google2fa->getCurrentOtp(decrypt($this->user->two_factor_secret));

            $response = $this->actingAs($this->user)->post(route('two-factor.verify'), [
                'code' => $validCode,
                'recovery' => false
            ]);

            $response->assertRedirect(route('dashboard'));
            expect(session('2fa_required'))->toBeNull();
        });

        it('rejects invalid authentication code', function () {
            $response = $this->actingAs($this->user)->post(route('two-factor.verify'), [
                'code' => '000000',
                'recovery' => false
            ]);

            $response->assertSessionHasErrors('code');
        });

        it('verifies valid recovery code', function () {
            $response = $this->actingAs($this->user)->post(route('two-factor.verify'), [
                'code' => 'RECOVERY1111',
                'recovery' => true
            ]);

            $response->assertRedirect(route('dashboard'))
                ->assertSessionHas('warning');

            // Check that recovery code was removed
            $this->user->refresh();
            $remainingCodes = decrypt($this->user->two_factor_recovery_codes);
            expect($remainingCodes)->not->toContain('RECOVERY1111');
            expect($remainingCodes)->toContain('RECOVERY2222');
        });

        it('rejects invalid recovery code', function () {
            $response = $this->actingAs($this->user)->post(route('two-factor.verify'), [
                'code' => 'invalid-code',
                'recovery' => true
            ]);

            $response->assertSessionHasErrors('code');
        });

        it('logs successful authentication code verification', function () {
            $google2fa = new Google2FA();
            $validCode = $google2fa->getCurrentOtp(decrypt($this->user->two_factor_secret));

            $this->actingAs($this->user)->post(route('two-factor.verify'), [
                'code' => $validCode,
                'recovery' => false
            ]);

            $this->assertDatabaseHas('user_activity_logs', [
                'user_id' => $this->user->id,
                'action' => 'two_factor_verified'
            ]);
        });

        it('logs recovery code usage', function () {
            $this->actingAs($this->user)->post(route('two-factor.verify'), [
                'code' => 'RECOVERY1111',
                'recovery' => true
            ]);

            $this->assertDatabaseHas('user_activity_logs', [
                'user_id' => $this->user->id,
                'action' => 'two_factor_recovery_code_used'
            ]);
        });
    });

    describe('profile integration', function () {
        it('displays 2FA section in profile when not enabled', function () {
            $response = $this->actingAs($this->user)->get(route('profile.edit'));

            $response->assertStatus(200)
                ->assertSee('Two-Factor Authentication')
                ->assertSee('Enable Two-Factor Authentication')
                ->assertDontSee('Two-Factor Authentication is enabled');
        });

        it('displays enabled 2FA section in profile', function () {
            $google2fa = new Google2FA();
            $this->user->update([
                'two_factor_secret' => encrypt($google2fa->generateSecretKey()),
                'two_factor_enabled_at' => now(),
                'two_factor_recovery_codes' => encrypt(['code1', 'code2'])
            ]);

            $response = $this->actingAs($this->user)->get(route('profile.edit'));

            $response->assertStatus(200)
                ->assertSee('Two-Factor Authentication is enabled')
                ->assertSee('View Recovery Codes')
                ->assertSee('Disable Two-Factor Authentication');
        });
    });
});