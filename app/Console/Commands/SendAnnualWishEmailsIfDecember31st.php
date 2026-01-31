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
        $this->error('Annual wish email functionality has been disabled for privacy compliance.');
        return self::FAILURE;
    }
}