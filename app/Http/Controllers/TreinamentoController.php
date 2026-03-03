<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Aula;
use App\Models\User;
use App\Models\AulaRegistro;

class TreinamentoController extends Controller
{
    /**
     * Valida dinamicamente se o usuário pertence à guarnição dos Guias
     */
    private function checkGuiasAccess()
    {
        $user = auth()->user();
        if (!$user) abort(403);

        $hasGuiasRole = $user->roles->contains(function($role) {
            return str_contains(strtolower($role->name), 'guias');
        });

        if (!$hasGuiasRole && !$user->hasRole('superadmin')) {
            abort(403, 'Você não pertence à guarnição dos Guias para aplicar formações.');
        }
    }

    /**
     * Valida dinamicamente se o usuário logado é de patente maior ou igual ao alvo
     */
    private function canInstructTarget($targetUser)
    {
        $user = auth()->user();

        if ($user->hasRole('superadmin')) {
            return true;
        }

        $myRank = $user->roles->where('hierarquia', '>', 0)->sortByDesc('hierarquia')->first();
        $targetRank = $targetUser->roles->whereNotNull('hierarquia')->sortByDesc('hierarquia')->first();

        // Se o instrutor não tem patente, ele não pode dar aula
        if (!$myRank) return false;

        // Se o alvo não tem patente, qualquer instrutor patenteado pode dar aula
        if (!$targetRank) return true;

        // Compara Hierarquia (MAIOR número = Patente Mais Alta. Ex: 10 = Comando, 1 = Soldado)
        // Se a minha hierarquia é >= à do alvo, significa que sou do mesmo nível ou superior
        if ($myRank->hierarquia >= $targetRank->hierarquia) {
            return true;
        }

        return false;
    }

    /**
     * Show the form for applying a training (aula)
     */
    public function create()
    {
        $this->checkGuiasAccess();

        // 1. Aulas disponíveis (com suas roles e pré-requisitos)
        $aulas = Aula::with('roles')->get();

        // 2. Filtrar as aulas que o instrutor logado TEM PERMISSÃO de aplicar
        // Se a aula exigir 'aplicar_cfsd' e o cara não tiver, a aula nem vai pro JSON
        $aulasPermitidas = $aulas->filter(function($aula) {
            if (empty($aula->required_permission)) {
                return true; // Aula aberta para qualquer instrutor (ou mude se quiser)
            }
            
            try {
                // Modificado: required_permission agora guarda o nome da FUNÇÃO (Role)
                return auth()->user()->hasRole($aula->required_permission);
            } catch (\Exception $e) {
                return false; // Se a role não existir, ignora a classe
            }
        });

        // 3. Estruturar os dados das Aulas para o Javascript
        $aulasData = $aulasPermitidas->map(function($aula) {
            return [
                'id' => $aula->id,
                'name' => $aula->name,
                'prerequisite_id' => $aula->prerequisite_id,
                'roles_allowed' => array_values($aula->roles->pluck('id')->map(function($id) { return (int)$id; })->toArray()) // IDs das patentes que podem cursar
            ];
        })->values();

        // 4. Estruturar os dados dos Alunos (Ativos, exceto o instrutor e Superiores)
        $alunosRawAll = User::where('ativo', true)->where('id', '!=', auth()->id())->with(['roles', 'aulasRegistros' => function($query) {
            $query->where('status', 'aprovado');
        }])->get();

        // Filtra militares de patente superior
        $alunosRaw = $alunosRawAll->filter(function($aluno) {
            return $this->canInstructTarget($aluno);
        })->values();

        $alunosData = $alunosRaw->map(function($aluno) {
            // Pega a patente mais alta (hierarquia >= 0 para incluir Aluno)
            $patente = $aluno->roles->whereNotNull('hierarquia')->sortByDesc('hierarquia')->first();
            
            return [
                'id' => $aluno->id,
                'nickname' => $aluno->nickname,
                'patente_id' => $patente ? (int)$patente->id : null,
                'patente_name' => $patente ? $patente->name : 'Sem Patente',
                // Array com os IDs das aulas que ele já foi aprovado
                'aulas_aprovadas' => array_values($aluno->aulasRegistros->pluck('aula_id')->map(function($id) { return (int)$id; })->toArray())
            ];
        });

        return view('pages.treinamentos.create', [
            'aulas' => $aulasPermitidas,
            'alunos' => $alunosRaw,
            'aulasJson' => json_encode($aulasData),
            'alunosJson' => json_encode($alunosData)
        ]);
    }

