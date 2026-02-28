<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role; // Usamos o mesmo model
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;

class FunctionRoleController extends Controller
{
    public function index()
    {
        // Pega apenas roles que NÃO são patentes (hierarquia 0 ou nula)
        $funcoes = Role::where('hierarquia', 0)->orWhereNull('hierarquia')->get();
        $permissions = Permission::all();
        return view('pages.admin.funcoes.index', compact('funcoes', 'permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name',
            'permissions' => 'nullable|array'
        ]);

        $funcao = Role::create(['name' => $request->name, 'hierarquia' => 0]); // Força hierarquia 0
        $funcao->syncPermissions($request->permissions ?? []);

        return redirect()->route('admin.funcoes.index')->with('success', 'Função criada com sucesso!');
    }

    public function update(Request $request, Role $funcao)
    {
        if ($funcao->hierarquia > 0) {
            return redirect()->route('admin.funcoes.index')->with('error', 'Não é possível atualizar uma patente através desta rota.');
        }
        $request->validate([
            'name' => 'required|string|unique:roles,name,' . $funcao->id,
            'permissions' => 'nullable|array'
        ]);

        $funcao->update(['name' => $request->name]);
        $funcao->syncPermissions($request->permissions ?? []);

        return redirect()->route('admin.funcoes.index')->with('success', 'Função atualizada com sucesso!');
    }

    public function destroy(Role $funcao)
    {
        if ($funcao->hierarquia > 0) {
            return redirect()->route('admin.funcoes.index')->with('error', 'Não é possível excluir uma patente através desta rota.');
        }
        if ($funcao->users()->count() > 0) {
            return redirect()->route('admin.funcoes.index')->with('error', 'Não é possível excluir uma função que está em uso.');
        }
        $funcao->delete();
        return redirect()->route('admin.funcoes.index')->with('success', 'Função excluída com sucesso!');
    }
}
