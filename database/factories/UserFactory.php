<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'prenom' => fake()->firstName(),
            'nom' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'password' => Hash::make('password'),
            'telephone' => fake()->numerify('0#########'),
            'ville' => fake()->city(),
            'code_postal' => fake()->postcode(),
            'pays' => 'France',
            'adresse_postale' => fake()->streetAddress(),
            'role_id' => 3,
            'is_active' => true,
            'remember_token' => Str::random(10),
        ];
    }
}
