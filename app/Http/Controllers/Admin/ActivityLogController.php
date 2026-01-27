<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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

    public function export(Request $request): Response
    {
        auth('admin')->user()->logActivity('ADMIN_ACTIVITY_LOGS_EXPORTED', [
            'filters' => $request->only(['action', 'admin', 'date_from', 'date_to'])
        ]);

        $query = AdminActivityLog::with('admin');

        // Apply same filters as index method
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

        $logs = $query->latest()->get();

        $filename = 'admin-activity-logs-' . now()->format('Y-m-d-H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function() use ($logs) {
            $file = fopen('php://output', 'w');
            
            // CSV Headers
            fputcsv($file, [
                'Admin Name',
                'Admin Email', 
                'Action',
                'IP Address',
                'User Agent',
                'Metadata',
                'Date/Time'
            ]);

            // CSV Data
            foreach ($logs as $log) {
                $metadata = '';
                if ($log->meta) {
                    $metaParts = [];
                    foreach ($log->meta as $key => $value) {
                        if (!in_array($key, ['ip', 'user_agent'])) {
                            $valueStr = is_array($value) || is_object($value) ? json_encode($value) : $value;
                            $metaParts[] = ucfirst(str_replace('_', ' ', $key)) . ': ' . $valueStr;
                        }
                    }
                    $metadata = implode(' | ', $metaParts);
                }

                fputcsv($file, [
                    $log->admin->name,
                    $log->admin->email,
                    $log->action,
                    $log->meta['ip'] ?? '',
                    $log->meta['user_agent'] ?? '',
                    $metadata,
                    $log->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}