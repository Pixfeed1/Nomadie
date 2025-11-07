<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouveau message de contact</title>
    <style>
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
        h1 {
            color: #ffffff;
            font-size: 24px;
            margin: 0;
            font-weight: 700;
        }
        h2 {
            color: #2d3748;
            font-size: 20px;
            margin-top: 0;
            margin-bottom: 16px;
        }
        .info-box {
            background-color: #f7fafc;
            border-left: 4px solid #38B2AC;
            padding: 16px;
            margin: 20px 0;
            border-radius: 0 8px 8px 0;
        }
        .info-row {
            margin: 10px 0;
            font-size: 15px;
        }
        .info-label {
            font-weight: 600;
            color: #4a5568;
            display: inline-block;
            width: 120px;
        }
        .message-content {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
            font-size: 15px;
            line-height: 1.8;
            white-space: pre-wrap;
        }
        .divider {
            height: 1px;
            background-color: #e2e8f0;
            margin: 24px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📧 Nouveau message de contact</h1>
        </div>

        <div class="content">
            <h2>Informations de l'expéditeur</h2>

            <div class="info-box">
                <div class="info-row">
                    <span class="info-label">Nom :</span>
                    <span>{{ $contactData['name'] }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Email :</span>
                    <span><a href="mailto:{{ $contactData['email'] }}" style="color: #38B2AC; text-decoration: none;">{{ $contactData['email'] }}</a></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Sujet :</span>
                    <span><strong>{{ $contactData['subject'] }}</strong></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Date :</span>
                    <span>{{ now()->format('d/m/Y à H:i') }}</span>
                </div>
            </div>

            <div class="divider"></div>

            <h2>Message</h2>
            <div class="message-content">{{ $contactData['message'] }}</div>

            <div class="divider"></div>

            <p style="color: #718096; font-size: 14px; font-style: italic;">
                💡 Astuce : Vous pouvez répondre directement à cet email pour contacter {{ $contactData['name'] }}.
            </p>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. Tous droits réservés.</p>
            <p>Ce message a été envoyé via le formulaire de contact du site web.</p>
        </div>
    </div>
</body>
</html>
