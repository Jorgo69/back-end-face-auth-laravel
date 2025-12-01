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
        Schema::create('people', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->nullable()->constrained()->onDelete('set null');
            
            // Informations de la personne
            $table->string('first_name');
            $table->string('last_name');
            $table->string('full_name')->virtualAs('concat(first_name, " ", last_name)');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('notes')->nullable();
            
            // Face Token (généré par Python API)
            $table->text('face_token')->unique()->comment('Token Argon2 unique du visage');
            $table->integer('face_age')->nullable();
            $table->string('face_gender', 1)->nullable();
            
            // Image originale (path)
            $table->string('image_path')->comment('Chemin vers l\'image de référence');
            $table->string('image_original_name')->nullable();
            
            // Métadonnées
            $table->boolean('is_active')->default(true);
            $table->timestamp('registered_at')->useCurrent();
            $table->timestamps();
            $table->softDeletes();
            
            // Index
            $table->index('full_name');
            $table->index('email');
            $table->index('is_active');
            $table->index(['user_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('people');
    }
};
