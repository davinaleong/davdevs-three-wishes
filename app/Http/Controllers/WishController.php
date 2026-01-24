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
        
        // Get all wishes for the user, grouped by theme year
        $wishes = $user->wishes()->with('theme')->get()->groupBy('theme.year')->sortKeysDesc();
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
            'wishes.*' => 'required|exists:wishes,id',
        ]);
        
        DB::transaction(function () use ($request) {
            foreach ($request->wishes as $position => $wishId) {
                $wish = Wish::where('id', $wishId)
                    ->where('user_id', Auth::id())
                    ->first();
                    
                if ($wish) {
                    $wish->update(['position' => $position + 1]); // position is 1-based
                }
            }
        });
        
        return response()->json(['success' => true]);
    }

    /**
     * Generate a wish card for printing
     */
    public function print(Request $request)
    {
        $activeTheme = ThemeService::getActiveTheme() ?: ThemeService::getCurrentYearTheme();
        
        $wishes = Wish::where('user_id', Auth::id())
            ->where('theme_id', $activeTheme->id)
            ->ordered()
            ->get();
            
        $layout = $request->get('layout', 'portrait-a4'); // Default layout
        $themeCssVariables = ThemeService::getCssVariables($activeTheme);
        
        return view('wishes.print', compact('wishes', 'activeTheme', 'layout', 'themeCssVariables'));
    }

    /**
     * Export wishes as text file
     */
    public function exportText()
    {
        $activeTheme = ThemeService::getActiveTheme() ?: ThemeService::getCurrentYearTheme();
        
        $wishes = Wish::where('user_id', Auth::id())
            ->where('theme_id', $activeTheme->id)
            ->ordered()
            ->get();

        $content = $this->generateTextExport($wishes, $activeTheme);
        
        $filename = $this->generateFilename($activeTheme, 'txt');
        
        return response($content)
            ->header('Content-Type', 'text/plain')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Export wishes as CSV file
     */
    public function exportCsv()
    {
        $activeTheme = ThemeService::getActiveTheme() ?: ThemeService::getCurrentYearTheme();
        
        $wishes = Wish::where('user_id', Auth::id())
            ->where('theme_id', $activeTheme->id)
            ->ordered()
            ->get();

        $csvData = $this->generateCsvExport($wishes, $activeTheme);
        
        $filename = $this->generateFilename($activeTheme, 'csv');
        
        return response($csvData)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Generate text content for export
     */
    private function generateTextExport($wishes, $activeTheme): string
    {
        $content = [];
        $content[] = strtoupper($activeTheme->theme_title . ' - ' . $activeTheme->year);
        $content[] = str_repeat('=', strlen($activeTheme->theme_title . ' - ' . $activeTheme->year));
        $content[] = '';
        
        if ($activeTheme->theme_verse_text) {
            $content[] = '"' . $activeTheme->theme_verse_text . '"';
            $content[] = '— ' . $activeTheme->theme_verse_reference;
            $content[] = '';
        }
        
        $content[] = 'MY WISHES:';
        $content[] = '';
        
        if ($wishes->count() > 0) {
            foreach ($wishes as $wish) {
                $content[] = $wish->position . '. ' . $wish->content;
                $content[] = '';
            }
        } else {
            $content[] = 'No wishes created yet for ' . $activeTheme->year;
            $content[] = '';
        }
        
        $content[] = 'Created with Dav/Devs Three Wishes © ' . $activeTheme->year;
        
        return implode("\n", $content);
    }

    /**
     * Generate CSV content for export
     */
    private function generateCsvExport($wishes, $activeTheme): string
    {
        $csvData = [];
        
        // Add header row
        $csvData[] = ['Position', 'Wish Content', 'Theme', 'Year', 'Created Date'];
        
        if ($wishes->count() > 0) {
            foreach ($wishes as $wish) {
                $csvData[] = [
                    $wish->position,
                    $wish->content,
                    $activeTheme->theme_title,
                    $activeTheme->year,
                    $wish->created_at->format('Y-m-d H:i:s')
                ];
            }
        }
        
        // Convert array to CSV string
        $output = fopen('php://temp', 'w');
        foreach ($csvData as $row) {
            fputcsv($output, $row);
        }
        rewind($output);
        $csvContent = stream_get_contents($output);
        fclose($output);
        
        return $csvContent;
    }

    /**
     * Generate filename for export
     */
    private function generateFilename($activeTheme, $extension): string
    {
        $themeName = preg_replace('/[^a-zA-Z0-9\s]/', '', $activeTheme->theme_title);
        $themeName = preg_replace('/\s+/', '_', trim($themeName));
        $themeName = strtolower($themeName);
        
        return "wishes_{$themeName}_{$activeTheme->year}." . $extension;
    }
}
