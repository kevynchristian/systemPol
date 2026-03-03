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
            'permissions' => 'nullable|array',
            'color' => 'nullable|string|max:7'
        ]);

        $funcao = Role::create([
            'name' => $request->name, 
            'hierarquia' => 0,
            'color' => $request->color ?? '#6c757d'
        ]); // Força hierarquia 0 e cor padrão se nulo
        $funcao->syncPermissions($request->permissions ?? []);

        // --- SISTEMA DE DELEGAÇÃO ---
        // Cria automaticamente a permissão para que outros possam atribuir esta função no futuro
        $permNome = 'atribuir_' . \Illuminate\Support\Str::slug($request->name, '_');
        Permission::firstOrCreate(['name' => $permNome]);

        return redirect()->route('admin.funcoes.index')->with('success', 'Função criada com sucesso! (Permissão de delegação ' . $permNome . ' foi gerada automaticamente).');
    }

    public function update(Request $request, $id)
    {
        $funcao = Role::findOrFail($id);

        if ($funcao->hierarquia > 0) {
            return redirect()->route('admin.funcoes.index')->with('error', 'Não é possível atualizar uma patente através desta rota.');
        }
        $request->validate([
            'name' => 'required|string|unique:roles,name,' . $funcao->id,
            'permissions' => 'nullable|array',
            'color' => 'nullable|string|max:7'
        ]);

        // Salva o nome antigo para caso precisemos renomear a permissão delegada
        $oldSlug = 'atribuir_' . \Illuminate\Support\Str::slug($funcao->name, '_');
        
        $funcao->update([
            'name' => $request->name,
            'color' => $request->color
        ]);
        $funcao->syncPermissions($request->permissions ?? []);

        // Se o nome mudou, atualiza também a permissão atrelada para não quebrar o vínculo lógico
        $newSlug = 'atribuir_' . \Illuminate\Support\Str::slug($request->name, '_');
        if ($oldSlug !== $newSlug) {
            $existingPerm = Permission::where('name', $oldSlug)->first();
            if ($existingPerm) {
                $existingPerm->update(['name' => $newSlug]);
            } else {
                Permission::firstOrCreate(['name' => $newSlug]);
            }
        }

        return redirect()->route('admin.funcoes.index')->with('success', 'Função atualizada com sucesso!');
    }

    public function destroy($id)
    {
        $funcao = Role::findOrFail($id);

        if ($funcao->hierarquia > 0) {
            return redirect()->route('admin.funcoes.index')->with('error', 'Não é possível excluir uma patente através desta rota.');
        }
        if ($funcao->users()->count() > 0) {
            return redirect()->route('admin.funcoes.index')->with('error', 'Não é possível excluir uma função que está em uso.');
        }
        $permSlug = 'atribuir_' . \Illuminate\Support\Str::slug($funcao->name, '_');
        $funcao->delete();
        
        // Exclui a permissão atrelada para manter o banco limpo
        Permission::where('name', $permSlug)->delete();

        return redirect()->route('admin.funcoes.index')->with('success', 'Função e suas permissões de delegação excluídas com sucesso!');
    }
}
