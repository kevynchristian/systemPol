<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Script;
use App\Models\ScriptCategory;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;

class ScriptController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        // Se o usuário tem apenas a role 'Recruta', bloqueia acesso
        if ($user->roles->count() === 1 && $user->hasRole('Recruta')) {
            return redirect()->route('dashboard')->with('error', 'Acesso negado.');
        }

        $canManage = $user->hasPermissionTo('gerenciar_scripts') || $user->hasRole('superadmin');

        
        $categoriesQuery = ScriptCategory::with(['scripts.creator', 'scripts.editor']);

        if (!$canManage) {
            // Se não pode gerenciar (membro normal), vê apenas scripts que pertençam a uma de suas roles (divisões) ou globais (null)
            $userRoleIds = $user->roles->pluck('id')->toArray();
            $categoriesQuery->whereIn('role_id', $userRoleIds)->orWhereNull('role_id');
        }

        $categories = $categoriesQuery->get();
        // Buscar todas as roles (divisões) para o formulário de criar categoria, se puder gerenciar
        $roles = $canManage ? Role::orderBy('name')->get() : collect();

        return view('scripts.index', compact('categories', 'canManage', 'roles'));
    }

    public function storeCategory(Request $request)
    {
        $user = Auth::user();
        if (!$user->hasPermissionTo('gerenciar_scripts') && !$user->hasRole('superadmin')) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'role_id' => 'nullable|exists:roles,id',
            'description' => 'nullable|string'
        ]);

        ScriptCategory::create($request->all());

        return redirect()->back()->with('success', 'Categoria criada com sucesso!');
    }

    public function storeScript(Request $request)
    {
        $user = Auth::user();
        if (!$user->hasPermissionTo('gerenciar_scripts') && !$user->hasRole('superadmin')) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:script_categories,id',
            'content' => 'required|string'
        ]);

        Script::create([
            'title' => $request->title,
            'category_id' => $request->category_id,
            'content' => $request->content,
            'created_by' => $user->id,
            'ip_address' => $request->ip()
        ]);

        return redirect()->back()->with('success', 'Script cadastrado com sucesso!');
    }

    public function updateScript(Request $request, Script $script)
    {
        $user = Auth::user();
        if (!$user->hasPermissionTo('gerenciar_scripts') && !$user->hasRole('superadmin')) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:script_categories,id',
            'content' => 'required|string'
        ]);

        $script->update(array_merge(
            $request->only(['title', 'category_id', 'content']),
            [
                'ip_address' => $request->ip(),
                'updated_by' => $user->id
            ]
        ));

        return redirect()->back()->with('success', 'Script atualizado com sucesso!');
    }

    public function destroyScript(Script $script)
    {
        $user = Auth::user();
        if (!$user->hasPermissionTo('gerenciar_scripts') && !$user->hasRole('superadmin')) {
            abort(403);
        }

        $script->delete();

        return redirect()->back()->with('success', 'Script excluído com sucesso!');
    }
}
