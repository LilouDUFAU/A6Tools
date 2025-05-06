<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .header {
            border-bottom: 2px solid #16A34A;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .title {
            font-size: 24px;
            font-weight: bold;
            color: #15803D;
        }
        .subtitle {
            font-size: 14px;
            color: #666;
        }
        .credentials-box {
            background: #f3f4f6;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .label {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
        }
        .value {
            font-size: 16px;
            font-weight: bold;
            margin: 5px 0 15px;
        }
        .warning {
            background: #D1FAE5;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            font-size: 14px;
        }
        .footer {
            margin-top: 40px;
            font-size: 12px;
            color: #666;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">Identifiants de Connexion</div>
        <div class="subtitle">Information confidentielle</div>
    </div>

    <p>Bienvenue, {{ $user->prenom }} {{ $user->nom }}</p>
    <p>Votre compte a été créé le {{ now()->format('d/m/Y') }}. Voici vos identifiants pour accéder à l'application.</p>

    <div class="credentials-box">
        <div class="label">ADRESSE EMAIL</div>
        <div class="value">{{ $user->email }}</div>

        <div class="label">MOT DE PASSE</div>
        <div class="value">{{ $password }}</div>
    </div>

    <div class="warning">
        <strong>Important :</strong><br>
        Pour des raisons de sécurité, nous vous recommandons de ne jamais donner vos identifiants à un collègue.
    </div>

    <div class="footer">
        <p>Ce document est confidentiel et contient des informations d'accès personnelles.</p>
        <p>© {{ date('Y') }} {{ config('app.name') }}</p>
    </div>
</body>
</html>
