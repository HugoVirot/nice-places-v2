<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Avis>
 */
class ReviewFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'user_id' => rand(1, 10),
            'place_id' => rand(1, 10),
            'note' => rand(7, 10),
            'comment' => $this->faker->randomElement([null,'J\'ai adoré', 'Génial', 'Inoubliable. Foncez !', 'Pas mal du tout',
            'De très bons souvenirs. Je recommande !', 'pas mal mais il y a mieux', 'Exceptionnel !', 'Allez-y !', 'Super endroit.',
            'mes enfants ont adoré']),
            'status' => $this->faker->randomElement(['validé', 'en attente', 'refusé'])
        ];
    }
}
