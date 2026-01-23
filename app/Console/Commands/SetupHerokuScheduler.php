<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SetupHerokuScheduler extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'heroku:setup-scheduler';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Display instructions for setting up Heroku Scheduler for annual wish emails';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('=== Heroku Scheduler Setup Instructions ===');
        $this->newLine();
        
        $this->info('1. Install the Heroku Scheduler add-on:');
        $this->line('   heroku addons:create scheduler:standard');
        $this->newLine();
        
        $this->info('2. Open the Heroku Scheduler dashboard:');
        $this->line('   heroku addons:open scheduler');
        $this->newLine();
        
        $this->info('3. Add a new job with these settings:');
        $this->line('   Command: php artisan wishes:send-annual-emails');
        $this->line('   Frequency: Yearly (you\'ll need to manually set this to run on December 31st)');
        $this->line('   Next Due: December 31st, 10:00 AM UTC');
        $this->newLine();
        
        $this->warn('Note: Heroku Scheduler doesn\'t have a built-in "yearly" option.');
        $this->warn('You may need to:');
        $this->line('- Set it to run daily and modify the command to check the date');
        $this->line('- Or manually schedule it each year');
        $this->newLine();
        
        $this->info('4. Alternative: Create a daily job that checks the date:');
        $this->line('   Command: php artisan wishes:send-annual-emails-if-december-31st');
        $this->line('   Frequency: Daily at 10:00 AM UTC');
        $this->newLine();
        
        $this->info('5. Test the setup:');
        $this->line('   heroku run php artisan wishes:send-annual-emails --dry-run');
        $this->newLine();
        
        $this->info('=== Email Configuration ===');
        $this->line('Make sure these environment variables are set on Heroku:');
        $this->line('- MAIL_MAILER (e.g., smtp, mailgun, ses)');
        $this->line('- MAIL_HOST (if using SMTP)');
        $this->line('- MAIL_PORT (if using SMTP)');
        $this->line('- MAIL_USERNAME');
        $this->line('- MAIL_PASSWORD');
        $this->line('- MAIL_FROM_ADDRESS');
        $this->line('- MAIL_FROM_NAME');
        
        return self::SUCCESS;
    }
}