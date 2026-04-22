<?php

namespace App\Jobs;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class GenerateInvoiceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 10;

    public function __construct(private Payment $payment) {}

    public function handle(): void
    {
        try {
            $payment = $this->payment->load(['user', 'expert.user', 'conversation']);

            $pdf = app('dompdf.wrapper');
            $pdf->loadView('invoices.payment', ['payment' => $payment]);

            $path    = "invoices/payment-{$payment->id}.pdf";
            $content = $pdf->output();

            Storage::disk('s3')->put($path, $content);

            Log::info("Invoice generated for payment #{$payment->id}");
        } catch (\Throwable $e) {
            Log::error('GenerateInvoiceJob failed', [
                'payment_id' => $this->payment->id,
                'error'      => $e->getMessage(),
            ]);
        }
    }
}
