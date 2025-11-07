<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Export Réservations - {{ $vendor->company_name ?? 'Vendeur' }}</title>
    <style>
        @media print {
            .no-print {
                display: none;
            }
            @page {
                margin: 2cm;
            }
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', 'Helvetica', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            background: #fff;
        }

        .container {
            max-width: 100%;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            border-bottom: 3px solid #38B2AC;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .header h1 {
            font-size: 24px;
            color: #2d3748;
            margin-bottom: 10px;
        }

        .header-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }

        .vendor-info {
            flex: 1;
        }

        .vendor-info p {
            margin: 4px 0;
            color: #4a5568;
        }

        .export-date {
            text-align: right;
            color: #718096;
            font-size: 11px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 15px;
            margin-bottom: 30px;
        }

        .stat-box {
            background: #f7fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 15px;
            text-align: center;
        }

        .stat-label {
            font-size: 10px;
            color: #718096;
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .stat-value {
            font-size: 20px;
            font-weight: bold;
            color: #2d3748;
        }

        .stat-value.revenue {
            color: #38B2AC;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        thead {
            background: #2d3748;
            color: white;
        }

        th {
            padding: 10px 8px;
            text-align: left;
            font-weight: 600;
            font-size: 11px;
            text-transform: uppercase;
        }

        tbody tr {
            border-bottom: 1px solid #e2e8f0;
        }

        tbody tr:nth-child(even) {
            background-color: #f7fafc;
        }

        tbody tr:hover {
            background-color: #edf2f7;
        }

        td {
            padding: 10px 8px;
            font-size: 11px;
        }

        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-confirmed {
            background: #c6f6d5;
            color: #22543d;
        }

        .status-pending {
            background: #fed7d7;
            color: #742a2a;
        }

        .status-cancelled {
            background: #e2e8f0;
            color: #2d3748;
        }

        .payment-paid {
            background: #c6f6d5;
            color: #22543d;
        }

        .payment-pending {
            background: #fefcbf;
            color: #744210;
        }

        .payment-refunded {
            background: #fed7d7;
            color: #742a2a;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #e2e8f0;
            text-align: center;
            color: #718096;
            font-size: 10px;
        }

        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #38B2AC;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .print-button:hover {
            background: #2C9A94;
        }
    </style>
</head>
<body>
    <button onclick="window.print()" class="print-button no-print">🖨️ Imprimer / Enregistrer en PDF</button>

    <div class="container">
        <!-- En-tête -->
        <div class="header">
            <h1>📋 Export des Réservations</h1>
            <div class="header-info">
                <div class="vendor-info">
                    <p><strong>{{ $vendor->company_name ?? 'Vendeur' }}</strong></p>
                    @if($vendor->email)
                        <p>{{ $vendor->email }}</p>
                    @endif
                </div>
                <div class="export-date">
                    <p><strong>Date d'export :</strong></p>
                    <p>{{ now()->format('d/m/Y à H:i') }}</p>
                </div>
            </div>
        </div>

        <!-- Statistiques -->
        <div class="stats-grid">
            <div class="stat-box">
                <div class="stat-label">Total</div>
                <div class="stat-value">{{ $stats['total'] }}</div>
            </div>
            <div class="stat-box">
                <div class="stat-label">Confirmées</div>
                <div class="stat-value">{{ $stats['confirmed'] }}</div>
            </div>
            <div class="stat-box">
                <div class="stat-label">En attente</div>
                <div class="stat-value">{{ $stats['pending'] }}</div>
            </div>
            <div class="stat-box">
                <div class="stat-label">Annulées</div>
                <div class="stat-value">{{ $stats['cancelled'] }}</div>
            </div>
            <div class="stat-box">
                <div class="stat-label">Chiffre d'affaires</div>
                <div class="stat-value revenue">{{ number_format($stats['revenue'], 2, ',', ' ') }} €</div>
            </div>
        </div>

        <!-- Tableau des réservations -->
        <table>
            <thead>
                <tr>
                    <th>Réf</th>
                    <th>Voyage</th>
                    <th>Client</th>
                    <th>Dates</th>
                    <th style="text-align: center;">Pers.</th>
                    <th style="text-align: right;">Montant</th>
                    <th style="text-align: center;">Statut</th>
                    <th style="text-align: center;">Paiement</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookings as $booking)
                    <tr>
                        <td><strong>#{{ $booking->id }}</strong></td>
                        <td>{{ Str::limit($booking->trip->title ?? 'N/A', 30) }}</td>
                        <td>
                            {{ $booking->user->firstname ?? '' }} {{ $booking->user->lastname ?? '' }}<br>
                            <span style="color: #718096; font-size: 10px;">{{ $booking->user->email ?? '' }}</span>
                        </td>
                        <td>
                            @if($booking->start_date && $booking->end_date)
                                {{ $booking->start_date->format('d/m/Y') }}<br>
                                <span style="color: #718096;">au {{ $booking->end_date->format('d/m/Y') }}</span>
                            @else
                                N/A
                            @endif
                        </td>
                        <td style="text-align: center;">
                            {{ ($booking->adults ?? 0) + ($booking->children ?? 0) }}<br>
                            <span style="color: #718096; font-size: 10px;">
                                ({{ $booking->adults ?? 0 }}A / {{ $booking->children ?? 0 }}E)
                            </span>
                        </td>
                        <td style="text-align: right; font-weight: 600;">
                            @if($booking->total_price)
                                {{ number_format($booking->total_price, 2, ',', ' ') }} €
                            @else
                                N/A
                            @endif
                        </td>
                        <td style="text-align: center;">
                            <span class="status-badge status-{{ $booking->status ?? 'pending' }}">
                                {{ $booking->status ?? 'pending' }}
                            </span>
                        </td>
                        <td style="text-align: center;">
                            <span class="status-badge payment-{{ $booking->payment_status ?? 'pending' }}">
                                {{ $booking->payment_status ?? 'pending' }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 40px; color: #718096;">
                            Aucune réservation à afficher
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pied de page -->
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }} - Rapport généré automatiquement</p>
            <p>Ce document contient {{ $bookings->count() }} réservation(s)</p>
        </div>
    </div>

    <script>
        // Auto-print on load (optionnel - désactivé par défaut)
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>
