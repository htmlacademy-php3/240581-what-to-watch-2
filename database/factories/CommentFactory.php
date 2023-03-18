<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'text' => $this->faker->text(),
            'rating' => $this->faker->numberBetween(1, 10),
        ];
    }
}
