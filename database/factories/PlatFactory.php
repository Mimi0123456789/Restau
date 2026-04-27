<?php

namespace Database\Factories;

use App\Models\Plat;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlatFactory extends Factory
{
    protected $model = Plat::class;

    public function definition(): array
    {
        return [
            'titre_plat' => ucfirst($this->faker->words(2, true)),
            'photo' => null,
        ];
    }
}
