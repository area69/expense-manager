<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExpenseCategory;

class ExpenseCategoryController extends Controller
{
    public function index()
    {
        $categories = ExpenseCategory::latest()->get();
        return view('expense-categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        ExpenseCategory::create($request->only('name'));

        return response()->json(['success' => true, 'message' => 'Category created successfully.']);
    }

    public function show(ExpenseCategory $expenseCategory)
    {
        return response()->json($expenseCategory);
    }

    public function update(Request $request, ExpenseCategory $expenseCategory)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $expenseCategory->update($request->only('name'));

        return response()->json(['success' => true, 'message' => 'Category updated successfully.']);
    }

    public function destroy(ExpenseCategory $expenseCategory)
    {
        $expenseCategory->delete();
        return response()->json(['success' => true, 'message' => 'Category deleted successfully.']);
    }
}
