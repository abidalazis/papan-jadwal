<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReminderEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $undangan; // <-- tambahkan properti publik

    /**
     * Create a new message instance.
     */
    public function __construct($undangan)
    {
        $this->undangan = $undangan; // simpan data ke variabel
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '📅 Pengingat Undangan untuk Besok',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.reminder', // pastikan ini sesuai dengan nama file view kamu
            with: [
                'undangan' => $this->undangan, // <-- kirim variabel ke view
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
