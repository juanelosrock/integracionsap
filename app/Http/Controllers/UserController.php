<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::with('profile')->latest()->paginate(15);
        return view('users.index', compact('users'));
    }

    public function show(User $user): View
    {
        $user->load('profile', 'roles', 'permissions');
        return view('users.show', compact('user'));
    }

    public function edit(User $user): View
    {
        $roles = Role::all();
        $user->load('profile', 'roles');
        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'email'     => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone'     => ['nullable', 'string', 'max:20'],
            'birthdate' => ['nullable', 'date'],
            'address'   => ['nullable', 'string', 'max:255'],
            'city'      => ['nullable', 'string', 'max:100'],
            'country'   => ['nullable', 'string', 'max:100'],
            'bio'       => ['nullable', 'string', 'max:1000'],
            'is_active' => ['boolean'],
            'roles'     => ['array'],
            'roles.*'   => ['exists:roles,name'],
        ]);

        $user->update($request->only('name', 'email'));

        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            $request->only('phone', 'birthdate', 'address', 'city', 'country', 'bio', 'is_active')
        );

        if ($request->has('roles')) {
            $user->syncRoles($request->input('roles'));
        }

        return redirect()->route('users.show', $user)->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(User $user): RedirectResponse
    {
        abort_if($user->id === auth()->id(), 403, 'No puedes eliminar tu propia cuenta.');

        $user->delete();

        return redirect()->route('users.index')->with('success', 'Usuario eliminado correctamente.');
    }

    public function toggleStatus(User $user): RedirectResponse
    {
        $profile = $user->profile()->firstOrCreate(['user_id' => $user->id]);
        $profile->update(['is_active' => !$profile->is_active]);

        $status = $profile->is_active ? 'activado' : 'desactivado';
        return back()->with('success', "Usuario {$status} correctamente.");
    }
}
