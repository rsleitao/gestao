<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::query()->with('role')->orderBy('name');

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($qry) use ($q) {
                $qry->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            });
        }

        $users = $query->paginate(15)->withQueryString();

        return view('users.index', compact('users'));
    }

    public function create(): View
    {
        $roles = Role::orderBy('id')->get();

        return view('users.create', compact('roles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'cc' => 'nullable|string|max:20',
            'nif' => 'nullable|string|max:20',
            'dgeg' => 'nullable|string|max:50',
            'oet' => 'nullable|string|max:50',
            'oe' => 'nullable|string|max:50',
            'id_role' => 'nullable|integer|exists:roles,id',
            'ativo' => 'boolean',
            'must_change_password' => 'boolean',
        ]);

        $validated['ativo'] = $request->boolean('ativo');
        $validated['must_change_password'] = $request->boolean('must_change_password');

        User::create($validated);

        return redirect()->route('users.index')->with('success', 'Utilizador criado com sucesso.');
    }

    public function edit(User $user): View
    {
        $roles = Role::orderBy('id')->get();

        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'cc' => 'nullable|string|max:20',
            'nif' => 'nullable|string|max:20',
            'dgeg' => 'nullable|string|max:50',
            'oet' => 'nullable|string|max:50',
            'oe' => 'nullable|string|max:50',
            'id_role' => 'nullable|integer|exists:roles,id',
            'ativo' => 'boolean',
            'must_change_password' => 'boolean',
        ];

        if ($request->filled('password')) {
            $rules['password'] = 'nullable|string|min:8|confirmed';
        }

        $validated = $request->validate($rules);

        if (! $request->filled('password')) {
            unset($validated['password']);
        }

        $validated['ativo'] = $request->boolean('ativo');
        $validated['must_change_password'] = $request->boolean('must_change_password');

        if ($user->isFixedCeo()) {
            $validated['id_role'] = $user->id_role;
            $validated['ativo'] = true;
        }

        $user->update($validated);

        return redirect()->route('users.index')->with('success', 'Utilizador atualizado com sucesso.');
    }
}
