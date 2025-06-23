<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ExpenseCategory;

class ExpenseCategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            ['name' => 'Food & Dining', 'description' => 'Restaurant meals, groceries, etc.'],
            ['name' => 'Transportation', 'description' => 'Gas, public transport, taxi, etc.'],
            ['name' => 'Shopping', 'description' => 'Clothing, electronics, etc.'],
            ['name' => 'Entertainment', 'description' => 'Movies, games, hobbies, etc.'],
            ['name' => 'Bills & Utilities', 'description' => 'Electricity, water, internet, etc.'],
        ];

        foreach ($categories as $category) {
            ExpenseCategory::create($category);
        }
    }
}