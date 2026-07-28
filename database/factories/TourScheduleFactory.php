<?php

namespace Database\Factories;

use App\Models\TourSchedule;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TourSchedule>
 */
class TourScheduleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $start = $this->faker->dateTimeBetween('+1 day', '+30 days');
        $end = (clone $start)->modify('+' . $this->faker->numberBetween(120, 360) . ' minutes');

        return [
            'start_datetime' => $start,
            'end_datetime' => $end,
            'max_spots' => $this->faker->numberBetween(5, 20),
            'booked_spots' => 0,
            'price_override' => null,
            'status' => 'available',
            'is_active' => true,
        ];
    }

    public function fullyBooked(): static
    {
        return $this->state(fn (array $attributes) => [
            'booked_spots' => $attributes['max_spots'],
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
        ]);
    }
}
