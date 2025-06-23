<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Expense;
use App\Models\User;
use App\Models\ExpenseCategory;

class ExpenseSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure categories and users exist
        if (User::count() === 0 || ExpenseCategory::count() === 0) {
            $this->command->warn('Users or Expense Categories not found. Run their seeders first.');
            return;
        }

        Expense::factory()->count(50)->create();
    }
}
