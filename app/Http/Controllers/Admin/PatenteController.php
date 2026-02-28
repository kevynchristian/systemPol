<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role; // Nosso model de Role customizado
use Spatie\Permission\Models\Permission;

class PatenteController extends Controller
{
    /**
     * Mostra a lista de patentes e o formulário de criação.
     */
    public function index()
    {
        // AQUI: Pega apenas roles que SÃO patentes (hierarquia > 0)
        $patentes = Role::where('hierarquia', '>', 0)
            ->with('permissions')
            ->orderBy('hierarquia', 'asc')
            ->get();

        // O dropdown de sincronização também só deve mostrar patentes
        $rolesParaSincronizar = Role::where('hierarquia', '>', 0)->get();

        $permissions = Permission::all();

        $rolesWithPermissions = $patentes->mapWithKeys(function ($role) {
            return [$role->id => $role->permissions->pluck('name')];
        });

        // Passamos a variável 'patentes' para a view
        return view('pages.admin.patentes.index', compact('patentes', 'permissions', 'rolesWithPermissions', 'rolesParaSincronizar'));
    }

    /**
     * Salva uma nova patente no banco de dados.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name',
            'hierarquia' => 'required|integer|min:0',
            'permissions' => 'nullable|array',
            'sync_with_role_id' => 'nullable|exists:roles,id'
        ]);

        $role = Role::create([
            'name' => $request->name,
            'hierarquia' => $request->hierarquia,
            'sync_with_role_id' => $request->sync_with_role_id,
        ]);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return redirect()->route('admin.patentes.index')->with('success', 'Patente criada com sucesso!');
    }

    /**
     * Atualiza uma patente existente.
     */
    public function update(Request $request, Role $patente)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|unique:roles,name,' . $patente->id,
            'hierarquia' => 'required|integer|min:0',
            'permissions' => 'nullable|array',
            'sync_with_role_id' => 'nullable|exists:roles,id'
        ]);


        $patente->update($validatedData);

        $patente->refresh();

        if (!$request->sync_with_role_id) {
            $patente->syncPermissions($request->permissions ?? []);
        }

        return redirect()->route('admin.patentes.index')->with('success', 'Patente atualizada com sucesso!');
    }

    /**
     * Deleta uma patente.
     */
    public function destroy(Role $patente)
    {
        if ($patente->users()->count() > 0) {
            return redirect()->route('admin.patentes.index')->with('error', '...');
        }
        $patente->delete();
        return redirect()->route('admin.patentes.index')->with('success', 'Patente excluída com sucesso!');
    }
}
