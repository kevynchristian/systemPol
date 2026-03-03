<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Str;
use App\Models\PromocaoLog;

class PromocaoController extends Controller
{
    /**
     * Gera as permissões vinculadas às patentes que possam estar faltando (retroativo)
     */
    private function autoGeneratePermissions()
    {
        $patentes = Role::where('hierarquia', '>', 0)->get();
        foreach ($patentes as $patente) {
            $slug = Str::slug($patente->name, '_');
            Permission::firstOrCreate(['name' => 'promove_' . $slug, 'guard_name' => 'web']);
            Permission::firstOrCreate(['name' => 'rebaixa_' . $slug, 'guard_name' => 'web']);
            Permission::firstOrCreate(['name' => 'exonera_' . $slug, 'guard_name' => 'web']);
        }
    }

    /**
     * Retorna a Role (Patente) de maior hierarquia que este usuário pode promover.
     * Retorna NULL se ele não puder promover ninguém.
     */
    public static function getPromotionCeiling()
    {
        $user = auth()->user();
        if (!$user) return null;

        if ($user->hasRole('superadmin')) {
            // Superadmin pode promover até a maior patente
            return Role::where('hierarquia', '>', 0)->orderByDesc('hierarquia')->first();
        }

        $maxRole = null;
        $maxHierarquia = -1;

        $permissions = $user->getAllPermissions();
        $patentes = Role::where('hierarquia', '>', 0)->get();

        foreach ($permissions as $permission) {
            if (Str::startsWith($permission->name, 'promove_')) {
                $slug = str_replace('promove_', '', $permission->name);
                
                foreach ($patentes as $patente) {
                    if (Str::slug($patente->name, '_') === $slug) {
                        if ($patente->hierarquia > $maxHierarquia) {
                            $maxHierarquia = $patente->hierarquia;
                            $maxRole = $patente;
                        }
                    }
                }
            }
        }

        // dd($maxRole);
        return $maxRole;
    }

    /**
     * Retorna a Role (Patente) de maior hierarquia que este usuário pode rebaixar.
     */
    public static function getDemotionCeiling()
    {
        $user = auth()->user();
        if (!$user) return null;

        if ($user->hasRole('superadmin')) {
            return Role::where('hierarquia', '>', 0)->orderByDesc('hierarquia')->first();
        }

        $maxRole = null;
        $maxHierarquia = -1;

        $permissions = $user->getAllPermissions();
        $patentes = Role::where('hierarquia', '>', 0)->get();

        foreach ($permissions as $permission) {
            if (Str::startsWith($permission->name, 'rebaixa_')) {
                $slug = str_replace('rebaixa_', '', $permission->name);
                
                foreach ($patentes as $patente) {
                    if (Str::slug($patente->name, '_') === $slug) {
                        if ($patente->hierarquia > $maxHierarquia) {
                            $maxHierarquia = $patente->hierarquia;
                            $maxRole = $patente;
                        }
                    }
                }
            }
        }

        return $maxRole;
    }

    /**
     * Retorna a Role (Patente) de maior hierarquia que este usuário pode exonerar.
     */
    public static function getExonerationCeiling()
    {
        $user = auth()->user();
        if (!$user) return null;

        if ($user->hasRole('superadmin')) {
            return Role::where('hierarquia', '>', 0)->orderByDesc('hierarquia')->first();
        }

        $maxRole = null;
        $maxHierarquia = -1;

        $permissions = $user->getAllPermissions();
        $patentes = Role::where('hierarquia', '>', 0)->get();

        foreach ($permissions as $permission) {
            if (Str::startsWith($permission->name, 'exonera_')) {
                $slug = str_replace('exonera_', '', $permission->name);
                
                foreach ($patentes as $patente) {
                    if (Str::slug($patente->name, '_') === $slug) {
                        if ($patente->hierarquia > $maxHierarquia) {
                            $maxHierarquia = $patente->hierarquia;
                            $maxRole = $patente;
                        }
                    }
                }
            }
        }

        return $maxRole;
    }

