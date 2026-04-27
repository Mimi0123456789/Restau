<?php

namespace Database\Factories;

use App\Models\Commande;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommandeFactory extends Factory
{
    protected $model = Commande::class;

    public function definition(): array
    {
        return [
            'date_commande' => now(),
            'date_prestation' => $this->faker->dateTimeBetween('+1 days', '+1 month'),
            'heure_livraison' => $this->faker->time(),
            'prix_menu' => rand(100, 500),
            'nombre_personne' => rand(5, 50),
            'prix_livraison' => rand(10, 50),
            'statut' => $this->faker->randomElement(['en attente', 'confirmée', 'livrée']),
            'pret_materiel' => rand(0, 1),
            'restitution_materiel' => rand(0, 1),
            // ⛔ PAS de user_id
        ];
    }

}
