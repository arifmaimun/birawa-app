<?php

namespace App\Http\Controllers\Manual\Management;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        // Simple authorization check (adjust as needed)
        if (Auth::user()->role !== 'superadmin' && !Auth::user()->hasRole('superadmin')) {
             abort(403, 'Unauthorized');
        }

        $query = User::query();

        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
        }

        $users = $query->latest()->paginate(10);

        return view('manual.management.users.index', compact('users'));
    }

    public function create()
    {
        if (Auth::user()->role !== 'superadmin' && !Auth::user()->hasRole('superadmin')) {
             abort(403, 'Unauthorized');
        }

        $roles = Role::orderBy('name')->get();
        return view('manual.management.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        if (Auth::user()->role !== 'superadmin' && !Auth::user()->hasRole('superadmin')) {
             abort(403, 'Unauthorized');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string', // The 'role' column
            'roles' => 'array', // Spatie roles
            'roles.*' => 'exists:roles,id',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role, // Main role column
            'email_verified_at' => now(), // Auto-verify for manual creation
        ]);

        if ($request->has('roles')) {
            $user->syncRoles($request->roles);
        }

        return redirect()->route('manual.users.index')
            ->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        if (Auth::user()->role !== 'superadmin' && !Auth::user()->hasRole('superadmin')) {
             abort(403, 'Unauthorized');
        }

        $roles = Role::orderBy('name')->get();
        return view('manual.management.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        if (Auth::user()->role !== 'superadmin' && !Auth::user()->hasRole('superadmin')) {
             abort(403, 'Unauthorized');
        }

        $rules = [
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|string',
            'roles' => 'array',
            'roles.*' => 'exists:roles,id',
        ];

        $request->validate($rules);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        if ($request->has('roles')) {
            $user->syncRoles($request->roles);
        }

        return redirect()->route('manual.users.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if (Auth::user()->role !== 'superadmin' && !Auth::user()->hasRole('superadmin')) {
             abort(403, 'Unauthorized');
        }

        if ($user->id === Auth::id()) {
            return back()->with('error', 'Cannot delete yourself.');
        }

        $user->delete();

        return redirect()->route('manual.users.index')
            ->with('success', 'User deleted successfully.');
    }
}
