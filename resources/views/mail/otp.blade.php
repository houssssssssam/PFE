<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
        .container { max-width: 480px; margin: 40px auto; background: #fff; border-radius: 8px; padding: 32px; }
        .logo { font-size: 24px; font-weight: bold; color: #4F46E5; margin-bottom: 24px; }
        .code { font-size: 40px; font-weight: bold; letter-spacing: 12px; color: #111; text-align: center;
                background: #F3F4F6; border-radius: 8px; padding: 16px; margin: 24px 0; }
        .footer { color: #9CA3AF; font-size: 12px; margin-top: 24px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">Nexora</div>

        <p>Bonjour <strong>{{ $user->name }}</strong>,</p>

        @if($data['type'] === 'password_reset')
            <p>Vous avez demandé la réinitialisation de votre mot de passe. Utilisez le code ci-dessous :</p>
        @else
            <p>Merci de vous être inscrit. Vérifiez votre adresse email avec le code ci-dessous :</p>
        @endif

        <div class="code">{{ $data['otp'] }}</div>

        <p>Ce code expire dans <strong>10 minutes</strong>.</p>
        <p>Si vous n'avez pas effectué cette action, ignorez cet email.</p>

        <div class="footer">© {{ date('Y') }} Nexora. Tous droits réservés.</div>
    </div>
</body>
</html>
