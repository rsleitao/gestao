<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PermissionController extends Controller
{
    /**
     * Ecrã de gestão de permissões por papel (apenas CEO).
     */
    public function index(): View
    {
        $roles = Role::orderBy('id')->with('permissions')->get();
        $permissions = Permission::orderBy('id')->get();

        return view('permissions.index', compact('roles', 'permissions'));
    }

    /**
     * Gravar todas as permissões (matriz completa) num único pedido.
     */
    public function updateAll(Request $request): RedirectResponse
    {
        $roles = Role::orderBy('id')->get();
        $rules = [];
        foreach ($roles as $role) {
            if ($role->isCEO()) {
                continue;
            }
            $rules["roles.{$role->id}"] = 'array';
            $rules["roles.{$role->id}.*"] = 'integer|exists:permissions,id';
        }
        $validated = $request->validate($rules);

        foreach ($roles as $role) {
            if ($role->isCEO()) {
                continue;
            }
            $role->permissions()->sync($validated['roles'][$role->id] ?? []);
        }

        return redirect()->route('permissions.index')
            ->with('success', 'Permissões gravadas com sucesso.');
    }
}
