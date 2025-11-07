<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Ajoute les champs pour différencier les 4 types de rédacteurs :
     * - community : Rédacteur Communauté (article test obligatoire)
     * - client_contributor : Client-Contributeur (voyage vérifié)
     * - partner : Partenaire-Rédacteur (max 20% auto-promo)
     * - team : Équipe Nomadie (accès mode commande)
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Type de rédacteur
            $table->enum('writer_type', ['community', 'client_contributor', 'partner', 'team'])
                  ->nullable()
                  ->after('role')
                  ->comment('Type de rédacteur : community, client_contributor, partner, team');

            // Statut du rédacteur (pour workflow validation)
            $table->enum('writer_status', ['pending_validation', 'validated', 'rejected', 'suspended'])
                  ->nullable()
                  ->after('writer_type')
                  ->comment('Statut validation : pending_validation, validated, rejected, suspended');

            // Date de validation en tant que rédacteur
            $table->timestamp('writer_validated_at')
                  ->nullable()
                  ->after('writer_status')
                  ->comment('Date de validation du statut rédacteur');

            // Notes admin sur le rédacteur (raison rejet, etc.)
            $table->text('writer_notes')
                  ->nullable()
                  ->after('writer_validated_at')
                  ->comment('Notes admin sur le rédacteur');

            // Pour les client-contributeurs : ID de la réservation vérifiée
            $table->unsignedBigInteger('verified_booking_id')
                  ->nullable()
                  ->after('writer_notes')
                  ->comment('ID réservation vérifiée pour client-contributeur');

            // Pour les partenaires : lien vers offre commerciale
            $table->string('partner_offer_url')
                  ->nullable()
                  ->after('verified_booking_id')
                  ->comment('URL offre commerciale pour partenaires');

            // Index pour performances
            $table->index('writer_type');
            $table->index('writer_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['writer_type']);
            $table->dropIndex(['writer_status']);

            $table->dropColumn([
                'writer_type',
                'writer_status',
                'writer_validated_at',
                'writer_notes',
                'verified_booking_id',
                'partner_offer_url'
            ]);
        });
    }
};
