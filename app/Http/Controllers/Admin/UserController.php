<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Mail\VerifyEmail;
use App\Mail\AnnualWishReminder;
use App\Models\Theme;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    public function resendVerification(Request $request, User $user): RedirectResponse
    {
        if ($user->hasVerifiedEmail()) {
            return back()->with('error', 'User email is already verified.');
        }

        $user->sendEmailVerificationNotification();

        auth('admin')->user()->logActivity('ADMIN_USER_VERIFICATION_RESENT', [
            'user_id' => $user->id,
            'user_email' => $user->email
        ]);

        return back()->with('success', 'Verification email resent to ' . $user->email);
    }

    public function sendYearEndWishes(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'year' => 'nullable|integer|exists:themes,year',
        ]);

        $year = $request->get('year', date('Y'));
        $theme = Theme::where('year', $year)->first();

        if (!$theme) {
            return back()->with('error', "No theme found for year {$year}.");
        }

        $wishes = $user->wishesForTheme($theme)->get();

        if ($wishes->isEmpty()) {
            return back()->with('error', "User has no wishes for {$year}.");
        }

        Mail::to($user->email)->send(new AnnualWishReminder($user, $wishes, $theme));

        auth('admin')->user()->logActivity('ADMIN_USER_YEAR_END_EMAIL_SENT', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'theme_id' => $theme->id,
            'year' => $year,
            'wishes_count' => $wishes->count()
        ]);

        return back()->with('success', "Year-end wishes email sent to {$user->email} for {$year}.");
    }
}