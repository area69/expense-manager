@extends('layouts.app')

@section('content')
<div class="p-6 space-y-6">

    <!-- Page Header -->
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-white">User Management</h1>
        <button onclick="openCreateModal()" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
            + Add Role
        </button>
    </div>


    <!-- Alert -->
    <div id="alert" class="hidden p-3 rounded text-white"></div>

    <!-- User Table -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-x-auto">
        <table id="userTable" class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
            <thead class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 uppercase text-xs">
                <tr>
                    <th class="px-6 py-3 text-left">Name</th>
                    <th class="px-6 py-3 text-left">Email</th>
                    <th class="px-6 py-3 text-left">Role</th>
                    <th class="px-6 py-3 text-left">Date Created</th>
                    <th class="px-6 py-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @foreach ($users as $user)
                <tr data-id="{{ $user->id }}">
                    <td class="px-6 py-4">{{ $user->name }}</td>
                    <td class="px-6 py-4">{{ $user->email }}</td>
                    <td class="px-6 py-4">{{ $user->roles->first()->name ?? '-' }}</td>
                    <td class="px-6 py-4">{{ $user->created_at }}</td>
                    <td class="px-6 py-4 space-x-3">
                        <button onclick="editUser({{ $user->id }})" class="text-blue-600 hover:underline">Edit</button>
                        <button onclick="deleteUser({{ $user->id }})" class="text-red-600 hover:underline">Delete</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>

<!-- Create/Edit Modal -->
<div id="userModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white dark:bg-gray-900 w-full max-w-md p-6 rounded shadow">
        <h2 id="modalTitle" class="text-lg font-semibold mb-4 text-gray-800 dark:text-white">Create User</h2>
        <form id="userForm">
            @csrf
            <input type="hidden" name="id" id="userId">

            <div class="mb-4">
                <label class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
                <input type="text" name="name" id="name" class="w-full border border-gray-300 dark:border-gray-600 rounded p-2 dark:bg-gray-800 dark:text-white">
            </div>
            <div class="mb-4">
                <label class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                <input type="email" name="email" id="email" class="w-full border border-gray-300 dark:border-gray-600 rounded p-2 dark:bg-gray-800 dark:text-white">
            </div>
            <div class="mb-4">
                <label class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">Password <span id="passNote" class="text-xs text-gray-400">(leave blank to keep)</span></label>
                <input type="password" name="password" id="password" class="w-full border border-gray-300 dark:border-gray-600 rounded p-2 dark:bg-gray-800 dark:text-white">
            </div>
            <div class="mb-4">
                <label class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">Role</label>
                <select name="role" id="role" class="w-full border border-gray-300 dark:border-gray-600 rounded p-2 dark:bg-gray-800 dark:text-white">
                    @foreach ($roles as $role)
                        <option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>
                    @endforeach
                </select>
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
<!-- DataTables CDN -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script>
$(document).ready(function () {
    $('#userTable').DataTable();
});

function showAlert(msg, color = 'bg-green-500') {
    const alert = document.getElementById('alert');
    alert.textContent = msg;
    alert.className = `${color} block p-3 rounded text-white`;
    setTimeout(() => alert.classList.add('hidden'), 3000);
}

function openCreateModal() {
    document.getElementById('userForm').reset();
    document.getElementById('userId').value = '';
    document.getElementById('modalTitle').textContent = 'Create User';
    document.getElementById('passNote').style.display = 'none';
    document.getElementById('userModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('userModal').classList.add('hidden');
}

function editUser(id) {
    fetch(`/users/${id}`)
        .then(res => res.json())
        .then(user => {
            document.getElementById('userId').value = user.id;
            document.getElementById('name').value = user.name;
            document.getElementById('email').value = user.email;
            document.getElementById('role').value = user.roles[0]?.name;
            document.getElementById('password').value = '';
            document.getElementById('modalTitle').textContent = 'Edit User';
            document.getElementById('passNote').style.display = 'inline';
            document.getElementById('userModal').classList.remove('hidden');
        });
}

function deleteUser(id) {
    if (!confirm('Delete this user?')) return;
    fetch(`/users/${id}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
    })
    .then(res => res.json())
    .then(data => {
        showAlert(data.message);
        setTimeout(() => location.reload(), 1000);
    });
}

// Handle form submission
document.getElementById('userForm').addEventListener('submit', function (e) {
    e.preventDefault();
    const formData = new FormData(this);
    const id = formData.get('id');
    const isUpdate = !!id;
    const url = isUpdate ? `/users/${id}` : '/users';

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
    .catch(err => {
        console.error(err);
        showAlert('Failed to save user.', 'bg-red-500');
    });
});
</script>
@endpush
