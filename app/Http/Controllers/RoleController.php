<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::all();
        return view('roles.index', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|unique:roles']);

        Role::create(['name' => $request->name]);

        return response()->json(['success' => true, 'message' => 'Role created successfully']);
    }

    public function show(Role $role)
    {
        return response()->json($role);
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name,' . $role->id,
        ]);

        $role->update(['name' => $request->name]);

        return response()->json(['success' => true, 'message' => 'Role updated successfully']);
    }

    public function destroy(Role $role)
    {
        if ($role->name === 'Administrator') {
            return response()->json(['success' => false, 'message' => 'Cannot delete Administrator role']);
        }

        $role->delete();

        return response()->json(['success' => true, 'message' => 'Role deleted successfully']);
    }
}
