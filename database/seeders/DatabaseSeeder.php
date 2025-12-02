<?php
// database/seeders/DatabaseSeeder.php

namespace Database\Seeders;

use App\Models\Person;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ✅ Reset les constantes Faker pour éviter les doublons
        // (important pour les champs unique() comme face_token)
        $this->command->info('🔄 Réinitialisation Faker...');
        \Faker\Factory::create()->unique(true);

        // ============================================
        // 1. Créer l'utilisateur ADMIN
        // ============================================
        $this->command->info('👤 Création de l\'admin...');
        $admin = User::factory()->create([
            'name' => 'John Doe Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $this->command->info("✅ Admin créé: {$admin->email}");

        // ============================================
        // 2. Créer des utilisateurs de test
        // ============================================
        $this->command->info('👥 Création de 5 utilisateurs de test...');
        User::factory(5)->create();
        $this->command->info('✅ 5 utilisateurs créés');

        // ============================================
        // 3. Créer 10 personnes pour l'admin
        // ============================================
        $this->command->info('🎭 Création de 10 personnes pour l\'admin...');
        Person::factory(10)->forUser($admin)->create([
            'is_active' => true,
        ]);
        $this->command->info('✅ 10 personnes créées');

        // ============================================
        // 4. Créer 5 personnes orphelines (user_id = null)
        // ============================================
        $this->command->info('🎭 Création de 5 personnes orphelines...');
        Person::factory(5)->create([
            'user_id' => null,
            'is_active' => true,
        ]);
        $this->command->info('✅ 5 personnes orphelines créées');

        // ============================================
        // 5. Créer des personnes pour les autres utilisateurs
        // ============================================
        $this->command->info('🎭 Création de personnes pour les autres utilisateurs...');
        User::where('email', '!=', 'admin@example.com')->each(function ($user) {
            Person::factory(rand(3, 7))->forUser($user)->create();
        });
        $this->command->info('✅ Personnes associées aux utilisateurs');

        // ============================================
        // RÉSUMÉ
        // ============================================
        $this->command->info('');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->info('✅ Seeder complété !');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->info('📊 Statistiques:');
        $this->command->info('   • Utilisateurs: ' . User::count());
        $this->command->info('   • Personnes: ' . Person::count());
        $this->command->info('   • Personnes actives: ' . Person::active()->count());
        $this->command->info('   • Paramètres: ' . \DB::table('settings')->count());
        $this->command->info('');
        $this->command->info('🔑 Identifiants de test:');
        $this->command->info('   Email: admin@example.com');
        $this->command->info('   Mot de passe: password');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
    }
}