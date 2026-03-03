<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRecruta
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user) {
            // Pega todas as roles do usuário
            $roles = $user->roles;
            
            // Verifica se ele tem APENAS a role 'Recruta' (ou nenhuma role, o que também deve ser bloqueado)
            // Se ele tiver qualquer outra role (hierarquia > 1 ou funções), ele passa.
            
            $hasOnlyRecruta = $roles->count() === 1 && $roles->first()->name === 'Recruta';
            $hasNoRoles = $roles->count() === 0;

            if ($hasOnlyRecruta || $hasNoRoles) {
                // Se o usuário tentar acessar algo que não seja a dashboard (rota '/')
                if (!$request->is('/')) {
                    // REGISTRA A TENTATIVA DE INVASÃO NO BANCO DE DADOS
                    \Illuminate\Support\Facades\Log::info("Tentativa de bypass de Recruta detectada para: " . $user->nickname);
                    \App\Models\SecurityLog::create([
                        'user_id' => $user->id,
                        'url' => $request->fullUrl(),
                        'method' => $request->method(),
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                        'type' => 'recruta_bypass_attempt'
                    ]);

                    return redirect('/')->with('error', 'ACESSO RESTRITO: Conclua sua Instrução Inicial para liberar o sistema.');
                }
            }
        }

        return $next($request);
    }
}
