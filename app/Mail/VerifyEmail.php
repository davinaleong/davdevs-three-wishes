<?php

namespace App\Mail;

use App\Models\Theme;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VerifyEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public User $user;
    public $year;
    public $yearTheme;
    public $verificationUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, string $verificationUrl)
    {
        $this->user = $user;
        $this->verificationUrl = $verificationUrl;
        $this->year = date('Y');
        $this->yearTheme = Theme::where('year', $this->year)->first();
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "🙏 Verify Your Email - Welcome to Dav/Devs Three Wishes {$this->year}!"
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.verify-email'
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