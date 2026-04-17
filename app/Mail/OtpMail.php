<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public array $data = []
    ) {}

    public function envelope(): Envelope
    {
        $subject = $this->data['type'] === 'password_reset'
            ? 'Réinitialisation de votre mot de passe — Coopiyo'
            : 'Vérifiez votre adresse email — Coopiyo';

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(view: 'mail.otp');
    }
}
