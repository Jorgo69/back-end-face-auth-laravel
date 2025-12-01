<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('face_verifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            // Relations
            $table->foreignUuid('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignUuid('person_id')->nullable()->constrained()->onDelete('cascade');
            
            // Type de vérification
            $table->enum('type', [
                'user_login',        // Connexion utilisateur
                'person_match',      // Comparaison avec personne
                'two_images',        // Comparaison de 2 images
                'enrollment',        // Enregistrement initial
            ]);
            
            // Résultat
            $table->boolean('match_found')->default(false);
            $table->string('status')->comment('pending|success|failed|error');
            $table->text('detail')->nullable()->comment('Message de l\'API Python');
            
            // Métadonnées
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->json('metadata')->nullable()->comment('Données supplémentaires');
            
            // Temps de réponse
            $table->integer('response_time_ms')->nullable()->comment('Temps de réponse API Python');
            
            $table->timestamps();
            
            // Index pour analytics
            $table->index('type');
            $table->index('status');
            $table->index('match_found');
            $table->index('created_at');
            $table->index(['user_id', 'created_at']);
            $table->index(['person_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('face_verifications');
    }
};
