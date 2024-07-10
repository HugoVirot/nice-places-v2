<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class PlaceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    public function definition()
    {
        static $number = 1;

        return [
            'name' => 'Lieu de test n°' . $number++,
            'description' => $this->faker->sentence(),
            'latitude' => rand(44, 49),
            'longitude' => rand(-1, 6.9),
            'note' => rand(6, 10),
            'time' => rand(1, 5),
            'difficulty' => $this->faker->randomElement(['famille' ,'amateur', 'sportif']),
            'adress' => '15, route du test',
            'postcode' => substr($this->faker->departmentNumber() . '000', 0, 5),
            'city' => $this->faker->randomElement(['Ville de Test' ,'TestVille', 'Ville Fictive']),
            'user_id' => rand(2, 10),
            'category_id' => rand(1,9),
            'department_id' => rand(1,99)
        ];
    }
}
