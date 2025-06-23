<?php

namespace Database\Factories;

use App\Models\Expense;
use App\Models\User;
use App\Models\ExpenseCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExpenseFactory extends Factory
{
    protected $model = Expense::class;

    public function definition(): array
    {
        return [
            'user_id' => User::inRandomOrder()->first()?->id ?? User::factory(),
            'expense_category_id' => ExpenseCategory::inRandomOrder()->first()?->id ?? ExpenseCategory::factory(),
            'description' => $this->faker->sentence(4),
            'notes' => $this->faker->optional()->paragraph,
            'amount' => $this->faker->randomFloat(2, 50, 2000),
            'date' => $this->faker->dateTimeBetween('-6 months', 'now')->format('Y-m-d'),
        ];
    }
}
