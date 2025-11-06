<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenue ! Votre compte est actif - {{ config('app.name') }}</title>
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
        .btn-secondary {
            background-color: #e2e8f0;
            color: #2d3748;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: 500;
            margin: 8px 4px;
            display: inline-block;
            font-size: 14px;
        }
        .success-box {
            background-color: #f0fff4;
            border-left: 4px solid #48bb78;
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
        .feature-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
            margin: 20px 0;
        }
        .feature-item {
            text-align: center;
            padding: 16px;
            background-color: #f7fafc;
            border-radius: 8px;
        }
        .feature-label {
            font-size: 14px;
            color: #4a5568;
            font-weight: 500;
        }
        .quick-links {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 24px 0;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            background-color: #d1fae5;
            color: #059669;
        }
        .promo-box {
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            border: 2px solid #f59e0b;
            padding: 20px;
            margin: 24px 0;
            border-radius: 8px;
            text-align: center;
        }
        .promo-code {
            display: inline-block;
            background-color: #ffffff;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 20px;
            font-weight: bold;
            color: #f59e0b;
            letter-spacing: 1px;
            margin: 8px 0;
            border: 2px dashed #f59e0b;
        }
        ul {
            padding-left: 20px;
        }
        ol {
            padding-left: 20px;
        }
        li {
            margin: 8px 0;
        }
        .icon-check {
            display: inline-block;
            width: 20px;
            height: 20px;
            vertical-align: middle;
            margin-right: 6px;
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
            <div class="success-box">
                <h2 style="color: #48bb78; margin-top: 0;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline-block; vertical-align: middle; margin-right: 8px;">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                    </svg>
                    Félicitations ! Votre compte est maintenant actif
                </h2>
                <p style="margin-bottom: 0;">Email vérifié avec succès - <span class="status-badge">Compte actif</span></p>
            </div>

            <p>Bonjour <span class="highlight">{{ $user->firstname }} {{ $user->lastname }}</span>,</p>

            <p>Excellente nouvelle ! Votre adresse email a été vérifiée avec succès et votre compte <strong>{{ config('app.name') }}</strong> est maintenant <strong>pleinement actif</strong>.</p>

            <p>Vous avez désormais accès à l'ensemble de notre marketplace : hébergements uniques, séjours organisés, activités locales et expériences sur mesure.</p>

            <!-- Informations du compte -->
            <div class="quick-links">
                <h3 style="margin-top: 0;">Récapitulatif de votre compte</h3>
                <p style="margin: 8px 0;"><strong>Email de connexion :</strong> {{ $user->email }}</p>
                <p style="margin: 8px 0;"><strong>Nom d'affichage :</strong> {{ $user->pseudo ?? $user->firstname }}</p>
                <p style="margin: 8px 0;"><strong>Date d'inscription :</strong> {{ $user->created_at->format('d/m/Y à H:i') }}</p>
                <p style="margin: 8px 0;"><strong>Statut :</strong> <span class="status-badge">Vérifié</span></p>
                @if($user->newsletter)
                    <p style="margin: 8px 0;"><strong>Newsletter :</strong> Inscrit aux actualités</p>
                @endif
            </div>

            <!-- Fonctionnalités disponibles -->
            <div class="info-box">
                <h3 style="color: #38B2AC; margin-top: 0;">Vos avantages membre</h3>
                <div class="feature-grid">
                    <div class="feature-item">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#38B2AC" stroke-width="2" style="margin: 0 auto 8px; display: block;">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                            <polyline points="9 22 9 12 15 12 15 22"></polyline>
                        </svg>
                        <div class="feature-label">Hébergements uniques</div>
                    </div>
                    <div class="feature-item">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#38B2AC" stroke-width="2" style="margin: 0 auto 8px; display: block;">
                            <circle cx="12" cy="10" r="3"></circle>
                            <path d="M12 21.7C17.3 17 20 13 20 10a8 8 0 1 0-16 0c0 3 2.7 6.9 8 11.7z"></path>
                        </svg>
                        <div class="feature-label">Séjours organisés</div>
                    </div>
                    <div class="feature-item">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#38B2AC" stroke-width="2" style="margin: 0 auto 8px; display: block;">
                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                        </svg>
                        <div class="feature-label">Activités & expériences</div>
                    </div>
                    <div class="feature-item">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#38B2AC" stroke-width="2" style="margin: 0 auto 8px; display: block;">
                            <path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"></path>
                        </svg>
                        <div class="feature-label">Offres sur mesure</div>
                    </div>
                </div>
            </div>

            <!-- Bouton d'accès -->
            <div style="text-align: center;">
                <a href="{{ $loginUrl }}" class="btn">Accéder à mon espace membre</a>
            </div>

            <!-- Offre de bienvenue -->
            <div class="promo-box">
                <h3 style="color: #92400e; margin-top: 0;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline-block; vertical-align: middle; margin-right: 8px;">
                        <rect x="3" y="8" width="18" height="12" rx="2" ry="2"></rect>
                        <rect x="7" y="4" width="10" height="8" rx="1" ry="1"></rect>
                        <path d="M12 4v4"></path>
                    </svg>
                    Cadeau de bienvenue
                </h3>
                <p style="margin: 8px 0;"><strong>10% de réduction</strong> sur votre première réservation</p>
                <div class="promo-code">BIENVENUE10</div>
                <p class="text-small" style="margin: 8px 0 0;">Valable pendant 30 jours</p>
            </div>

            <div class="divider"></div>

            <h3>Prochaines étapes recommandées</h3>
            <ol>
                <li><strong>Complétez votre profil</strong> - Ajoutez une photo et vos préférences</li>
                <li><strong>Explorez notre marketplace</strong> - Découvrez hébergements, séjours et activités</li>
                <li><strong>Créez votre liste de favoris</strong> - Sauvegardez vos coups de cœur pour plus tard</li>
                <li><strong>Configurez vos alertes</strong> - Soyez informé des nouvelles offres qui vous correspondent</li>
            </ol>

            <!-- Ressources utiles -->
            <div class="info-box">
                <h3 style="color: #38B2AC; margin-top: 0;">Ressources utiles</h3>
                <ul style="margin-bottom: 0;">
                    <li><a href="{{ route('destinations') }}" style="color: #38B2AC; text-decoration: none;"><strong>Toutes nos destinations</strong></a> - Parcourez notre catalogue complet</li>
                    <li><a href="{{ route('help') }}" style="color: #38B2AC; text-decoration: none;"><strong>Centre d'aide</strong></a> - FAQ et guides pratiques</li>
                    <li><a href="{{ route('profile') }}" style="color: #38B2AC; text-decoration: none;"><strong>Mon profil</strong></a> - Gérez vos informations personnelles</li>
                    <li><a href="{{ route('contact') }}" style="color: #38B2AC; text-decoration: none;"><strong>Nous contacter</strong></a> - Notre équipe est à votre écoute</li>
                </ul>
            </div>

            <div class="divider"></div>

            <h3>Besoin d'assistance ?</h3>
            <p>Notre équipe support est disponible pour vous accompagner :</p>
            <ul>
                <li><strong>Email :</strong> <a href="mailto:{{ config('mail.from.address') }}" style="color: #38B2AC;">{{ config('mail.from.address') }}</a></li>
                <li><strong>Horaires :</strong> Du lundi au vendredi, 9h-18h</li>
                <li><strong>Temps de réponse moyen :</strong> Moins de 24h</li>
            </ul>

            <p>Nous sommes ravis de vous compter parmi nos membres et avons hâte de vous accompagner dans vos prochaines aventures !</p>

            <p>Excellent voyage avec {{ config('app.name') }} !</p>

            <p>Cordialement,<br>
            <strong>L'équipe {{ config('app.name') }}</strong></p>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. Tous droits réservés.</p>
            <p>Cet email confirme l'activation de votre compte suite à la validation de votre adresse email.</p>
            <p>{{ $user->email }} - Compte vérifié le {{ now()->format('d/m/Y à H:i') }}</p>
        </div>
    </div>
</body>
</html>