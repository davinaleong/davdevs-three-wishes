<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Theme;
use App\Models\Wish;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        // Log dashboard access
        auth('admin')->user()->logActivity('ADMIN_DASHBOARD_VIEWED');

        $stats = [
            'total_wishes' => Wish::count(),
            'active_themes' => Theme::where('is_active', true)->count(),
        ];

        $activeTheme = Theme::where('is_active', true)->first();

        $recentAdminActivity = auth('admin')->user()
            ->activityLogs()
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact(
            'stats', 
            'activeTheme',
            'recentAdminActivity'
        ));
    }
}