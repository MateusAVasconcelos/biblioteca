<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Client>
 */
class ClientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->numerify('(##) #####-####'),
            'cpf' => $this->faker->unique()->numerify('###.###.###-##'),
            'address' => $this->faker->streetAddress(),
            'zip_code' => $this->faker->numerify('#####-###'),
            'city' => $this->faker->city(),
            'state' => 'SP',
            'birth_date' => $this->faker->dateTimeBetween('-60 years', '-18 years')->format('Y-m-d'),
        ];
    }
}
