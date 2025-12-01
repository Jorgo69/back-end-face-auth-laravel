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
        Schema::create('settings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('key')->unique()->comment('Clé de configuration (ex: python_api_url)');
            $table->text('value')->nullable()->comment('Valeur de la configuration');
            $table->string('type')->default('string')->comment('string|integer|boolean|json');
            $table->string('group')->default('general')->comment('Groupe de paramètres');
            $table->string('label')->comment('Libellé pour l\'interface');
            $table->text('description')->nullable()->comment('Description du paramètre');
            $table->boolean('is_public')->default(false)->comment('Visible dans l\'API publique');
            $table->timestamps();
            
            // Index
            $table->index('key');
            $table->index('group');
            $table->index('is_public');
        });
        
        // Insérer les paramètres par défaut
        DB::table('settings')->insert([
            [
                'id' => (string) Str::uuid(),
                'key' => 'python_api_url',
                'value' => 'http://localhost:8000',
                'type' => 'string',
                'group' => 'faceauth',
                'label' => 'URL de l\'API Python FaceAuth',
                'description' => 'URL complète de l\'API de reconnaissance faciale (ex: http://localhost:8000 ou https://api.faceauth.io)',
                'is_public' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => (string) Str::uuid(),
                'key' => 'python_api_timeout',
                'value' => '30',
                'type' => 'integer',
                'group' => 'faceauth',
                'label' => 'Timeout API Python (secondes)',
                'description' => 'Temps maximum d\'attente pour une réponse de l\'API Python',
                'is_public' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => (string) Str::uuid(),
                'key' => 'face_verification_enabled',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'faceauth',
                'label' => 'Activer la vérification faciale',
                'description' => 'Désactiver temporairement la reconnaissance faciale (mode maintenance)',
                'is_public' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
