<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Services\HabboApiService;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('pages.dashboard.index');
    }

    public function searchOperator(Request $request, HabboApiService $habboApi)
    {
        $request->validate(['nickname' => 'required|string']);
        $nickname = $request->input('nickname');
        $operador = User::where('nickname', $nickname)->with('roles')->first();

        if (!$operador) {
            return response()->json(['message' => 'OPERADOR NÃO ENCONTRADO NO S.I.G.O.'], 404);
        }

        $habboData = $habboApi->getUserData($operador->nickname);
        if (!$habboData) {
            return response()->json(['message' => 'FALHA AO ACESSAR DADOS EXTERNOS DO OPERADOR'], 500);
        }

        $info = $habboData['info'];
        $missao = $info['motto'] ?? 'MISSÃO NÃO DIVULGADA';
        $onlineAgora = $info['online'] ?? false;
        $membroDesde = \Carbon\Carbon::parse($info['memberSince'])->diffForHumans();
        $ultimoAcessoHabbo = isset($info['lastAccessTime']) ? \Carbon\Carbon::parse($info['lastAccessTime'])->format('d/m/Y H:i') : 'Desconhecido';

        // ### A CORREÇÃO ESTÁ AQUI ###
        $emblemas = []; // Começa com um array vazio como padrão
        if (isset($info['selectedBadges']) && is_array($info['selectedBadges'])) {
            $emblemas = collect($info['selectedBadges'])->map(function ($badge) {
                return [
                    'code' => $badge['code'],
                    'name' => $badge['name'],
                    'description' => $badge['description'],
                    'url' => "https://images.habbo.com/c_images/album1584/{$badge['code']}.gif"
                ];
            });
        }

        // --- PASSO 3: PREPARAR OS DADOS PARA A RESPOSTA ---
        $patente = $operador->roles->where('hierarquia', '>', 0)->first();

        $data = [
            'nickname' => $operador->nickname,
            'status' => $operador->ativo ? 'ATIVO' : 'INATIVO',
            'status_class' => $operador->ativo ? 'text-success' : 'text-danger',
            'patente' => $patente ? $patente->name : 'N/D',
            'ultimo_login_sistema' => $operador->updated_at->format('d/m/Y H:i'),
            'avatar_url' => 'https://www.habbo.com.br/habbo-imaging/avatarimage?user=' . $operador->nickname . '&direction=2&head_direction=3&gesture=sml&action=wav',
            'missao' => $missao,

            // NOVAS INFORMAÇÕES RELEVANTES
            'online_agora' => $onlineAgora,
            'ultimo_acesso_habbo' => $ultimoAcessoHabbo,
            'tempo_de_servico' => $membroDesde,
            'emblemas_selecionados' => $emblemas,
        ];

        return response()->json($data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
