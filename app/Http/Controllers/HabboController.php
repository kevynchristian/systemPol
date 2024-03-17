<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class HabboController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function getHabbo($nickname)
    {
        // Faz a requisição para obter os dados do usuário
        $response = Http::withHeaders([
            'User-Agent' => 'github.com/gerbenjacobs/habbo-api v2.2.0'
        ])->get('https://www.habbo.com.br/api/public/users', [
            'name' => $nickname
        ]);

        // Verifica se a requisição foi bem sucedida
        if ($response->ok()) {
            $dadosUsuario = $response->json();

            // Obtém o uniqueId do usuário
            $uniqueId = $dadosUsuario['uniqueId'];

            // Faz a requisição para obter os grupos do usuário
            $response = Http::withHeaders([
                'User-Agent' => 'github.com/gerbenjacobs/habbo-api v2.2.0'
            ])->get("https://www.habbo.com.br/api/public/users/{$uniqueId}/groups");

            // Verifica se a requisição foi bem sucedida
            if ($response->ok()) {
                $dadosGrupos = $response->json();

                // Retorna os dados para o controller que chamou este método
                return response()->json(['userInfo' => $dadosUsuario, 'userGroups' => $dadosGrupos]);
            } else {
                // Se a requisição para obter os grupos falhar, retorna uma resposta de erro
                return response()->json(['error' => true], $response->status());
            }
        } else {
            // Se a requisição para obter os dados do usuário falhar, retorna uma resposta de erro
            return response()->json(['error' => true], $response->status());
        }
    }

    public static function getGroupImg($codigo)
    {
        return "https://www.habbo.com.br/habbo-imaging/badge/{$codigo}.gif";
    }
}
