<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LoginLink extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(protected string $url)
    {
        //
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: "Log in to " . config("app.name"));
    }

    public function content(): Content
    {
        $recipient = $this->to[0];
        return new Content(
            markdown: "mail.login-link",
            with: ["url" => $this->url, "email" => $recipient["address"]]
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
