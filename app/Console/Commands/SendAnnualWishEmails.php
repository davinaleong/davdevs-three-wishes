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
        $this->error('Annual wish email functionality has been disabled for privacy compliance.');
        return Command::FAILURE;
    }
}