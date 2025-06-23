<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Other seeders
        $this->call([
            RolePermissionSeeder::class,
            UserSeeder::class,
            ExpenseCategorySeeder::class,
            ExpenseSeeder::class, // Add this line
        ]);
    }

}