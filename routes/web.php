<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    ProfileController,
    DashboardController,
    UserController,
    RoleController,
    ExpenseCategoryController,
    ExpenseController
};

// Redirect root to dashboard
Route::get('/', fn () => redirect()->route('dashboard'));

// Group all routes for authenticated and verified users
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/chart-data', [DashboardController::class, 'getChartData'])->name('dashboard.chart-data');

    // User Management
    Route::resource('users', UserController::class);

    // Role Management
    Route::resource('roles', RoleController::class);

    // Expense Categories
    Route::resource('expense-categories', ExpenseCategoryController::class);

    // Expenses
    Route::resource('expenses', ExpenseController::class);

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Auth routes from Breeze/Fortify/etc.
require __DIR__ . '/auth.php';
