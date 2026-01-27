<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserActivityLog;
use App\Mail\VerifyEmail;
use App\Mail\AnnualWishReminder;
use App\Models\Theme;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        auth('admin')->user()->logActivity('ADMIN_USERS_VIEWED');

        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('uuid', 'like', "%{$search}%");
            });
        }

        if ($request->filled('verified')) {
            if ($request->get('verified') === 'yes') {
                $query->whereNotNull('email_verified_at');
            } else {
                $query->whereNull('email_verified_at');
            }
        }

        if ($request->filled('two_factor')) {
            if ($request->get('two_factor') === 'yes') {
                $query->whereNotNull('two_factor_enabled_at');
            } else {
                $query->whereNull('two_factor_enabled_at');
            }
        }

        $users = $query->withCount('wishes')
                      ->latest()
                      ->paginate(20)
                      ->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function show(User $user): View
    {
        auth('admin')->user()->logActivity('ADMIN_USER_VIEWED', ['user_id' => $user->id]);

        $user->load(['wishes.theme', 'activityLogs']);
        
        $wishesGroupedByTheme = $user->wishes->groupBy('theme.year');

        return view('admin.users.show', compact('user', 'wishesGroupedByTheme'));
    }

    public function resendVerification(Request $request, User $user): RedirectResponse
    {
        if ($user->hasVerifiedEmail()) {
            return back()->with('error', 'User email is already verified.');
        }

        $user->sendEmailVerificationNotification();

        auth('admin')->user()->logActivity('ADMIN_VERIFICATION_RESENT', [
            'user_id' => $user->id,
            'user_email' => $user->email
        ]);

        return back()->with('success', 'Verification email sent successfully.');
    }

    public function sendYearEndWishes(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'year' => 'required|integer|exists:themes,year',
        ]);

        $theme = Theme::where('year', $request->get('year'))->first();
        $wishes = $user->wishesForTheme($theme)->get();

        if ($wishes->isEmpty()) {
            return back()->with('error', 'User has no wishes for the selected year.');
        }

        Mail::to($user->email)->send(new AnnualWishReminder($user, $wishes, $theme));

        auth('admin')->user()->logActivity('ADMIN_YEAR_END_EMAIL_SENT', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'theme_id' => $theme->id,
            'year' => $theme->year
        ]);

        return back()->with('success', 'Year-end wishes email sent successfully.');
    }

    public function activityLogs(Request $request, User $user): View
    {
        auth('admin')->user()->logActivity('ADMIN_USER_ACTIVITY_VIEWED', ['user_id' => $user->id]);

        $query = $user->activityLogs();

        if ($request->filled('action')) {
            $query->where('action', $request->get('action'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->get('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->get('date_to'));
        }

        $logs = $query->latest()->paginate(50)->withQueryString();
        $actions = UserActivityLog::distinct()->pluck('action');

        return view('admin.users.activity-logs', compact('user', 'logs', 'actions'));
    }
}