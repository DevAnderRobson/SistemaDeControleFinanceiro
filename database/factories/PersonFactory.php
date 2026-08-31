<?php

namespace Database\Factories;

use App\Enums\PersonType;
use App\Models\Person;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Person>
 */
class PersonFactory extends Factory
{
    protected $model = Person::class;

    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'document' => $this->generateCnpj(),
            'email' => fake()->unique()->companyEmail(),
            'phone' => fake()->phoneNumber(),
            'type' => fake()->randomElement([PersonType::Customer, PersonType::Supplier]),
        ];
    }

    public function customer(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => PersonType::Customer,
        ]);
    }

    public function supplier(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => PersonType::Supplier,
        ]);
    }

    private function generateCnpj(): string
    {
        $n = [fake()->numberBetween(10, 99), fake()->numberBetween(100, 999), fake()->numberBetween(100, 999), '0001'];
        $base = sprintf('%02d%03d%03d0001', $n[0], $n[1], $n[2]);

        $weights1 = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $sum += (int) $base[$i] * $weights1[$i];
        }
        $rem = $sum % 11;
        $d1 = ($rem < 2) ? 0 : 11 - $rem;

        $weights2 = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $sum += (int) $base[$i] * $weights2[$i];
        }
        $sum += $d1 * $weights2[12];
        $rem = $sum % 11;
        $d2 = ($rem < 2) ? 0 : 11 - $rem;

        return $base . $d1 . $d2;
    }
}
