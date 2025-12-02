<?php
// database/factories/PersonFactory.php

namespace Database\Factories;

use App\Models\Person;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PersonFactory extends Factory
{
    protected $model = Person::class;

    public function definition(): array
    {
        $gender = $this->faker->randomElement(['M', 'F']);
        $firstName = $this->faker->firstName($gender === 'M' ? 'male' : 'female');
        $lastName = $this->faker->lastName();
        $email = Str::lower($firstName) . '.' . Str::lower($lastName) . '@' . $this->faker->domainName();

        return [
            'user_id' => User::factory(), // Associe à un nouvel utilisateur par défaut
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'phone' => $this->faker->phoneNumber(),
            'notes' => $this->faker->sentence(),
            
            // ✅ Token facial simulé : 128 caractères hex (comme Argon2)
            // En production, viendra de l'API Python
            'face_token' => bin2hex(random_bytes(64)), // 128 caractères hex uniques
            
            'face_age' => $this->faker->numberBetween(18, 65),
            'face_gender' => $gender,
            
            // Chemin d'image simulé
            'image_path' => 'public/faces/' . (string) Str::uuid() . '.jpg',
            'image_original_name' => $firstName . ' ' . $lastName . ' photo.jpg',
            
            'is_active' => $this->faker->boolean(90), // 90% actif
            'registered_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }

    /**
     * Personne inactive
     */
    public function inactive(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Personne active avec email spécifique
     */
    public function withEmail($email): Factory
    {
        return $this->state(fn (array $attributes) => [
            'email' => $email,
        ]);
    }

    /**
     * Personne associée à un utilisateur spécifique
     */
    public function forUser(User $user): Factory
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
        ]);
    }
}