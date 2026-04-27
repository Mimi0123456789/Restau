<?php

namespace Database\Factories;

use App\Models\Menu;
use Illuminate\Database\Eloquent\Factories\Factory;

class MenuFactory extends Factory
{
    protected $model = Menu::class;

    public function definition(): array
    {
        return [
            'titre' => 'Menu ' . $this->faker->word(),
            'nombre_personne_minimum' => rand(5, 20),
            'prix_par_personne' => rand(15, 40),
            'description' => $this->faker->sentence(),
            'quantite_restante' => rand(1, 100),
            // ⛔ PAS de regime_id ici
        ];
    }
}
