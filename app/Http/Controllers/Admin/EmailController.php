<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Theme;
use Illuminate\View\View;

class EmailController extends Controller
{
    public function index(): View
    {
        auth('admin')->user()->logActivity('ADMIN_EMAIL_TOOLS_VIEWED');

        $themes = Theme::orderBy('year', 'desc')->get();

        return view('admin.emails.index', compact('themes'));
    }
}