<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StudentWelcomeEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $studentName;
    public $registrationNumber;
    public $temporaryPassword;

    /**
     * Create a new message instance.
     */
    public function __construct($studentName, $registrationNumber, $temporaryPassword)
    {
        $this->studentName = $studentName;
        $this->registrationNumber = $registrationNumber;
        $this->temporaryPassword = $temporaryPassword;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to The Neotia University - Your Login Credentials',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.students.welcome',
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
