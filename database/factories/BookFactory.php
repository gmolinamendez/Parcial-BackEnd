<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class BookFactory extends Factory
{
    public function definition(): array
    {
        $totalCopies = fake()->numberBetween(1, 20);
        $availableCopies = fake()->numberBetween(0, $totalCopies);

        return [
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'isbn' => fake()->unique()->numerify('978##########'),
            'total_copies' => $totalCopies,
            'available_copies' => $availableCopies,
            'status' => $availableCopies > 0,
        ];
    }
}