    /**
     * Store the training record in DB
     */
    public function store(Request $request)
    {
        $this->checkGuiasAccess();

        $request->validate([
            'aluno_id' => 'required|array',
            'aluno_id.*' => 'exists:users,id',
            'aula_id' => 'required|exists:aulas,id', // Aqui vai manter o padrão do soft delete nativo (só pega aulas não deletadas)
            'status' => 'required|array',
            'status.*' => 'required|in:aprovado,reprovado',
            'observacao' => 'required|array',
            'observacao.*' => 'required|string',
        ]);

        $aula = Aula::with('roles')->findOrFail($request->aula_id);

        // --- VALIDAÇÃO ESTRITA 1: INSTRUTOR TEM A FUNÇÃO PARA APLICAR? ---
        if (!empty($aula->required_permission)) {
            try {
                if (!auth()->user()->hasRole($aula->required_permission)) {
                    abort(403, 'Você não possui a Função exigida para aplicar esta formação (Ex: Guias).');
                }
            } catch (\Exception $e) {
                abort(403, 'A Função exigida por esta instrução não existe no sistema. Contate o Administrador.');
            }
        }

        // Recuperar Alunos
        $alunosSelecionados = User::whereIn('id', $request->aluno_id)->with(['roles', 'aulasRegistros' => function($q) {
            $q->where('status', 'aprovado');
        }])->get();

        $allowedPatentesIds = $aula->roles->pluck('id')->toArray();

        foreach ($alunosSelecionados as $aluno) {
            // --- VALIDAÇÃO ESTRITA 1.5: O ALUNO É SUPERIOR HIERÁRQUICO? ---
            if (!$this->canInstructTarget($aluno)) {
                return redirect()->back()->with('error', "Ação negada: Você não pode aplicar formações a militares de patente superior ({$aluno->nickname}).");
            }

            // --- VALIDAÇÃO ESTRITA 2: O ALUNO É DA PATENTE CORRETA? ---
            if (!empty($allowedPatentesIds)) {
                $patenteAluno = $aluno->roles->where('hierarquia', '>', 0)->first();
                if (!$patenteAluno || !in_array($patenteAluno->id, $allowedPatentesIds)) {
                    return redirect()->back()->with('error', "O militar {$aluno->nickname} não possui a patente exigida para esta instrução.");
                }
            }

            // --- VALIDAÇÃO ESTRITA 3: O ALUNO TEM O PRÉ-REQUISITO? ---
            if ($aula->prerequisite_id) {
                // Como aulasRegistros guarda apenas o ID, se a aula foi soft deletada, o AulaRegistro correspondente ainda exisistirá
                $temRequisito = $aluno->aulasRegistros->contains('aula_id', $aula->prerequisite_id);
                if (!$temRequisito) {
                    return redirect()->back()->with('error', "O militar {$aluno->nickname} não compriu o pré-requisito necessário (Aula ID: {$aula->prerequisite_id}).");
                }
            }

            // Se passou em tudo, salva!
            $studentStatus = $request->status[$aluno->id] ?? 'reprovado';
            $studentObservacao = $request->observacao[$aluno->id] ?? 'Sem observações detalhadas.';

            AulaRegistro::create([
                'instrutor_id' => auth()->id(),
                'aluno_id' => $aluno->id,
                'aula_id' => $aula->id,
                'status' => $studentStatus,
                'observacao' => $studentObservacao
            ]);
        }

        return redirect()->route('treinamentos.create')->with('success', 'Relatório de treinamento validado e salvo com sucesso!');
    }
}
