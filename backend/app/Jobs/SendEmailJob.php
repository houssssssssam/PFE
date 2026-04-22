<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(
        private string $template,
        private User $user,
        private array $data = []
    ) {}

    public function handle(): void
    {
        match ($this->template) {
            'otp' => Mail::to($this->user->email)->send(
                new \App\Mail\OtpMail($this->user, $this->data)
            ),
            default => null,
        };
    }
}
