<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Person;
use App\Models\User;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Factory>
 */
class PersonFactoryCopy extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Person::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $gender = $this->faker->randomElement(['M', 'F']);
        $firstName = $this->faker->firstName($gender == 'M' ? 'male' : 'female');
        $lastName = $this->faker->lastName();
        $email = Str::lower($firstName) . '.' . Str::lower($lastName) . '@' . $this->faker->domainName();

        return [
            'user_id' => User::factory(), // Associe à un nouvel utilisateur par défaut
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'phone' => $this->faker->phoneNumber(),
            'notes' => $this->faker->sentence(),
            
            // Simulation de données faciales
            // 'face_token' => Str::uuid()->toString() . Str::random(60), // Simuler un token unique et long
            // NOUVEAU : Utiliser unique() avec un hash long pour simuler l'unicité
            // 'face_token' => $this->faker->unique()->sha256(), // Ou une autre méthode longue et unique
            
            // Si SHA256 (64 caractères) n'est pas assez long et que vous voulez garder le format UUID + Aléatoire
            // 'face_token' => $this->faker->unique()->numerify(Str::uuid()->toString() . '############################################################'),
            // OU simplement:
            // 'face_token' => $this->faker->unique()->regexify('[A-Za-z0-9]{128}'), // Génère une chaîne aléatoire de 128 caractères et garantit l'unicité pour le seeder
            // 'face_token' => bin2hex(random_bytes(64)), // 128 caractères hex
            // 'face_token' => Str::uuid().'-'.Str::random(64),
            'face_token' => (string) Str::uuid(),




            'face_age' => $this->faker->numberBetween(18, 65),
            'face_gender' => $gender,
            
            // Simuler un chemin d'image (vous pouvez le lier à une image par défaut si nécessaire)
            'image_path' => 'public/faces/' . Str::uuid() . '.jpg', 
            'image_original_name' => $firstName . ' ' . $lastName . ' photo.jpg',
            
            'is_active' => $this->faker->boolean(90), // 90% de chance d'être actif
            'registered_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }

    /**
     * Indique que la personne est inactive.
     */
    public function inactive(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
