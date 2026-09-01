<?php

namespace Database\Factories;

use App\Enums\EntryStatus;
use App\Enums\EntryType;
use App\Models\FinancialEntry;
use App\Models\Person;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FinancialEntry>
 */
class FinancialEntryFactory extends Factory
{
    protected $model = FinancialEntry::class;

    public function definition(): array
    {
        $issueDate = fake()->dateTimeBetween('-2 months', 'now');
        $dueDate = (clone $issueDate)->modify('+30 days');

        return [
            'person_id' => Person::factory(),
            'type' => fake()->randomElement([EntryType::Payable, EntryType::Receivable]),
            'description' => fake()->sentence(3),
            'amount' => fake()->randomFloat(2, 50, 5000),
            'issue_date' => $issueDate->format('Y-m-d'),
            'due_date' => $dueDate->format('Y-m-d'),
            'settled_at' => null,
            'status' => EntryStatus::Pending,
            'notes' => fake()->optional()->sentence(),
        ];
    }

    public function payable(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => EntryType::Payable,
            'person_id' => Person::factory()->supplier(),
        ]);
    }

    public function receivable(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => EntryType::Receivable,
            'person_id' => Person::factory()->customer(),
        ]);
    }

    public function paid(?string $date = null): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => EntryStatus::Paid,
            'settled_at' => $date ?: Carbon::now()->toDateString(),
        ]);
    }

    public function overdue(): static
    {
        $pastDate = Carbon::now()->subDays(15);
        return $this->state(fn (array $attributes) => [
            'status' => EntryStatus::Overdue,
            'issue_date' => (clone $pastDate)->subDays(30)->toDateString(),
            'due_date' => $pastDate->toDateString(),
            'settled_at' => null,
        ]);
    }
}
