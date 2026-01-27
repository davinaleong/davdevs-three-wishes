<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Theme;
use App\Models\User;
use App\Models\UserActivityLog;
use App\Models\Wish;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total_users' => User::count(),
            'verified_users' => User::whereNotNull('email_verified_at')->count(),
            'users_with_2fa' => User::whereNotNull('two_factor_enabled_at')->count(),
            'total_wishes' => Wish::count(),
            'active_themes' => Theme::where('is_active', true)->count(),
        ];

        $activeTheme = Theme::where('is_active', true)->first();
        
        $averageWishesPerUser = User::whereHas('wishes')->withCount('wishes')
            ->get()
            ->avg('wishes_count');

        $recentUserActivity = UserActivityLog::with('user')
            ->latest()
            ->limit(10)
            ->get();

        $recentAdminActivity = auth('admin')->user()
            ->activityLogs()
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact(
            'stats', 
            'activeTheme', 
            'averageWishesPerUser',
            'recentUserActivity',
            'recentAdminActivity'
        ));
    }
}