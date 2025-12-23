<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LeaveRequestNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $leaveRequest;
    public $status;

    public function __construct($leaveRequest, $status)
    {
        $this->leaveRequest = $leaveRequest;
        $this->status = $status;
    }

    public function envelope(): Envelope
    {
        $subject = $this->status === 'approved' 
            ? 'Pengajuan Cuti Disetujui' 
            : 'Pengajuan Cuti Ditolak';
        
        return new Envelope(
            subject: $subject . ' - PT Arung Laut Nusantara',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.leave-request-notification',
            with: [
                'leaveRequest' => $this->leaveRequest,
                'status' => $this->status,
            ],  
        );
    }

    public function attachments(): array
    {
        return [];
    }
}