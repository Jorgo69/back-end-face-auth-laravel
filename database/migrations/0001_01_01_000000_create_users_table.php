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
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            
            // Face Authentication Fields
            $table->text('face_token')->nullable()->comment('Token Argon2 du visage (irréversible)');
            $table->integer('face_age')->nullable()->comment('Âge estimé lors de l\'enregistrement');
            $table->string('face_gender', 1)->nullable()->comment('M ou F');
            $table->timestamp('face_registered_at')->nullable()->comment('Date d\'enregistrement du visage');
            $table->timestamp('face_last_verified_at')->nullable()->comment('Dernière vérification réussie');
            $table->integer('face_verification_count')->default(0)->comment('Nombre de vérifications réussies');
            
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
            
            // Index pour performances
            $table->index('email');
            $table->index('face_registered_at');
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignUuid('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
