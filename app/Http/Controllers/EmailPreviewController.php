<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Theme;
use App\Models\Wish;
use App\Mail\WelcomeEmail;
use App\Mail\VerifyEmail;
use App\Mail\AnnualWishReminder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EmailPreviewController extends Controller
{
    public function index()
    {
        $emails = [
            'welcome' => 'Welcome Email',
            'verify' => 'Email Verification',
            'annual-reminder' => 'Annual Wish Reminder',
        ];

        return view('email-preview.index', compact('emails'));
    }

    public function preview(Request $request, string $template)
    {
        // Get or create test user
        $user = $this->getTestUser();
        
        // Get current theme or create test theme
        $theme = $this->getTestTheme();
        
        switch ($template) {
            case 'welcome':
                return $this->previewWelcomeEmail($user, $theme);
                
            case 'verify':
                return $this->previewVerifyEmail($user);
                
            case 'annual-reminder':
                return $this->previewAnnualReminderEmail($user, $theme);
                
            default:
                abort(404, 'Email template not found');
        }
    }

    private function previewWelcomeEmail(User $user, Theme $theme)
    {
        $mailable = new WelcomeEmail($user, $theme);
        return $mailable->render();
    }

    private function previewVerifyEmail(User $user)
    {
        $dummyVerificationUrl = url('/email-verified?dummy=true&expires=' . time() . '&signature=dummy_signature');
        $mailable = new VerifyEmail($user, $dummyVerificationUrl);
        return $mailable->render();
    }

    private function previewAnnualReminderEmail(User $user, Theme $theme)
    {
        // Create test wishes
        $wishes = collect([
            (object) [
                'id' => 1,
                'wish_text' => 'To grow deeper in my relationship with God through daily prayer and Bible study',
                'created_at' => now()->subMonths(6),
            ],
            (object) [
                'id' => 2,
                'wish_text' => 'To find a meaningful career that allows me to serve others and use my talents for His glory',
                'created_at' => now()->subMonths(4),
            ],
            (object) [
                'id' => 3,
                'wish_text' => 'To build stronger relationships with my family and friends, showing Christ\'s love in all my interactions',
                'created_at' => now()->subMonths(2),
            ],
        ]);

        $mailable = new AnnualWishReminder($user, $wishes, $theme);
        return $mailable->render();
    }

    private function getTestUser(): User
    {
        $testUser = new User();
        $testUser->id = 1;
        $testUser->name = 'John Doe';
        $testUser->email = 'john.doe@example.com';
        $testUser->email_verified_at = now();
        $testUser->created_at = now()->subYear();
        
        return $testUser;
    }

    private function getTestTheme(): Theme
    {
        // Try to get current active theme
        $currentYear = date('Y');
        $theme = Theme::where('year', $currentYear)->where('is_active', true)->first();
        
        if (!$theme) {
            $theme = Theme::where('year', $currentYear)->first();
        }
        
        // If still no theme, create a test theme
        if (!$theme) {
            $theme = new Theme();
            $theme->id = 1;
            $theme->year = $currentYear;
            $theme->theme_title = 'Test Theme';
            $theme->theme_tagline = 'A year of testing and growth';
            $theme->theme_verse_reference = 'Jeremiah 29:11';
            $theme->theme_verse_text = 'For I know the plans I have for you," declares the Lord, "plans to prosper you and not to harm you, to give you hope and a future.';
            $theme->colors_json = [
                'primary' => '#2b7fff',
                'accent' => '#1447e6',
                'background' => '#ffffff',
                'text' => '#333333',
                'muted' => '#7f8c8d'
            ];
            $theme->is_active = true;
        }
        
        return $theme;
    }
}