<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Person;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeederCopy extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('Reset Faker unique');
        \Faker\Factory::create()->unique(true);

        // 1. Appel du Seeder pour les paramètres (déjà géré par la migration)
        // La migration 'settings' insère déjà les valeurs initiales.
        
        // 2. Création d'un utilisateur Administrateur/Développeur
        $admin = User::factory()->create([
            'name' => 'John Doe Admin',
            'email' => 'admin@example.com',
            'password' => \Hash::make('password'), // Mot de passe par défaut
            // Les champs face_* sont null par défaut, prêt pour l'enregistrement facial
        ]);
        
        // 3. Création d'autres utilisateurs de test
        User::factory(5)->create();

        // 4. Création de personnes associées à l'utilisateur admin
        // Ces personnes auront des face_token simulés pour les tests d'appariement.
        Person::factory(10)->create([
            'user_id' => $admin->id,
            // Simuler un token, dans la réalité, il viendra de votre API Python.
            // 'face_token' => \Str::random(128), 
            'is_active' => true,
        ]);
        
        // 5. Création de personnes orphelines (ou créées par d'autres utilisateurs)
        Person::factory(5)->create();


    }
}
