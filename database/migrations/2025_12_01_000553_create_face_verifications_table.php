<?php
// database/migrations/2025_12_01_000217_create_face_verifications_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ✅ Les types ENUM sont créés dans create_people_table.php
        // Pas besoin de les recréer ici

        Schema::create('face_verifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            // Relations
            $table->foreignUuid('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignUuid('person_id')->nullable()->constrained('people')->onDelete('cascade');
            
            // Type de vérification
            $table->enum('type', [
                'user_login',
                'person_match',
                'two_images',
                'enrollment',
            ])->default('user_login');
            
            // Résultat
            $table->boolean('match_found')->default(false);
            $table->string('status')->default('pending')->comment('pending|success|failed|error');
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

    public function down(): void
    {
        Schema::dropIfExists('face_verifications');
        // Les types ENUM sont supprimés dans create_people_table.php
    }
};