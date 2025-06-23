@extends('layouts.app')

@section('content')
<div class="p-6 space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-white">Role Management</h1>
        <button onclick="openCreateModal()" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
            + Add Role
        </button>
    </div>

    <div id="alert" class="hidden p-3 rounded text-white"></div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-x-auto mt-4">
        <table id="roleTable" class="min-w-full text-sm">
            <thead class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200">
                <tr>
                    <th class="px-4 py-2 text-left">Role Name</th>
                    <th class="px-4 py-2 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($roles as $role)
                <tr data-id="{{ $role->id }}">
                    <td class="px-4 py-2">{{ $role->name }}</td>
                    <td class="px-4 py-2 space-x-2">
                        <button onclick="editRole({{ $role->id }})" class="text-blue-600 hover:underline">Edit</button>
                        <button onclick="deleteRole({{ $role->id }})" class="text-red-600 hover:underline">Delete</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Create/Edit Modal -->
<div id="roleModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white dark:bg-gray-900 w-full max-w-md p-6 rounded shadow">
        <h2 id="modalTitle" class="text-lg font-semibold mb-4 text-gray-800 dark:text-white">Create Role</h2>
        <form id="roleForm">
            @csrf
            <input type="hidden" name="id" id="roleId">
            <div class="mb-4">
                <label class="block text-sm text-gray-700 dark:text-gray-300">Role Name</label>
                <input type="text" name="name" id="roleName" class="w-full border border-gray-300 dark:border-gray-600 rounded p-2 dark:bg-gray-800 dark:text-white">
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
<!-- DataTables + jQuery -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script>
$(document).ready(function () {
    $('#roleTable').DataTable();
});

function showAlert(msg, color = 'bg-green-500') {
    const alert = document.getElementById('alert');
    alert.textContent = msg;
    alert.className = `${color} block p-3 rounded text-white`;
    setTimeout(() => alert.classList.add('hidden'), 3000);
}

function openCreateModal() {
    document.getElementById('roleForm').reset();
    document.getElementById('roleId').value = '';
    document.getElementById('modalTitle').textContent = 'Create Role';
    document.getElementById('roleModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('roleModal').classList.add('hidden');
}

function editRole(id) {
    fetch(`/roles/${id}`)
        .then(res => res.json())
        .then(role => {
            document.getElementById('roleId').value = role.id;
            document.getElementById('roleName').value = role.name;
            document.getElementById('modalTitle').textContent = 'Edit Role';
            document.getElementById('roleModal').classList.remove('hidden');
        });
}

function deleteRole(id) {
    if (!confirm('Delete this role?')) return;
    fetch(`/roles/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(res => res.json())
    .then(data => {
        showAlert(data.message);
        setTimeout(() => location.reload(), 1000);
    });
}

document.getElementById('roleForm').addEventListener('submit', function (e) {
    e.preventDefault();
    const formData = new FormData(this);
    const id = formData.get('id');
    const isUpdate = !!id;
    const url = isUpdate ? `/roles/${id}` : '/roles';
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
        showAlert('Failed to save role.', 'bg-red-500');
    });
});
</script>
@endpush
