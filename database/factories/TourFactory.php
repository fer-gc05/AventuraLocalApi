<?php

namespace Database\Factories;

use App\Models\Tour;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Tour>
 */
class TourFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->sentence(4);

        return [
            'title' => $title,
            'slug' => \Illuminate\Support\Str::slug($title) . '-' . $this->faker->unique()->randomNumber(6),
            'short_description' => $this->faker->sentence(10),
            'description' => $this->faker->paragraphs(3, true),
            'meeting_point' => $this->faker->address(),
            'meeting_latitude' => $this->faker->latitude(4.5, 11.0),
            'meeting_longitude' => $this->faker->longitude(-77.5, -72.5),
            'price_per_person' => $this->faker->randomElement([50000, 75000, 100000, 150000, 200000]),
            'currency' => 'COP',
            'duration_minutes' => $this->faker->randomElement([120, 180, 240, 300, 360]),
            'max_participants' => $this->faker->numberBetween(5, 20),
            'min_participants' => $this->faker->numberBetween(1, 3),
            'languages' => $this->faker->randomElements(['es', 'en'], rand(1, 2)),
            'includes' => $this->faker->randomElements(['Transporte', 'Entradas', 'Refrigerio', 'Guía experto', 'Seguro básico'], rand(2, 5)),
            'excludes' => $this->faker->randomElements(['Almuerzo', 'Propinas', 'Hotel', 'Transporte desde el aeropuerto'], rand(1, 3)),
            'what_to_bring' => $this->faker->randomElements(['Ropa cómoda', 'Zapatos para caminar', 'Bloqueador', 'Agua', 'Cámara'], rand(2, 4)),
            'difficulty' => $this->faker->randomElement(['easy', 'medium', 'hard']),
            'status' => 'published',
            'is_active' => true,
        ];
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
        ]);
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'published',
        ]);
    }
}
