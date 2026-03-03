<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    /**
     * Exibe a página de gerenciamento de permissões.
     */
    public function index()
    {
        $permissions = Permission::orderBy('id', 'asc')->get();
        return view('pages.admin.permissoes.index', compact('permissions'));
    }

    /**
     * Salva uma nova permissão no banco de dados.
     */
    public function store(Request $request)
    {
        $request->validate([
            // Regra de validação: obrigatório e único na tabela de permissões
            'name' => 'required|string|unique:permissions,name',
        ]);

        Permission::create(['name' => $request->name]);

        return redirect()->route('admin.permissoes.index')->with('success', 'Permissão criada com sucesso!');
    }

    /**
     * Remove uma permissão do banco de dados.
     */
    public function destroy(Permission $permission)
    {
        $roleCount = $permission->roles()->count();

        if ($roleCount > 0) {
            return redirect()->route('admin.permissoes.index')
                ->with('error', 'Não é possível excluir uma permissão que já está atribuída a um ou mais cargos.');
        }

        $permission->delete();



        return redirect()->route('admin.permissoes.index')->with('success', 'Permissão excluída com sucesso!');
    }

    /**
     * Remove esta permissão de todos os cargos que a possuam.
     */
    public function reset(Permission $permission)
    {
        // Pega todas as roles (patentes e funções) que têm esta permissão
        $roles = $permission->roles()->get();
        
        foreach ($roles as $role) {
            $role->revokePermissionTo($permission);
        }

        return redirect()->route('admin.permissoes.index')
            ->with('success', "A permissão '{$permission->name}' foi removida de {$roles->count()} cargo(s) com sucesso!");
    }
}
