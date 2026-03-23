<?php

namespace Database\Factories;

use App\Enums\BookGenre;
use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Book>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $copies = $this->faker->numberBetween(1, 10);

        return [
            'title' => $this->faker->sentence(3),
            'author' => $this->faker->name(),
            'isbn' => $this->faker->isbn13(),
            'publisher' => $this->faker->company(),
            'genre' => $this->faker->randomElement(BookGenre::cases())->value,
            'published_at' => $this->faker->dateTimeBetween('-50 years', 'now')->format('Y-m-d'),
            'description' => $this->faker->paragraph(),
            'copies' => $copies,
            'available_copies' => $copies,
        ];
    }
}
