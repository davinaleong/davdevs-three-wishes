<?php

namespace App\Console\Commands;

use App\Mail\WelcomeEmail;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestWelcomeEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:welcome-email {user_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test sending welcome email to a user';

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
        
        $this->info("Sending welcome email to: {$user->email}");
        
        try {
            Mail::to($user->email)->send(new WelcomeEmail($user));
            $this->info("Welcome email sent successfully!");
            
            // Log the activity
            $user->logActivity('test_welcome_email_sent', [
                'sent_at' => now()->toDateTimeString(),
                'via' => 'test command'
            ]);
            
        } catch (\Exception $e) {
            $this->error("Failed to send welcome email: " . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}