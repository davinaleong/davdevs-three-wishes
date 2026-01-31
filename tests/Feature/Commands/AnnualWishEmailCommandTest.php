<?php

namespace Tests\Feature\Commands;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnnualWishEmailCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_annual_wish_email_command_is_disabled()
    {
        $this->artisan('wishes:send-annual-emails')
            ->expectsOutput('Annual wish email functionality has been disabled for privacy compliance.')
            ->assertExitCode(1);
    }

    public function test_annual_wish_email_december_31st_command_is_disabled()
    {
        $this->artisan('wishes:send-annual-emails-if-december-31st')
            ->assertExitCode(1);
    }
}