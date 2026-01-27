<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActivityLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    public function index(Request $request): View
    {
        auth('admin')->user()->logActivity('ADMIN_ACTIVITY_LOGS_VIEWED');

        $query = AdminActivityLog::with('admin');

        if ($request->filled('action')) {
            $query->where('action', $request->get('action'));
        }

        if ($request->filled('admin')) {
            $query->whereHas('admin', function($q) use ($request) {
                $q->where('email', 'like', '%' . $request->get('admin') . '%');
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->get('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->get('date_to'));
        }

        $logs = $query->latest()->paginate(50)->withQueryString();
        $actions = AdminActivityLog::distinct()->pluck('action');

        return view('admin.activity-logs.index', compact('logs', 'actions'));
    }
}