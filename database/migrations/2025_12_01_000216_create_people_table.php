<?php
// database/migrations/2025_12_01_000216_create_people_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ✅ Créer les types ENUM PostgreSQL EN PREMIER
        // Avant de créer les tables qui les utilisent
        if (env('DB_CONNECTION') === 'pgsql' || DB::connection()->getDriverName() === 'pgsql') {
            DB::statement("
                CREATE TYPE verification_type AS ENUM (
                    'user_login',
                    'person_match',
                    'two_images',
                    'enrollment'
                )
            ");
            
            DB::statement("
                CREATE TYPE setting_type AS ENUM (
                    'string',
                    'integer',
                    'boolean',
                    'json'
                )
            ");
        }

        Schema::create('people', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->nullable()->constrained('users')->onDelete('set null');
            
            // Informations de la personne
            $table->string('first_name');
            $table->string('last_name');
            // ✅ Pas de colonne full_name - c'est un accessor du Model
            
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('notes')->nullable();
            
            // Face Token (généré par Python API)
            $table->text('face_token')->unique()->comment('Token Argon2 unique du visage');
            $table->integer('face_age')->nullable();
            $table->char('face_gender', 1)->nullable();
            
            // Image originale (path)
            $table->string('image_path')->comment('Chemin vers l\'image de référence');
            $table->string('image_original_name')->nullable();
            
            // Métadonnées
            $table->boolean('is_active')->default(true);
            $table->timestamp('registered_at')->useCurrent();
            $table->timestamps();
            $table->softDeletes();
            
            // Index
            // ✅ Pas d'index sur full_name (c'est un accessor, pas une colonne)
            $table->index('email');
            $table->index('is_active');
            $table->index(['user_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('people');
        
        // ✅ Supprimer les types ENUM PostgreSQL
        if (env('DB_CONNECTION') === 'pgsql' || DB::connection()->getDriverName() === 'pgsql') {
            DB::statement('DROP TYPE IF EXISTS verification_type CASCADE');
            DB::statement('DROP TYPE IF EXISTS setting_type CASCADE');
        }
    }
};