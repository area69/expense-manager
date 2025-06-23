<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\User;

class ExpenseController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $expenses = $user->hasRole('Administrator')
            ? Expense::with(['category', 'user'])->latest()->get()
            : Expense::with('category')->where('user_id', $user->id)->latest()->get();

        $categories = ExpenseCategory::all();
        $users = $user->hasRole('Administrator') ? User::all() : collect();

        return view('expenses.index', compact('expenses', 'categories', 'users'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $rules = [
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'expense_category_id' => 'required|exists:expense_categories,id',
            'date' => 'required|date',
        ];

        if ($user->hasRole('Administrator')) {
            $rules['user_id'] = 'required|exists:users,id';
        }

        $request->validate($rules);

        Expense::create([
            'user_id' => $user->hasRole('Administrator') ? $request->user_id : $user->id,
            'description' => $request->description,
            'amount' => $request->amount,
            'expense_category_id' => $request->expense_category_id,
            'date' => $request->date,
        ]);

        return response()->json(['success' => true, 'message' => 'Expense recorded successfully.']);
    }

    public function show(Expense $expense)
    {
        $this->authorizeAccess($expense);
        return response()->json($expense->load('category', 'user'));
    }

    public function update(Request $request, Expense $expense)
    {
        $this->authorizeAccess($expense);

        $user = auth()->user();

        $rules = [
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'expense_category_id' => 'required|exists:expense_categories,id',
            'date' => 'required|date',
        ];

        if ($user->hasRole('Administrator')) {
            $rules['user_id'] = 'required|exists:users,id';
        }

        $request->validate($rules);

        $expense->update([
            'description' => $request->description,
            'amount' => $request->amount,
            'expense_category_id' => $request->expense_category_id,
            'date' => $request->date,
            'user_id' => $user->hasRole('Administrator') ? $request->user_id : $expense->user_id,
        ]);

        return response()->json(['success' => true, 'message' => 'Expense updated successfully.']);
    }

    public function destroy(Expense $expense)
    {
        $this->authorizeAccess($expense);
        $expense->delete();

        return response()->json(['success' => true, 'message' => 'Expense deleted successfully.']);
    }

    private function authorizeAccess($expense)
    {
        $user = auth()->user();
        if (!$user->hasRole('Administrator') && $expense->user_id !== $user->id) {
            abort(403, 'Unauthorized');
        }
    }
}
