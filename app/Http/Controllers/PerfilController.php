<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class PerfilController extends Controller
{
    /**
     * Show the profile timeline.
     */
    public function show(User $user)
    {
        // 1. Puxa as aulas registradas
        $aulas = \App\Models\AulaRegistro::with(['aula' => function ($q) {
            $q->withTrashed();
        }, 'instrutor'])
            ->where('aluno_id', $user->id)
            ->get()
            ->map(function ($item) {
                return [
                    'type' => 'aula',
                    'date' => $item->created_at,
                    'data' => $item
                ];
            });

        // 2. Puxa os logs de promoção
        $promocoes = \App\Models\PromocaoLog::with(['promoter', 'oldRole', 'newRole'])
            ->where('user_id', $user->id)
            ->get()
            ->map(function ($item) {
                return [
                    'type' => 'promocao',
                    'date' => $item->created_at,
                    'data' => $item
                ];
            });

        // 3. Mescla e ordena do mais recente para o mais antigo
        $timeline = $aulas->concat($promocoes)->sortByDesc('date')->values();

        return view('pages.perfil.show', compact('user', 'timeline'));
    }
}
