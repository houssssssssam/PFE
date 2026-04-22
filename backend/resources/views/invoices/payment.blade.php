<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #1a1a2e; font-size: 14px; margin: 0; padding: 40px; }
        .header { display: flex; justify-content: space-between; border-bottom: 3px solid #6c63ff; padding-bottom: 20px; margin-bottom: 30px; }
        .logo { font-size: 28px; font-weight: 700; color: #6c63ff; }
        .invoice-title { font-size: 22px; font-weight: 700; text-align: right; }
        .invoice-number { color: #666; font-size: 13px; }
        .section { margin-bottom: 25px; }
        .section-title { font-size: 12px; text-transform: uppercase; letter-spacing: 1px; color: #999; margin-bottom: 8px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #f5f4ff; padding: 10px 14px; text-align: left; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; color: #666; }
        td { padding: 12px 14px; border-bottom: 1px solid #eee; }
        .total-row td { font-weight: 700; font-size: 16px; color: #6c63ff; border-top: 2px solid #6c63ff; border-bottom: none; }
        .footer { margin-top: 50px; font-size: 11px; color: #999; text-align: center; }
        .badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; background: #d4edda; color: #155724; }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <div class="logo">NEXORA</div>
            <div style="color: #666; font-size: 12px; margin-top: 4px;">Plateforme d'expertise en ligne</div>
        </div>
        <div>
            <div class="invoice-title">FACTURE</div>
            <div class="invoice-number">#{{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}</div>
            <div style="color: #666; font-size: 12px; margin-top: 4px;">{{ $payment->paid_at?->format('d/m/Y') }}</div>
        </div>
    </div>

    <div style="display: flex; gap: 40px; margin-bottom: 30px;">
        <div style="flex: 1;">
            <div class="section-title">Facturé à</div>
            <div style="font-weight: 600;">{{ $payment->user?->name }}</div>
            <div style="color: #666;">{{ $payment->user?->email }}</div>
        </div>
        <div style="flex: 1;">
            <div class="section-title">Prestataire</div>
            <div style="font-weight: 600;">{{ $payment->expert?->user?->name }}</div>
            <div style="color: #666;">Expert Nexora</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th>Référence</th>
                <th>Statut</th>
                <th style="text-align: right;">Montant</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Consultation — Conversation #{{ $payment->conversation_id }}</td>
                <td style="color: #666; font-size: 12px;">{{ $payment->stripe_payment_intent_id ?? $payment->cmi_order_id }}</td>
                <td><span class="badge">Payé</span></td>
                <td style="text-align: right; font-weight: 600;">{{ number_format($payment->amount, 2) }} {{ $payment->currency }}</td>
            </tr>
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="3">Total</td>
                <td style="text-align: right;">{{ number_format($payment->amount, 2) }} {{ $payment->currency }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        Nexora — Plateforme SaaS d'expertise en ligne &bull; noreply@nexora.ma<br>
        Ce document est généré automatiquement et constitue votre reçu de paiement.
    </div>
</body>
</html>