    public function index()
    {
        $this->autoGeneratePermissions();

        $ceilingPatente = self::getPromotionCeiling();
        $demotionCeiling = self::getDemotionCeiling();
        $exonerationCeiling = self::getExonerationCeiling();

        // Recupera operadores ativos (menos o próprio usuário logado)
        $usersRaw = User::where('ativo', true)->where('id', '!=', auth()->id())->with('roles')->get();

        $subordinados = collect(); // Para promoção
        $subordinadosDemote = collect(); // Para rebaixamento
        $subordinadosExonerate = collect(); // Para exoneração

        foreach ($usersRaw as $user) {
            $userPatente = $user->roles->where('hierarquia', '>', 0)->sortByDesc('hierarquia')->first();
            $targetHierarquia = $userPatente ? $userPatente->hierarquia : 0;

            // Attach helper properties once
            $user->highest_patente = $userPatente;
            $user->highest_patente_id = $userPatente ? $userPatente->id : null;
            $user->highest_patente_nome = $userPatente ? $userPatente->name : 'Sem Patente';

            // Alvos de Promoção (Abaixo do Teto de Promoção)
            if ($ceilingPatente && $targetHierarquia < $ceilingPatente->hierarquia) {
                $subordinados->push($user);
            }

            // Alvos de Rebaixamento (Abaixo do Teto de Rebaixamento E que já têm alguma patente)
            if ($demotionCeiling && $targetHierarquia < $demotionCeiling->hierarquia && $targetHierarquia > 0) {
                $subordinadosDemote->push($user);
            }

            // Alvos de Exoneração (Abaixo do Teto de Exoneração E que já têm alguma patente)
            if ($exonerationCeiling && $targetHierarquia < $exonerationCeiling->hierarquia && $targetHierarquia > 0) {
                $subordinadosExonerate->push($user);
            }
        }

        // Se ele não tiver NENHUM teto (nem promove, nem rebaixa, nem exonera), barra o acesso.
        if (!$ceilingPatente && !$demotionCeiling && !$exonerationCeiling) {
            abort(403, 'Você não possui permissões para gerenciar patentes (promover, rebaixar ou exonerar).');
        }

        // Patentes disponíveis no dropdown (Apenas para visualização no JS)
        $patentesDisponiveis = Role::where('hierarquia', '>', 0)->orderBy('hierarquia', 'asc')->get();

        return view('pages.admin.promocoes.index', compact(
            'subordinados', 'subordinadosDemote', 'subordinadosExonerate',
            'patentesDisponiveis', 'ceilingPatente', 'demotionCeiling', 'exonerationCeiling'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'new_role_id' => 'required|exists:roles,id',
            'description' => 'required|string|max:500'
        ]);

        $ceilingPatente = self::getPromotionCeiling();
        if (!$ceilingPatente) abort(403);

        $targetUser = User::with('roles')->findOrFail($request->user_id);
        $targetCurrentPatente = $targetUser->roles->where('hierarquia', '>', 0)->sortByDesc('hierarquia')->first();
        $targetHierarquia = $targetCurrentPatente ? $targetCurrentPatente->hierarquia : 0;

        // 1. O alvo é válido? (Menor que meu teto)
        if ($targetHierarquia >= $ceilingPatente->hierarquia) {
            return redirect()->back()->with('error', 'Ação negada: O militar selecionado possui patente igual ou superior ao seu Teto de Promoção.');
        }

        // 2. [SEGURANÇA] Calcular a PRÓXIMA patente elegível independente do que veio do Select (que pode ser adulterado)
        // Encontra todas as patentes com hierarquia maior que a atual do alvo
        $nextPatente = Role::where('hierarquia', '>', $targetHierarquia)
                            ->orderBy('hierarquia', 'asc')
                            ->first();

        if (!$nextPatente) {
            return redirect()->back()->with('error', 'Erro interno: Não há patente superior cadastrada para promover este militar.');
        }

        // 3. O usuário logado tem permissão para promover ATÉ essa nova patente exigida?
        if ($nextPatente->hierarquia > $ceilingPatente->hierarquia) {
            return redirect()->back()->with('error', 'Ação negada: O próximo grau hierárquico é ' . $nextPatente->name . ', que ultrapassa seu teto de ' . $ceilingPatente->name . '.');
        }

        // A partir daqui usamos sempre a patente autocalculada ($nextPatente), ignorando completamente o input do usuário.
        $newPatente = $nextPatente;

        // 4. Executa a Revogação stricta de todas as patentes antigas (role com hierarquia > 0)
        $patentesAntigas = $targetUser->roles->where('hierarquia', '>', 0);
        foreach ($patentesAntigas as $pa) {
            $targetUser->removeRole($pa);
        }

        // 5. Atribui a nova Patente
        $targetUser->assignRole($newPatente);

        // 6. Salva Log de Promoção
        \App\Models\PromocaoLog::create([
            'user_id' => $targetUser->id,
            'promoter_id' => auth()->id(),
            'old_role_id' => $targetCurrentPatente ? $targetCurrentPatente->id : null,
            'new_role_id' => $newPatente->id,
            'description' => $request->input('description')
        ]);


        return redirect()->route('admin.promocoes.index')->with('success', "O militar {$targetUser->nickname} foi promovido com sucesso para a patente de {$newPatente->name}!");
    }

