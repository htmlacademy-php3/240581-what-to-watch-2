<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class FilmFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->paragraph(1),
            'poster_image' => $this->faker->imageUrl(),
            'preview_image' => $this->faker->imageUrl(),
            'background_image' => $this->faker->imageUrl(),
            'background_color' => $this->faker->hexColor(),
            'video_link' => $this->faker->url(),
            'preview_video_link' => $this->faker->url(),
            'description' => $this->faker->text(),
            'director' => $this->faker->name(),
            'run_time' => $this->faker->numberBetween(30, 200),
            'released' => $this->faker->year(),
            'imdb_id' => 'tt' . $this->faker->unique()->randomNumber(7, true),
            'rating' => $this->faker->numberBetween(1, 10),
        ];
    }
}
