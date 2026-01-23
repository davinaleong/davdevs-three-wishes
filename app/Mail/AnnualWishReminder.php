<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Theme;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AnnualWishReminder extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $user;
    public $wishes;
    public $year;
    public $yearTheme;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
        $this->wishes = $user->wishes()->with('theme')->get();
        $this->year = now()->year;
        $this->yearTheme = Theme::getThemeForYear($this->year);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Your {$this->year} Three Wishes - God's Faithfulness & New Hopes!",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.annual-wish-reminder',
            with: [
                'user' => $this->user,
                'wishes' => $this->wishes,
                'year' => $this->year,
                'yearTheme' => $this->yearTheme,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}