    public function demote(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'description' => 'required|string|max:500'
        ]);

        $ceilingPatente = self::getDemotionCeiling();
        if (!$ceilingPatente) return redirect()->back()->with('error', 'Ação negada: Você não tem permissão para rebaixar militares.');

        $targetUser = User::with('roles')->findOrFail($request->user_id);
        
        $targetCurrentPatente = $targetUser->roles->where('hierarquia', '>', 0)->sortByDesc('hierarquia')->first();
        $targetHierarquia = $targetCurrentPatente ? $targetCurrentPatente->hierarquia : 0;

        // 1. O alvo é válido? (Menor que meu teto de rebaixamento)
        if ($targetHierarquia >= $ceilingPatente->hierarquia) {
            return redirect()->back()->with('error', 'Ação negada: O militar selecionado possui patente igual ou superior ao seu teto de Rebaixamento.');
        }

        if ($targetHierarquia <= 0) {
            return redirect()->back()->with('error', 'Ação negada: O alvo já é civil. Para remover do sistema, use exoneração.');
        }

        // 2. [SEGURANÇA] Calcular a patente imediatamente inferior
        $previousPatente = Role::where('hierarquia', '<', $targetHierarquia)
                            ->where('hierarquia', '>', 0)
                            ->orderBy('hierarquia', 'desc')
                            ->first();

        // Limpa as patentes atuais
        $patentesAntigas = $targetUser->roles->where('hierarquia', '>', 0);
        foreach ($patentesAntigas as $pa) {
            $targetUser->removeRole($pa);
        }

        if ($previousPatente) {
            // Rebaixa formalmente para a patente inferior
            $targetUser->assignRole($previousPatente);
            
            PromocaoLog::create([
                'user_id' => $targetUser->id,
                'promoter_id' => auth()->id(),
                'old_role_id' => $targetCurrentPatente ? $targetCurrentPatente->id : null,
                'new_role_id' => $previousPatente->id,
                'description' => '[REBAIXAMENTO] ' . $request->input('description')
            ]);
            
            return redirect()->route('admin.promocoes.index')->with('success', "O militar {$targetUser->nickname} foi REBAIXADO para {$previousPatente->name}!");
        } else {
            // Falha segura: ele não possui patente inferior (era Nível 1). 
            return redirect()->back()->with('error', 'O militar está na patente mais baixa. Utilize a aba EXONERAÇÃO para transformá-lo em civil.');
        }
    }

    public function exonerate(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'description' => 'required|string|max:500'
        ]);

        $ceilingPatente = self::getExonerationCeiling();
        if (!$ceilingPatente) return redirect()->back()->with('error', 'Ação negada: Você não tem permissão para exonerar militares.');

        $targetUser = User::with('roles')->findOrFail($request->user_id);
        
        $targetCurrentPatente = $targetUser->roles->where('hierarquia', '>', 0)->sortByDesc('hierarquia')->first();
        $targetHierarquia = $targetCurrentPatente ? $targetCurrentPatente->hierarquia : 0;

        // 1. O alvo é válido? (Menor que meu teto de exoneração)
        if ($targetHierarquia >= $ceilingPatente->hierarquia) {
            return redirect()->back()->with('error', 'Ação negada: O militar selecionado possui patente igual ou superior ao seu teto de Exoneração.');
        }

        if ($targetHierarquia <= 0) {
            return redirect()->back()->with('error', 'Ação negada: O alvo já não possui patentes oficiais ativas (Civil).');
        }

        // 2. Remove todas as patentes oficiais
        $patentesAntigas = $targetUser->roles->where('hierarquia', '>', 0);
        foreach ($patentesAntigas as $pa) {
            $targetUser->removeRole($pa);
        }

        // 3. Atribui a patente de Recruta (nível 1)
        $recrutaPatente = Role::where('name', 'Recruta')->first();
        if ($recrutaPatente) {
            $targetUser->assignRole($recrutaPatente);
            $newRoleId = $recrutaPatente->id;
        } else {
            // Fallback caso "Recruta" não exista por algum motivo
            $newRoleId = null;
        }

        PromocaoLog::create([
            'user_id' => $targetUser->id,
            'promoter_id' => auth()->id(),
            'old_role_id' => $targetCurrentPatente ? $targetCurrentPatente->id : null,
            'new_role_id' => $newRoleId, // Antes null, agora obrigatório (Recruta)
            'description' => '[EXONERAÇÃO] ' . $request->input('description')
        ]);
        
        return redirect()->route('admin.promocoes.index')->with('success', "O militar {$targetUser->nickname} foi EXONERADO e transferido para a patente de Recruta com sucesso.");
    }
}
