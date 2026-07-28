<?php

namespace Database\Factories;

use App\Models\GuideProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GuideProfile>
 */
class GuideProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'bio' => $this->faker->paragraph(),
            'phone' => $this->faker->phoneNumber(),
            'languages' => $this->faker->randomElements(['es', 'en', 'fr', 'de', 'pt'], rand(1, 3)),
            'specialties' => $this->faker->randomElements(['gastronomía', 'historia', 'aventura', 'naturaleza', 'misterio'], rand(1, 3)),
            'documents' => null,
            'verification_status' => 'pending',
            'rejection_reason' => null,
            'is_verified' => false,
            'average_rating' => 0,
            'total_reviews' => 0,
            'available_balance' => 0,
            'pending_balance' => 0,
            'total_earnings' => 0,
            'is_active' => true,
        ];
    }

    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'verification_status' => 'approved',
            'is_verified' => true,
        ]);
    }
}
