<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AttendanceNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $type;
    public $status;
    public $time;

    /**
     * Create a new message instance.
     */
    public function __construct($user, $type, $status)
    {
        $this->user = $user;
        $this->type = $type;
        $this->status = $status;
        $this->time = now()->format('H:i:s');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->type === 'check_in' ? 'Check-in Berhasil' : 'Check-out Berhasil';
        
        return new Envelope(
            subject: $subject . ' - PT Arung Laut Nusantara',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.attendance-notification',
            with: [
                'userName' => $this->user->name,
                'type' => $this->type,
                'status' => $this->status,
                'time' => $this->time,
                'date' => now()->format('d F Y'),
            ],
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