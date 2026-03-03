<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;

class FuncaoAssignmentController extends Controller
{
    /**
     * Exibe o painel de atribuição de funções para os Líderes.
     */
    public function isSuperior($targetUser)
    {
        $user = auth()->user();
        if ($targetUser->hasRole('superadmin') && !$user->hasRole('superadmin')) {
            return true;
        }
        if ($user->hasRole('superadmin')) {
            return false; // Nobody is superior to superadmin
        }

        $permsThatControlMe = [];
        $myAssignPerms = [];

        // Coleta todas as permissões que controlariam MEUS cargos
        foreach ($user->roles as $myRole) {
            $permsThatControlMe[] = 'atribuir_' . \Illuminate\Support\Str::slug($myRole->name, '_');
        }

        // Verifica se o alvo tem o poder de me controlar
        foreach ($permsThatControlMe as $perm) {
            if (Permission::where('name', $perm)->where('guard_name', 'web')->exists()) {
                if ($targetUser->hasPermissionTo($perm)) {
                    return true; // Target user is my boss
                }
            }
        }

        // Verifica se somos PARES (Temos as mesmas permissões de atribuição)
        // Se as minhas permissões forem um subconjunto das permissões do alvo, o alvo é no mínimo meu Par.
        $minhasPermissoesDeAtribuir = $user->getAllPermissions()->filter(function ($p) {
            return str_starts_with($p->name, 'atribuir_');
        })->pluck('name')->toArray();

        $alvoPermissoesDeAtribuir = $targetUser->getAllPermissions()->filter(function ($p) {
            return str_starts_with($p->name, 'atribuir_');
        })->pluck('name')->toArray();

        if (empty($minhasPermissoesDeAtribuir)) {
            return true; // Failsafe: Se eu não tenho privilégios, não edito ninguém
        }

        // Calcula a diferença: O que EU posso gerenciar que o ALVO NÃO PODE?
        // Se vazio, tudo que eu faço, o alvo também faz. É meu par ou superior administrativo.
        $diferenca = array_diff($minhasPermissoesDeAtribuir, $alvoPermissoesDeAtribuir);
        
        if (empty($diferenca)) {
            return true; 
        }

        return false;
    }

    public function index()
    {
        $user = auth()->user();

        // 1. Descobrir quais cargos este usuário pode atribuir
        $permissoesDeAtribuicao = $user->getAllPermissions()->filter(function ($perm) {
            return str_starts_with($perm->name, 'atribuir_');
        });

        $rolesAllowedToAssign = [];
        foreach ($permissoesDeAtribuicao as $perm) {
            $roleSlug = substr($perm->name, 9);
            $role = Role::where('name', str_replace('_', ' ', $roleSlug))
                        ->orWhere('name', str_replace('_', '-', $roleSlug))
                        ->orWhere('name', $roleSlug)
                        ->first();
            
            if ($role && ($role->hierarquia == 0 || is_null($role->hierarquia))) {
                $rolesAllowedToAssign[] = $role;
            }
        }

        if (empty($rolesAllowedToAssign)) {
            return redirect()->route('dashboard')->with('error', 'Você não lidera nenhuma função específica para poder atribuir cargos.');
        }

        $allowedRoleNames = collect($rolesAllowedToAssign)->pluck('name')->toArray();

        // 2. Busca todos os usuários ativos (EXCETO O PRÓPRIO USUÁRIO) filtrando Chefes
        $alunosRaw = User::where('ativo', true)->where('id', '!=', $user->id)->get();
        $alunos = $alunosRaw->reject(function($aluno) {
            return $this->isSuperior($aluno);
        });

        // 3. Montar a lista e remover O Proprio Usuario e Seus Chefes da tabela de Revogação
        // ATUALIZAÇÃO: Para a tabela visual (Direita), queremos exibir todos que possuem o cargo,
        // mas na view vamos bloquear o botão de revogar se o alvo for Superior ou Par.
        $usuariosComCargos = User::whereHas('roles', function ($q) use ($allowedRoleNames) {
            $q->whereIn('name', $allowedRoleNames);
        })->with('roles')->get();

        $controller = $this; // Passando a instância para usar o isSuperior na View

        return view('pages.admin.funcoes_assign.index', compact('rolesAllowedToAssign', 'alunos', 'usuariosComCargos', 'allowedRoleNames', 'controller'));
    }

    /**
     * Processa a atribuição de uma Função a um ou mais Militares.
     */
    public function store(Request $request)
    {
        $request->validate([
            'aluno_id' => 'required|array',
            'aluno_id.*' => 'exists:users,id',
            'role_id' => 'required|exists:roles,id'
        ]);

        $roleToAssign = Role::findOrFail($request->role_id);
        
        // --- VALIDAÇÃO DE SEGURANÇA (O líder realmente tem poder para dar essa role?) ---
        // O slug esperado do poder é 'atribuir_' + slugDessaRole
        $expectedPerm = 'atribuir_' . \Illuminate\Support\Str::slug($roleToAssign->name, '_');
        
        // Superadmin ou se a conta dele tem a permissão
        if (!auth()->user()->hasRole('superadmin') && !auth()->user()->hasPermissionTo($expectedPerm)) {
            abort(403, "Você não possui a permissão ({$expectedPerm}) para distribuir o cargo de {$roleToAssign->name}.");
        }

        $alunos = User::whereIn('id', $request->aluno_id)->get();
        $count = 0;

        foreach ($alunos as $aluno) {
            // Segurança: Não permite atribuir cargo ao seu próprio chefe via bypass de HTML
            if ($this->isSuperior($aluno) || $aluno->id == auth()->id()) {
                continue;
            }

            if (!$aluno->hasRole($roleToAssign->name)) {
                $aluno->assignRole($roleToAssign->name);
                $count++;
            }
        }

        return redirect()->route('admin.funcoes.assign.index')->with('success', "Cargo '{$roleToAssign->name}' foi comissionado a {$count} militar(es) com sucesso!");
    }

    /**
     * Revoga a função de um usuário específico (Apenas se o líder gerenciar aquela função).
     */
    public function destroy(Request $request, User $user)
    {
        $request->validate([
            'role_name' => 'required|string|exists:roles,name'
        ]);

        $roleName = $request->role_name;

        // Validar permissão do Líder
        $expectedPerm = 'atribuir_' . \Illuminate\Support\Str::slug($roleName, '_');
        if (!auth()->user()->hasRole('superadmin') && !auth()->user()->hasPermissionTo($expectedPerm)) {
            abort(403, "Você não pode revogar um cargo que não gerencia.");
        }

        if ($this->isSuperior($user) || $user->id == auth()->id()) {
            abort(403, "Ação negada: Você não pode revogar cargos de si mesmo ou de um superior hierárquico na corporação.");
        }

        if ($user->hasRole($roleName)) {
            $user->removeRole($roleName);
            return redirect()->route('admin.funcoes.assign.index')->with('success', "O cargo '{$roleName}' foi revogado de {$user->nickname}.");
        }

        return redirect()->route('admin.funcoes.assign.index')->with('error', "O militar não possuía este cargo.");
    }
}
