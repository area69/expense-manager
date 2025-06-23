<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $this->authorizeAdmin(); // Check manually

        $users = User::with('roles')->get();
        $roles = Role::all();
        return view('users.index', compact('users', 'roles'));
    }

    public function store(Request $request)
    {
        $this->authorizeAdmin();

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role'     => 'required|exists:roles,name'
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole($request->role);

        return response()->json(['success' => true, 'message' => 'User created successfully']);
    }

    public function show(User $user)
    {
        $this->authorizeAdmin();
        return response()->json($user->load('roles'));
    }

    public function update(Request $request, User $user)
    {
        $this->authorizeAdmin();

        if ($user->hasRole('Administrator') && $request->role !== 'Administrator') {
            return response()->json(['success' => false, 'message' => 'Cannot change Administrator role']);
        }

        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role'  => 'required|exists:roles,name'
        ]);

        $user->update([
            'name'  => $request->name,
            'email' => $request->email,
        ]);

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        $user->syncRoles([$request->role]);

        return response()->json(['success' => true, 'message' => 'User updated successfully']);
    }

    public function destroy(User $user)
    {
        $this->authorizeAdmin();

        if ($user->hasRole('Administrator')) {
            return response()->json(['success' => false, 'message' => 'Cannot delete Administrator']);
        }

        $user->delete();
        return response()->json(['success' => true, 'message' => 'User deleted successfully']);
    }

    /**
     * Protect route actions from non-admins.
     */
    private function authorizeAdmin()
    {
        if (!auth()->user() || !auth()->user()->hasRole('Administrator')) {
            abort(403, 'Unauthorized action.');
        }
    }
}
