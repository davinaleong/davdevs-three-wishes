<?php

namespace App\Http\Controllers;

use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use PragmaRX\Google2FA\Google2FA;
use Illuminate\Support\Str;

class TwoFactorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('verified');
        $this->middleware('password.confirm')->except(['challenge', 'verify']);
    }

    public function enable(Request $request)
    {
        $user = Auth::user();
        
        if ($user->hasTwoFactorEnabled()) {
            return redirect()->route('profile.edit')->with('error', 'Two-factor authentication is already enabled.');
        }

        $google2fa = new Google2FA();
        $secret = $google2fa->generateSecretKey();
        
        // Store the secret temporarily in session for verification
        $request->session()->put('2fa_secret', $secret);
        
        $qrCodeUrl = $google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secret
        );

        // Generate a simple QR code URL for display
        $qrCode = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . urlencode($qrCodeUrl);
        
        return view('profile.two-factor.setup', [
            'qrCode' => $qrCode,
            'secret' => $secret,
            'user' => $user
        ]);
    }

    public function confirm(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6'
        ]);

        $user = Auth::user();
        $secret = $request->session()->get('2fa_secret');
        
        if (!$secret) {
            return redirect()->route('profile.edit')->with('error', 'Two-factor setup session expired. Please try again.');
        }

        $google2fa = new Google2FA();
        $valid = $google2fa->verifyKey($secret, $request->code);
        
        if (!$valid) {
            throw ValidationException::withMessages([
                'code' => ['The verification code is invalid.']
            ]);
        }

        // Generate recovery codes
        $recoveryCodes = $this->generateRecoveryCodes();
        
        // Save 2FA settings
        $user->update([
            'two_factor_secret' => encrypt($secret),
            'two_factor_enabled_at' => now(),
            'two_factor_recovery_codes' => encrypt($recoveryCodes)
        ]);
        
        // Clear session
        $request->session()->forget('2fa_secret');
        
        // Log activity
        $user->logActivity('two_factor_enabled', [
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);
        
        return view('profile.two-factor.recovery-codes', [
            'recoveryCodes' => $recoveryCodes
        ]);
    }

    public function showRecoveryCodes()
    {
        $user = Auth::user();
        
        if (!$user->hasTwoFactorEnabled()) {
            return redirect()->route('profile.edit')->with('error', 'Two-factor authentication is not enabled.');
        }
        
        $recoveryCodes = decrypt($user->two_factor_recovery_codes);
        
        return view('profile.two-factor.recovery-codes', [
            'recoveryCodes' => $recoveryCodes
        ]);
    }

    public function regenerateRecoveryCodes(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->hasTwoFactorEnabled()) {
            return redirect()->route('profile.edit')->with('error', 'Two-factor authentication is not enabled.');
        }
        
        $recoveryCodes = $this->generateRecoveryCodes();
        
        $user->update([
            'two_factor_recovery_codes' => encrypt($recoveryCodes)
        ]);
        
        // Log activity
        $user->logActivity('two_factor_recovery_codes_regenerated', [
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);
        
        return view('profile.two-factor.recovery-codes', [
            'recoveryCodes' => $recoveryCodes
        ])->with('success', 'Recovery codes regenerated successfully.');
    }

    public function disable(Request $request)
    {
        $request->validate([
            'password' => 'required|current_password'
        ]);
        
        $user = Auth::user();
        
        if (!$user->hasTwoFactorEnabled()) {
            return redirect()->route('profile.edit')->with('error', 'Two-factor authentication is not enabled.');
        }
        
        $user->update([
            'two_factor_secret' => null,
            'two_factor_enabled_at' => null,
            'two_factor_recovery_codes' => null
        ]);
        
        // Log activity
        $user->logActivity('two_factor_disabled', [
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);
        
        return redirect()->route('profile.edit')->with('success', 'Two-factor authentication has been disabled.');
    }

    public function challenge()
    {
        if (!session('2fa_required')) {
            return redirect()->route('dashboard');
        }
        
        return view('auth.two-factor-challenge');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'recovery' => 'boolean'
        ]);

        $user = Auth::user();
        
        if (!$user->hasTwoFactorEnabled()) {
            return redirect()->route('dashboard');
        }

        if ($request->recovery) {
            return $this->verifyRecoveryCode($request, $user);
        }
        
        return $this->verifyCode($request, $user);
    }

    private function verifyCode(Request $request, $user)
    {
        $google2fa = new Google2FA();
        $secret = decrypt($user->two_factor_secret);
        $valid = $google2fa->verifyKey($secret, $request->code);
        
        if (!$valid) {
            throw ValidationException::withMessages([
                'code' => ['The verification code is invalid.']
            ]);
        }
        
        session()->forget('2fa_required');
        session(['2fa_verified' => true]);
        
        // Log successful verification
        $user->logActivity('two_factor_verified', [
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);
        
        return redirect()->intended(route('dashboard'));
    }

    private function verifyRecoveryCode(Request $request, $user)
    {
        $recoveryCodes = decrypt($user->two_factor_recovery_codes);
        $code = $request->code;
        
        if (!in_array($code, $recoveryCodes)) {
            throw ValidationException::withMessages([
                'code' => ['The recovery code is invalid.']
            ]);
        }
        
        // Remove used recovery code
        $recoveryCodes = array_diff($recoveryCodes, [$code]);
        $user->update([
            'two_factor_recovery_codes' => encrypt($recoveryCodes)
        ]);
        
        session()->forget('2fa_required');        session(['2fa_verified' => true]);        
        // Log recovery code usage
        $user->logActivity('two_factor_recovery_code_used', [
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'codes_remaining' => count($recoveryCodes)
        ]);
        
        return redirect()->intended(route('dashboard'))
            ->with('warning', 'You used a recovery code. Consider regenerating new ones.');
    }

    private function generateRecoveryCodes()
    {
        $codes = [];
        for ($i = 0; $i < 8; $i++) {
            $codes[] = Str::random(10);
        }
        return $codes;
    }
}