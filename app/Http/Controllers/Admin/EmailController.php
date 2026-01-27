<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Theme;
use App\Mail\VerifyEmail;
use App\Mail\AnnualWishReminder;
use App\Mail\WelcomeEmail;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Mail;

class EmailController extends Controller
{
    public function index(): View
    {
        auth('admin')->user()->logActivity('ADMIN_EMAIL_TOOLS_VIEWED');

        $themes = Theme::orderBy('year', 'desc')->get();
        $userCount = User::whereNotNull('email_verified_at')->count();

        return view('admin.emails.index', compact('themes', 'userCount'));
    }

    public function sendBroadcast(Request $request): RedirectResponse
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'target' => 'required|in:all,verified,unverified,2fa',
        ]);

        $query = User::query();

        switch ($request->get('target')) {
            case 'verified':
                $query->whereNotNull('email_verified_at');
                break;
            case 'unverified':
                $query->whereNull('email_verified_at');
                break;
            case '2fa':
                $query->whereNotNull('two_factor_enabled_at');
                break;
        }

        $users = $query->get();
        $count = $users->count();

        if ($count === 0) {
            return back()->with('error', 'No users match the selected criteria.');
        }

        // For demo/safety, limit to 100 users at once
        if ($count > 100) {
            return back()->with('error', 'Too many recipients. Please refine your target audience.');
        }

        $subject = $request->get('subject');
        $content = $request->get('content');

        foreach ($users as $user) {
            Mail::raw($content, function ($message) use ($user, $subject): void {
                $message->to($user->email)
                        ->subject($subject);
            });
        }

        auth('admin')->user()->logActivity('ADMIN_BROADCAST_EMAIL_SENT', [
            'subject' => $subject,
            'target' => $request->get('target'),
            'recipient_count' => $count
        ]);

        return back()->with('success', "Broadcast email sent to {$count} users successfully.");
    }

    public function sendYearEndBatch(Request $request): RedirectResponse
    {
        $request->validate([
            'year' => 'required|integer|exists:themes,year',
            'limit' => 'nullable|integer|min:1|max:100',
        ]);

        $theme = Theme::where('year', $request->get('year'))->first();
        $limit = $request->get('limit', 50);

        $users = User::whereNotNull('email_verified_at')
                    ->whereHas('wishes', function($q) use ($theme) {
                        $q->where('theme_id', $theme->id);
                    })
                    ->limit($limit)
                    ->get();

        $sentCount = 0;

        foreach ($users as $user) {
            $wishes = $user->wishesForTheme($theme)->get();
            
            if ($wishes->isNotEmpty()) {
                Mail::to($user->email)->send(new AnnualWishReminder($user, $wishes, $theme));
                $sentCount++;
            }
        }

        auth('admin')->user()->logActivity('ADMIN_YEAR_END_BATCH_SENT', [
            'theme_id' => $theme->id,
            'year' => $theme->year,
            'sent_count' => $sentCount,
            'limit' => $limit
        ]);

        return back()->with('success', "Year-end wishes emails sent to {$sentCount} users successfully.");
    }
}