<?php

namespace App\Listeners;

use App\Mail\WelcomeEmail;
use Illuminate\Auth\Events\Verified;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendWelcomeEmail implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Verified $event): void
    {
        // Send welcome email after successful email verification
        Mail::to($event->user->email)->send(new WelcomeEmail($event->user));
        
        // Log the welcome email activity
        $event->user->logActivity('welcome_email_sent', [
            'sent_at' => now()->toDateTimeString(),
        ]);
    }
}