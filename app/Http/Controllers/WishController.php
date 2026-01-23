<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWishRequest;
use App\Http\Requests\UpdateWishRequest;
use App\Models\Wish;
use App\Services\ThemeService;
use App\Services\WishEditWindow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WishController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Check if user needs to verify email
        if (!$user->hasVerifiedEmail()) {
            return redirect()->route('verification.notice')
                ->with('message', 'Please verify your email address to start creating wishes.');
        }
        
        $activeTheme = ThemeService::getActiveTheme() ?: ThemeService::getCurrentYearTheme();
        
        $wishes = $user->wishesForTheme($activeTheme)->get();
        $canEdit = WishEditWindow::isOpen($activeTheme);
        $cutoffDescription = WishEditWindow::getClosingDescription($activeTheme);
        
        return view('wishes.index', compact('wishes', 'activeTheme', 'canEdit', 'cutoffDescription'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        
        // Check if user needs to verify email
        if (!$user->hasVerifiedEmail()) {
            return redirect()->route('verification.notice')
                ->with('message', 'Please verify your email address to start creating wishes.');
        }
        
        $activeTheme = ThemeService::getActiveTheme() ?: ThemeService::getCurrentYearTheme();
        
        if (!WishEditWindow::isOpen($activeTheme)) {
            return redirect()->route('wishes.index')
                ->with('error', 'The editing window for this year has closed.');
        }
        
        $user = Auth::user();
        $currentWishCount = $user->wishesForTheme($activeTheme)->count();
        
        if ($currentWishCount >= 10) {
            return redirect()->route('wishes.index')
                ->with('error', 'You can only have a maximum of 10 wishes.');
        }
        
        $nextPosition = $currentWishCount + 1;
        
        return view('wishes.create', compact('activeTheme', 'nextPosition'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreWishRequest $request)
    {
        $activeTheme = ThemeService::getActiveTheme() ?: ThemeService::getCurrentYearTheme();
        
        if (!WishEditWindow::isOpen($activeTheme)) {
            return redirect()->route('wishes.index')
                ->with('error', 'The editing window for this year has closed.');
        }
        
        $user = Auth::user();
        
        DB::transaction(function () use ($request, $user, $activeTheme) {
            $wish = $user->wishes()->create([
                'theme_id' => $activeTheme->id,
                'position' => $request->position,
                'content' => $request->content,
            ]);
            
            $user->logActivity('WISH_CREATED', [
                'wish_uuid' => $wish->uuid,
                'theme_year' => $activeTheme->year,
            ]);
        });
        
        return redirect()->route('wishes.index')
            ->with('success', 'Your wish has been created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Wish $wish)
    {
        $this->authorize('view', $wish);
        
        return view('wishes.show', compact('wish'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Wish $wish)
    {
        $this->authorize('update', $wish);
        
        $activeTheme = ThemeService::getActiveTheme() ?: ThemeService::getCurrentYearTheme();
        
        if (!WishEditWindow::isOpen($activeTheme)) {
            return redirect()->route('wishes.index')
                ->with('error', 'The editing window for this year has closed.');
        }
        
        return view('wishes.edit', compact('wish', 'activeTheme'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateWishRequest $request, Wish $wish)
    {
        $this->authorize('update', $wish);
        
        $activeTheme = ThemeService::getActiveTheme() ?: ThemeService::getCurrentYearTheme();
        
        if (!WishEditWindow::isOpen($activeTheme)) {
            return redirect()->route('wishes.index')
                ->with('error', 'The editing window for this year has closed.');
        }
        
        DB::transaction(function () use ($request, $wish) {
            $wish->update([
                'content' => $request->content,
                'position' => $request->position,
            ]);
            
            $wish->user->logActivity('WISH_UPDATED', [
                'wish_uuid' => $wish->uuid,
                'theme_year' => $wish->theme->year,
            ]);
        });
        
        return redirect()->route('wishes.index')
            ->with('success', 'Your wish has been updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Wish $wish)
    {
        $this->authorize('delete', $wish);
        
        $activeTheme = ThemeService::getActiveTheme() ?: ThemeService::getCurrentYearTheme();
        
        if (!WishEditWindow::isOpen($activeTheme)) {
            return redirect()->route('wishes.index')
                ->with('error', 'The editing window for this year has closed.');
        }
        
        DB::transaction(function () use ($wish) {
            $wish->user->logActivity('WISH_DELETED', [
                'wish_uuid' => $wish->uuid,
                'wish_content' => $wish->content,
                'theme_year' => $wish->theme->year,
            ]);
            
            $wish->delete();
        });
        
        return redirect()->route('wishes.index')
            ->with('success', 'Your wish has been deleted successfully!');
    }
    
    /**
     * Reorder wishes
     */
    public function reorder(Request $request)
    {
        $activeTheme = ThemeService::getActiveTheme() ?: ThemeService::getCurrentYearTheme();
        
        if (!WishEditWindow::isOpen($activeTheme)) {
            return response()->json(['error' => 'Editing window closed'], 403);
        }
        
        $request->validate([
            'wishes' => 'required|array',
            'wishes.*.id' => 'required|exists:wishes,uuid',
            'wishes.*.position' => 'required|integer|min:1|max:10',
        ]);
        
        DB::transaction(function () use ($request) {
            foreach ($request->wishes as $wishData) {
                $wish = Wish::where('uuid', $wishData['id'])
                    ->where('user_id', Auth::id())
                    ->first();
                    
                if ($wish) {
                    $wish->update(['position' => $wishData['position']]);
                }
            }
        });
        
        return response()->json(['success' => true]);
    }
}
