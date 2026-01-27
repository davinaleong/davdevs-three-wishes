<?php

namespace App\Http\Controllers\Dev;

use App\Http\Controllers\Controller;
use App\Mail\AnnualWishReminder;
use App\Mail\VerifyEmail;
use App\Mail\WelcomeEmail;
use App\Models\Theme;
use App\Models\User;
use App\Services\ThemeService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class EmailController extends Controller
{
    public function dashboard(): View
    {
        return view('dev.emails.dashboard');
    }

    public function sendVerification(Request $request): RedirectResponse
    {
        $user = $request->user();
        $user->sendEmailVerificationNotification();
        
        return back()->with('success', 'Verification email sent to ' . $user->email);
    }

    public function sendPasswordReset(Request $request): RedirectResponse
    {
        $user = $request->user();
        $status = Password::sendResetLink(['email' => $user->email]);
        
        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('success', 'Password reset email sent to ' . $user->email);
        }
        
        return back()->with('error', 'Failed to send password reset email');
    }

    public function sendWelcome(Request $request): RedirectResponse
    {
        $user = $request->user();
        $activeTheme = ThemeService::getActiveTheme() ?: ThemeService::getCurrentYearTheme();
        
        Mail::to($user->email)->send(new WelcomeEmail($user, $activeTheme));
        
        return back()->with('success', 'Welcome email sent to ' . $user->email);
    }

    public function sendYearEndWishes(Request $request): RedirectResponse
    {
        $user = $request->user();
        
        $themeId = $request->get('theme_id');
        if ($themeId) {
            $theme = Theme::find($themeId);
        } else {
            $theme = ThemeService::getActiveTheme() ?: ThemeService::getCurrentYearTheme();
        }
        
        if (!$theme) {
            return back()->with('error', 'No active theme found');
        }
        
        $wishes = $user->wishesForTheme($theme)->get();
        
        if ($wishes->isEmpty()) {
            return back()->with('error', 'No wishes found for the selected theme/year');
        }
        
        Mail::to($user->email)->send(new AnnualWishReminder($user, $wishes, $theme));
        
        return back()->with('success', "Year-end wishes email sent for {$theme->year} to " . $user->email);
    }

    public function previewEmail(Request $request): View
    {
        $type = $request->get('type');
        $user = $request->user();
        
        switch ($type) {
            case 'verification':
                $verificationUrl = url('/verify-email/demo/hash');
                $mail = new VerifyEmail($user, $verificationUrl);
                break;
                
            case 'welcome':
                $theme = ThemeService::getActiveTheme() ?: ThemeService::getCurrentYearTheme();
                $mail = new WelcomeEmail($user, $theme);
                break;
                
            case 'year-end':
                $themeId = $request->get('theme_id');
                $theme = $themeId ? Theme::find($themeId) : (ThemeService::getActiveTheme() ?: ThemeService::getCurrentYearTheme());
                
                if (!$theme) {
                    abort(404, 'Theme not found');
                }
                
                $wishes = $user->wishesForTheme($theme)->get();
                if ($wishes->isEmpty()) {
                    // Create fake wishes for preview
                    $fakeWishes = collect([
                        (object) ['content' => 'To grow closer to God through daily prayer and scripture reading'],
                        (object) ['content' => 'To serve others in my community with love and compassion'],
                        (object) ['content' => 'To trust in God\'s plan even during difficult times'],
                    ]);
                    $mail = new AnnualWishReminder($user, $fakeWishes, $theme);
                } else {
                    $mail = new AnnualWishReminder($user, $wishes, $theme);
                }
                break;
                
            default:
                abort(404, 'Email type not found');
        }
        
        return view('dev.emails.preview', [
            'content' => $mail->render(),
            'type' => $type,
            'theme' => $theme ?? null
        ]);
    }
}