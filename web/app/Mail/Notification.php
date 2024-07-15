<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class Notification extends Mailable
{
    use Queueable, SerializesModels;

    public $mailData;

    /**
     * Create a new message instance.
     */
    public function __construct(array $mailData)
    {
        $this->mailData = $mailData;
        $this->validate();
    }

    public function validate()
    {
        if (! isset($this->mailData['title']) || empty($this->mailData['title'])) {
            $this->mailData['title'] = 'Notification';
        }
        if (! isset($this->mailData['subject']) || empty($this->mailData['subject'])) {
            $this->mailData['subject'] = env('APP_NAME').' Notification';
        }
        if (! isset($this->mailData['body']) || empty($this->mailData['body'])) {
            $this->mailData['body'] = 'Hei, this is sample email to you.';
        }
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->mailData['subject'],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'Mail.notification',
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
