<?php

namespace App\Services;

use App\Enums\PaymentProvider;
use App\Enums\PaymentStatus;
use App\Enums\TransactionType;
use App\Jobs\GenerateInvoiceJob;
use App\Models\Conversation;
use App\Models\Expert;
use App\Models\Payment;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\StripeClient;

class PaymentService
{
    private const PLATFORM_COMMISSION = 0.20;

    private StripeClient $stripe;

    public function __construct()
    {
        $this->stripe = new StripeClient(config('services.stripe.secret'));
    }

    /**
     * Create a Stripe PaymentIntent and a pending Payment record.
     */
    public function createIntent(User $user, Expert $expert, Conversation $conversation): array
    {
        $amount = (int) ($expert->hourly_rate * 100);

        $intent = $this->stripe->paymentIntents->create([
            'amount'   => $amount,
            'currency' => 'mad',
            'metadata' => [
                'user_id'         => $user->id,
                'expert_id'       => $expert->id,
                'conversation_id' => $conversation->id,
            ],
        ]);

        Payment::create([
            'user_id'                  => $user->id,
            'expert_id'                => $expert->id,
            'conversation_id'          => $conversation->id,
            'amount'                   => $expert->hourly_rate,
            'currency'                 => 'MAD',
            'status'                   => PaymentStatus::Pending,
            'provider'                 => PaymentProvider::Stripe,
            'stripe_payment_intent_id' => $intent->id,
        ]);

        return ['client_secret' => $intent->client_secret];
    }

    /**
     * Confirm a payment after successful Stripe charge.
     */
    public function confirm(string $paymentIntentId): Payment
    {
        $intent  = $this->stripe->paymentIntents->retrieve($paymentIntentId);
        $payment = Payment::where('stripe_payment_intent_id', $paymentIntentId)->firstOrFail();

        if ($intent->status === 'succeeded') {
            $payment->update([
                'status'           => PaymentStatus::Completed,
                'stripe_charge_id' => $intent->latest_charge,
                'paid_at'          => now(),
            ]);

            $this->creditExpertWallet($payment);
            GenerateInvoiceJob::dispatch($payment);
        } else {
            $payment->update(['status' => PaymentStatus::Failed]);
        }

        return $payment->fresh();
    }

    /**
     * Handle Stripe webhook events.
     */
    public function handleWebhook(string $payload, string $signature): void
    {
        $event = \Stripe\Webhook::constructEvent(
            $payload,
            $signature,
            config('services.stripe.webhook_secret')
        );

        match ($event->type) {
            'payment_intent.succeeded' => $this->confirm($event->data->object->id),
            'payment_intent.payment_failed' => $this->markFailed($event->data->object->id),
            default => null,
        };
    }

    /**
     * Initiate a CMI payment — returns form fields for redirect.
     */
    public function initiateCmi(User $user, Expert $expert, Conversation $conversation): array
    {
        $orderId = 'NX-' . strtoupper(substr(uniqid(), -8));
        $amount  = number_format($expert->hourly_rate, 2, '.', '');

        Payment::create([
            'user_id'         => $user->id,
            'expert_id'       => $expert->id,
            'conversation_id' => $conversation->id,
            'amount'          => $expert->hourly_rate,
            'currency'        => 'MAD',
            'status'          => PaymentStatus::Pending,
            'provider'        => PaymentProvider::Cmi,
            'cmi_order_id'    => $orderId,
        ]);

        $params = [
            'clientid'    => config('services.cmi.merchant_id'),
            'amount'      => $amount,
            'oid'         => $orderId,
            'okUrl'       => config('app.frontend_url') . '/payment/success',
            'failUrl'     => config('app.frontend_url') . '/payment/fail',
            'callbackUrl' => config('app.url') . '/api/v1/payments/cmi/callback',
            'currency'    => '504',
            'lang'        => 'fr',
            'rnd'         => time(),
            'storetype'   => '3D_PAY_HOSTING',
        ];

        $hashStr = implode('|', array_values($params));
        $params['hash'] = base64_encode(hash_hmac('sha512', $hashStr, config('services.cmi.store_key'), true));

        return [
            'cmi_url' => config('services.cmi.base_url'),
            'params'  => $params,
        ];
    }

    /**
     * Handle CMI server-to-server callback.
     */
    public function handleCmiCallback(array $data): string
    {
        $payment = Payment::where('cmi_order_id', $data['oid'] ?? '')->first();

        if (! $payment) {
            return 'ACTION=FAILURE';
        }

        $expectedHash = base64_encode(hash_hmac(
            'sha512',
            implode('|', [
                $data['clientid'] ?? '',
                $data['oid'] ?? '',
                $data['AuthCode'] ?? '',
                $data['ProcReturnCode'] ?? '',
                $data['Response'] ?? '',
                $data['mdStatus'] ?? '',
            ]),
            config('services.cmi.store_key'),
            true
        ));

        if (($data['HASH'] ?? '') !== $expectedHash) {
            Log::warning('CMI callback hash mismatch', ['oid' => $data['oid']]);
            return 'ACTION=FAILURE';
        }

        if (($data['ProcReturnCode'] ?? '') === '00') {
            $payment->update([
                'status'  => PaymentStatus::Completed,
                'paid_at' => now(),
            ]);
            $this->creditExpertWallet($payment);
            GenerateInvoiceJob::dispatch($payment);

            return 'ACTION=POSTAUTH';
        }

        $payment->update(['status' => PaymentStatus::Failed]);
        return 'ACTION=FAILURE';
    }

    /**
     * Credit 80% of payment to expert wallet (20% platform commission).
     */
    public function creditExpertWallet(Payment $payment): void
    {
        DB::transaction(function () use ($payment) {
            $expertShare = round($payment->amount * (1 - self::PLATFORM_COMMISSION), 2);

            $wallet = Wallet::firstOrCreate(
                ['expert_id' => $payment->expert_id],
                ['balance' => 0, 'total_earned' => 0, 'total_withdrawn' => 0]
            );

            $wallet->increment('balance', $expertShare);
            $wallet->increment('total_earned', $expertShare);

            WalletTransaction::create([
                'wallet_id'   => $wallet->id,
                'type'        => TransactionType::Credit,
                'amount'      => $expertShare,
                'description' => "Paiement conversation #{$payment->conversation_id}",
                'reference'   => $payment->stripe_payment_intent_id ?? $payment->cmi_order_id,
            ]);
        });
    }

    /**
     * Get paginated payment history for a user.
     */
    public function history(User $user): LengthAwarePaginator
    {
        return Payment::where('user_id', $user->id)
            ->with(['expert.user', 'conversation'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
    }

    private function markFailed(string $paymentIntentId): void
    {
        Payment::where('stripe_payment_intent_id', $paymentIntentId)
            ->update(['status' => PaymentStatus::Failed]);
    }
}
