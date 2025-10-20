<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;

class HabboApiService
{
    protected string $baseUrl;
    protected string $userAgent;

    public function __construct()
    {
        // É uma boa prática carregar isso de um arquivo de configuração
        $this->baseUrl = 'https://www.habbo.com.br/api/public';
        $this->userAgent = 'github.com/gerbenjacobs/habbo-api v2.2.0';
    }

    /**
     * Busca os dados básicos e os grupos de um usuário do Habbo.
     * Retorna um array com os dados ou null em caso de falha.
     */
    public function getUserData(string $nickname): ?array
    {
        // 1. Buscar informações do usuário
        $userResponse = Http::withHeaders(['User-Agent' => $this->userAgent])
            ->get("{$this->baseUrl}/users", ['name' => $nickname]);

        if ($userResponse->failed()) {
            return null;
        }

        $userData = $userResponse->json();
        $uniqueId = $userData['uniqueId'];

        // 2. Buscar grupos do usuário
        $groupsResponse = Http::withHeaders(['User-Agent' => $this->userAgent])
            ->get("{$this->baseUrl}/users/{$uniqueId}/groups");

        // Mesmo se a busca de grupos falhar, podemos retornar os dados do usuário
        $userGroups = $groupsResponse->successful() ? $groupsResponse->json() : [];

        return [
            'info'   => $userData,
            'groups' => $userGroups,
        ];
    }

    /**
     * Gera a URL da imagem de um emblema de grupo.
     */
    public function getGroupBadgeUrl(string $badgeCode): string
    {
        return "https://www.habbo.com.br/habbo-imaging/badge/{$badgeCode}.gif";
    }
}
