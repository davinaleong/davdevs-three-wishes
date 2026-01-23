<?php

namespace App\Console\Commands;

use App\Mail\AnnualWishReminder;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendAnnualWishEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wishes:send-annual-emails 
                            {--dry-run : Run without actually sending emails}
                            {--user= : Send to specific user UUID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send annual wish reminder emails to all users on December 31st';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $isDryRun = $this->option('dry-run');
        $specificUser = $this->option('user');

        $this->info('Starting annual wish email process...');
        
        if ($isDryRun) {
            $this->warn('DRY RUN MODE - No emails will actually be sent');
        }

        // Get users to email
        $usersQuery = User::whereNotNull('email_verified_at');
        
        if ($specificUser) {
            $usersQuery->where('uuid', $specificUser);
        }
        
        $users = $usersQuery->with('wishes.theme')->get();
        
        $this->info("Found {$users->count()} users to email");

        if ($users->isEmpty()) {
            $this->warn('No users found to email');
            return self::SUCCESS;
        }

        $successCount = 0;
        $failureCount = 0;

        foreach ($users as $user) {
            try {
                $this->line("Processing user: {$user->name} ({$user->email})");
                
                // Check if user has any wishes
                if ($user->wishes->isEmpty()) {
                    $this->comment("  - User has no wishes, skipping");
                    continue;
                }

                if (!$isDryRun) {
                    // Send the email
                    Mail::to($user->email)->send(new AnnualWishReminder($user));
                    
                    // Log the activity
                    $user->logActivity('annual_wish_email_sent', [
                        'year' => now()->year,
                        'wishes_count' => $user->wishes->count(),
                        'sent_at' => now()->toDateTimeString(),
                    ]);
                    
                    Log::info("Annual wish email sent to user {$user->id}", [
                        'user_uuid' => $user->uuid,
                        'email' => $user->email,
                        'wishes_count' => $user->wishes->count(),
                    ]);
                }
                
                $this->info("  ✅ Email " . ($isDryRun ? 'would be sent' : 'sent') . " to {$user->email}");
                $successCount++;
                
            } catch (\Exception $e) {
                $this->error("  ❌ Failed to send email to {$user->email}: " . $e->getMessage());
                
                Log::error("Failed to send annual wish email", [
                    'user_uuid' => $user->uuid,
                    'email' => $user->email,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                
                $failureCount++;
            }
        }

        $this->newLine();
        $this->info("Annual wish email process completed!");
        $this->table(['Status', 'Count'], [
            ['Success', $successCount],
            ['Failures', $failureCount],
            ['Total', $successCount + $failureCount],
        ]);

        if ($isDryRun) {
            $this->warn('This was a dry run - no actual emails were sent');
        }

        return $failureCount > 0 ? self::FAILURE : self::SUCCESS;
    }
}