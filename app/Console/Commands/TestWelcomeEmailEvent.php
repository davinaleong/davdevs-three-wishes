<?php

namespace App\Console\Commands;

use App\Listeners\SendWelcomeEmail;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Console\Command;

class TestWelcomeEmailEvent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:welcome-email-event {user_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the welcome email event listener';

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
        
        $this->info("Testing welcome email event for: {$user->email}");
        
        try {
            // Create the event
            $event = new Verified($user);
            
            // Create and call the listener directly
            $listener = new SendWelcomeEmail();
            $listener->handle($event);
            
            $this->info("Welcome email event handled successfully!");
            
        } catch (\Exception $e) {
            $this->error("Failed to handle welcome email event: " . $e->getMessage());
            $this->error("Stack trace: " . $e->getTraceAsString());
            return 1;
        }
        
        return 0;
    }
}