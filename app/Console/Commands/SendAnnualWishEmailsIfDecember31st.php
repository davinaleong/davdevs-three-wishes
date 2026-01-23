<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class SendAnnualWishEmailsIfDecember31st extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wishes:send-annual-emails-if-december-31st';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send annual wish emails if today is December 31st (for daily Heroku scheduler)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $today = now();
        
        // Check if today is December 31st
        if ($today->month !== 12 || $today->day !== 31) {
            $this->info("Today is {$today->format('F j, Y')} - not December 31st. Skipping annual wish emails.");
            return self::SUCCESS;
        }
        
        $this->info("It's December 31st, {$today->year}! Sending annual wish emails...");
        
        // Call the main command
        $exitCode = Artisan::call('wishes:send-annual-emails');
        
        if ($exitCode === 0) {
            $this->info('Annual wish emails sent successfully!');
        } else {
            $this->error('There was an error sending annual wish emails.');
        }
        
        return $exitCode;
    }
}