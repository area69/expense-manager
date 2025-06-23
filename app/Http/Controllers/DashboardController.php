<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Query limited by role
        $query = Expense::with('category');
        if (!$user->hasRole('Administrator')) {
            $query->where('user_id', $user->id);
        }

        // Expense categories with related expenses (for count cards)
        $categories = ExpenseCategory::with(['expenses' => function ($q) use ($user) {
            if (!$user->hasRole('Administrator')) {
                $q->where('user_id', $user->id);
            }
        }])->get();

        // Total expense amount (filtered by role)
        $totalExpenses = $query->sum('amount');

        // Chart data: Total expense per category
        $chartData = $query
            ->select('expense_category_id', DB::raw('SUM(amount) as total'))
            ->groupBy('expense_category_id')
            ->get()
            ->map(function ($expense) {
                return [
                    'category' => optional($expense->category)->name,
                    'total' => (float) $expense->total
                ];
            });

        return view('dashboard', [
            'expensesByCategory' => $categories,
            'totalExpenses' => $totalExpenses,
            'chartData' => $chartData
        ]);
    }

    // Optional API route if you want AJAX chart support later
    public function getChartData()
    {
        $user = auth()->user();

        $query = Expense::with('category');
        if (!$user->hasRole('Administrator')) {
            $query->where('user_id', $user->id);
        }

        $data = $query
            ->select('expense_category_id', DB::raw('SUM(amount) as total'))
            ->groupBy('expense_category_id')
            ->get()
            ->map(function ($expense) {
                return [
                    'category' => optional($expense->category)->name,
                    'total' => (float) $expense->total
                ];
            });

        return response()->json($data);
    }
}
