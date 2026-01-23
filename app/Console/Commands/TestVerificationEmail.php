<?php

namespace App\Console\Commands;

use App\Mail\VerifyEmail;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon;

class TestVerificationEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:verification-email {user_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test sending verification email to a user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('user_id') ?? 1;
        
        $user = User::find($userId);
        
        if (!$user) {
            $this->error("User with ID {$userId} not found");
            return 1;
        }
        
        $this->info("Sending verification email to: {$user->email}");
        
        try {
            // Create a verification URL
            $verificationUrl = URL::temporarySignedRoute(
                'verification.verify',
                Carbon::now()->addMinutes(config('auth.verification.expire', 60)),
                [
                    'id' => $user->getKey(),
                    'hash' => sha1($user->getEmailForVerification()),
                ]
            );
            
            Mail::to($user->email)->send(new VerifyEmail($user, $verificationUrl));
            $this->info("Verification email sent successfully!");
            
            // Log the activity
            $user->logActivity('test_verification_email_sent', [
                'sent_at' => now()->toDateTimeString(),
                'via' => 'test command'
            ]);
            
        } catch (\Exception $e) {
            $this->error("Failed to send verification email: " . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}