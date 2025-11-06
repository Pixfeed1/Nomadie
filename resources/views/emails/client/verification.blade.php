<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérifiez votre adresse email - {{ config('app.name') }}</title>
    <style>
        /* Styles de base pour l'email */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333333;
            background-color: #f7f7f7;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }
        .header {
            background: linear-gradient(135deg, #38B2AC 0%, #2C9A94 100%);
            color: #ffffff;
            padding: 32px 24px;
            text-align: center;
        }
        .content {
            padding: 32px 24px;
        }
        .footer {
            background-color: #f7fafc;
            padding: 20px 24px;
            text-align: center;
            font-size: 12px;
            color: #718096;
            border-top: 1px solid #e2e8f0;
        }
        .logo {
            margin-bottom: 16px;
            max-width: 200px;
        }
        .btn {
            display: inline-block;
            background-color: #38B2AC;
            color: #ffffff;
            text-decoration: none;
            padding: 14px 28px;
            border-radius: 8px;
            font-weight: 600;
            margin: 20px 0;
            font-size: 16px;
        }
        .btn:hover {
            background-color: #2C9A94;
        }
        .warning-box {
            background-color: #fff5f5;
            border-left: 4px solid #f56565;
            padding: 20px;
            margin: 24px 0;
            border-radius: 0 8px 8px 0;
        }
        .info-box {
            background-color: #f0f9ff;
            border-left: 4px solid #38B2AC;
            padding: 20px;
            margin: 24px 0;
            border-radius: 0 8px 8px 0;
        }
        h1 {
            color: #ffffff;
            font-size: 28px;
            margin: 0;
            font-weight: 700;
        }
        h2 {
            color: #2d3748;
            font-size: 22px;
            margin-top: 0;
            margin-bottom: 16px;
        }
        h3 {
            color: #2d3748;
            font-size: 18px;
            margin-top: 24px;
            margin-bottom: 12px;
        }
        p {
            margin: 16px 0;
            font-size: 16px;
        }
        .text-small {
            font-size: 14px;
            color: #718096;
        }
        .highlight {
            color: #38B2AC;
            font-weight: 600;
        }
        .divider {
            height: 1px;
            background-color: #e2e8f0;
            margin: 32px 0;
        }
        .url-box {
            background-color: #f7fafc;
            padding: 16px;
            border-radius: 8px;
            margin: 16px 0;
            word-break: break-all;
            border: 1px solid #e2e8f0;
        }
        .step-number {
            display: inline-block;
            width: 32px;
            height: 32px;
            background-color: #38B2AC;
            color: #ffffff;
            text-align: center;
            line-height: 32px;
            border-radius: 50%;
            font-weight: bold;
            margin-right: 12px;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            background-color: #fef3c7;
            color: #92400e;
        }
        ul {
            padding-left: 20px;
        }
        li {
            margin: 8px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            @if(config('app.logo'))
                <img src="{{ config('app.logo') }}" alt="{{ config('app.name') }}" class="logo">
            @else
                <h1>{{ config('app.name') }}</h1>
            @endif
        </div>

        <div class="content">
            <div class="info-box">
                <h2 style="color: #38B2AC; margin-top: 0;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline-block; vertical-align: middle; margin-right: 8px;">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                        <polyline points="22,6 12,13 2,6"></polyline>
                    </svg>
                    Vérification de votre adresse email
                </h2>
                <p style="margin-bottom: 0;">Dernière étape pour activer votre compte - <span class="status-badge">Action requise</span></p>
            </div>

            <p>Bonjour <span class="highlight">{{ $user->firstname }} {{ $user->lastname }}</span>,</p>

            <p>Merci de vous être inscrit sur <strong>{{ config('app.name') }}</strong> ! Pour finaliser votre inscription et accéder à votre compte, nous devons vérifier votre adresse email.</p>

            <p><strong>C'est très simple et rapide :</strong></p>

            <div style="text-align: center;">
                <a href="{{ $verificationUrl }}" class="btn">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline-block; vertical-align: middle; margin-right: 8px;">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    Vérifier mon adresse email
                </a>
            </div>

            <p class="text-small" style="text-align: center;">Ou copiez et collez ce lien dans votre navigateur :</p>
            <div class="url-box">
                <code style="font-size: 13px; color: #2d3748;">{{ $verificationUrl }}</code>
            </div>

            <div class="warning-box">
                <h3 style="color: #c53030; margin-top: 0;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline-block; vertical-align: middle; margin-right: 8px;">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                    Important
                </h3>
                <ul style="margin-bottom: 0;">
                    <li>Ce lien de vérification expirera dans <strong>48 heures</strong></li>
                    <li>Si le lien a expiré, vous pourrez en demander un nouveau depuis la page de connexion</li>
                    <li>Votre compte ne sera actif qu'après la vérification de votre email</li>
                </ul>
            </div>

            <div class="divider"></div>

            <h3>Pourquoi vérifier votre email ?</h3>
            <p>La vérification de votre adresse email nous permet de :</p>
            <ul>
                <li><strong>Sécuriser votre compte</strong> - Protéger vos données personnelles</li>
                <li><strong>Garantir la communication</strong> - Vous envoyer vos confirmations de réservation</li>
                <li><strong>Prévenir la fraude</strong> - Maintenir une plateforme sûre pour tous</li>
                <li><strong>Activer toutes les fonctionnalités</strong> - Accéder à l'ensemble de nos services</li>
            </ul>

            <div class="info-box">
                <h3 style="color: #38B2AC; margin-top: 0;">Vos informations d'inscription</h3>
                <p style="margin: 8px 0;"><strong>Email :</strong> {{ $user->email }}</p>
                <p style="margin: 8px 0;"><strong>Nom d'affichage :</strong> {{ $user->pseudo ?? $user->firstname }}</p>
                <p style="margin: 8px 0;"><strong>Date d'inscription :</strong> {{ $user->created_at->format('d/m/Y à H:i') }}</p>
                @if($user->newsletter)
                    <p style="margin: 8px 0;">✓ Inscrit à la newsletter pour recevoir nos meilleures offres</p>
                @endif
            </div>

            <div class="divider"></div>

            <h3>Besoin d'aide ?</h3>
            <p>Si vous rencontrez des difficultés ou si vous n'avez pas demandé cette inscription :</p>
            <ul>
                <li>Contactez notre support : <a href="mailto:{{ config('mail.from.address') }}" style="color: #38B2AC;">{{ config('mail.from.address') }}</a></li>
                <li>Consultez notre <a href="{{ route('help') }}" style="color: #38B2AC;">centre d'aide</a></li>
                <li>Si vous n'avez pas créé ce compte, ignorez simplement cet email</li>
            </ul>

            <p>Nous avons hâte de vous accueillir sur {{ config('app.name') }} !</p>

            <p>Cordialement,<br>
            <strong>L'équipe {{ config('app.name') }}</strong></p>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. Tous droits réservés.</p>
            <p>Cet email a été envoyé à {{ $user->email }} suite à une demande d'inscription.</p>
            <p class="text-small">Si vous n'êtes pas à l'origine de cette demande, vous pouvez ignorer cet email en toute sécurité.</p>
        </div>
    </div>
</body>
</html>