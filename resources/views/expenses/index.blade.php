@extends('layouts.app')

@section('content')
<div class="p-6 space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-white">Expense List</h1>
        <button onclick="openCreateModal()" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">+ Add Expense</button>
    </div>

    <div id="alert" class="hidden p-3 rounded text-white"></div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-x-auto">
        <table id="expenseTable" class="min-w-full text-sm">
            <thead class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200">
                <tr>
                    <th class="px-4 py-2">User</th>
                    <th class="px-4 py-2">Description</th>
                    <th class="px-4 py-2">Amount</th>
                    <th class="px-4 py-2">Category</th>
                    <th class="px-4 py-2">Date</th>
                    <th class="px-4 py-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($expenses as $expense)
                <tr data-id="{{ $expense->id }}">
                    <td class="px-4 py-2">{{ $expense->user->name ?? 'N/A' }}</td>
                    <td class="px-4 py-2">{{ $expense->description }}</td>
                    <td class="px-4 py-2">₱{{ number_format($expense->amount, 2) }}</td>
                    <td class="px-4 py-2">{{ $expense->category->name }}</td>
                    <td class="px-4 py-2">{{ $expense->date }}</td>
                    <td class="px-4 py-2 space-x-2">
                        <button onclick="editExpense({{ $expense->id }})" class="text-blue-600 hover:underline">Edit</button>
                        <button onclick="deleteExpense({{ $expense->id }})" class="text-red-600 hover:underline">Delete</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Modal -->
<div id="expenseModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white dark:bg-gray-900 w-full max-w-md p-6 rounded shadow">
        <h2 id="modalTitle" class="text-lg font-semibold mb-4 text-gray-800 dark:text-white">Create Expense</h2>
        <form id="expenseForm">
            @csrf
            <input type="hidden" name="id" id="expenseId">

            @if(auth()->user()->hasRole('Administrator'))
            <div class="mb-4">
                <label class="block text-sm text-gray-700 dark:text-gray-300">User</label>
                <select name="user_id" id="user_id" class="w-full border rounded p-2 dark:bg-gray-800 dark:text-white">
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            <div class="mb-4">
                <label class="block text-sm text-gray-700 dark:text-gray-300">Description</label>
                <input type="text" name="description" id="description" class="w-full border rounded p-2 dark:bg-gray-800 dark:text-white">
            </div>

            <div class="mb-4">
                <label class="block text-sm text-gray-700 dark:text-gray-300">Amount</label>
                <input type="number" step="0.01" name="amount" id="amount" class="w-full border rounded p-2 dark:bg-gray-800 dark:text-white">
            </div>

            <div class="mb-4">
                <label class="block text-sm text-gray-700 dark:text-gray-300">Category</label>
                <select name="expense_category_id" id="expense_category_id" class="w-full border rounded p-2 dark:bg-gray-800 dark:text-white">
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm text-gray-700 dark:text-gray-300">Date</label>
                <input type="date" name="date" id="date" class="w-full border rounded p-2 dark:bg-gray-800 dark:text-white">
            </div>

            <div class="flex justify-between">
                <button type="button" onclick="closeModal()" class="text-gray-500 hover:underline">Cancel</button>
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Save</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script>
$(document).ready(() => $('#expenseTable').DataTable());

function showAlert(msg, color = 'bg-green-500') {
    const alert = document.getElementById('alert');
    alert.textContent = msg;
    alert.className = `${color} block p-3 rounded text-white`;
    setTimeout(() => alert.classList.add('hidden'), 3000);
}

function openCreateModal() {
    document.getElementById('expenseForm').reset();
    document.getElementById('expenseId').value = '';
    document.getElementById('modalTitle').textContent = 'Create Expense';
    document.getElementById('expenseModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('expenseModal').classList.add('hidden');
}

function editExpense(id) {
    fetch(`/expenses/${id}`)
        .then(res => res.json())
        .then(data => {
            document.getElementById('expenseId').value = data.id;
            document.getElementById('description').value = data.description;
            document.getElementById('amount').value = data.amount;
            document.getElementById('expense_category_id').value = data.expense_category_id;
            document.getElementById('date').value = data.date;
            if (document.getElementById('user_id')) {
                document.getElementById('user_id').value = data.user_id;
            }
            document.getElementById('modalTitle').textContent = 'Edit Expense';
            document.getElementById('expenseModal').classList.remove('hidden');
        });
}

function deleteExpense(id) {
    if (!confirm('Delete this expense?')) return;
    fetch(`/expenses/${id}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
    })
    .then(res => res.json())
    .then(data => {
        showAlert(data.message);
        setTimeout(() => location.reload(), 1000);
    });
}

document.getElementById('expenseForm').addEventListener('submit', function (e) {
    e.preventDefault();
    const formData = new FormData(this);
    const id = formData.get('id');
    const isUpdate = !!id;
    const url = isUpdate ? `/expenses/${id}` : '/expenses';

    if (isUpdate) formData.append('_method', 'PUT');

    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showAlert(data.message);
            closeModal();
            setTimeout(() => location.reload(), 1000);
        } else {
            showAlert(data.message || 'Something went wrong', 'bg-red-500');
        }
    })
    .catch(() => {
        showAlert('Failed to save expense.', 'bg-red-500');
    });
});
</script>
@endpush
