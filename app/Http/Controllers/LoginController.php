<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthenticateRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\SystemConfig;
use App\Models\User;
use App\Services\HabboApiService;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    protected HabboApiService $habboApi;

    public function __construct(HabboApiService $habboApiService)
    {
        $this->habboApi = $habboApiService;
    }

    /**
     * Exibe o formulário de login e gera o token da missão.
     */
    public function index()
    {
        $system = SystemConfig::find(1); // Mais simples que 'where' para ID
        $baseMotto = $system ? $system->system_init_motto : 'REG-';

        // Lógica para gerar token movida para um local mais limpo
        $missionToken = $baseMotto . Str::random(5);
        session(['missionToken' => $missionToken]);

        return view('auth.login');
    }

    /**
     * Processa a tentativa de login.
     */
    public function authenticate(AuthenticateRequest $request)
    {
        $credentials = $request->validated();

        if (!Auth::attempt($credentials)) {
            // AQUI: Usando Session Flash em vez de Toastr
            return redirect()->back()->with('notification', [
                'type'    => 'error',
                'title'   => 'AUTENTICAÇÃO FALHOU',
                'message' => 'Verifique novamente seu ID de Operador e Senha de Acesso.',
                'icon'    => 'fas fa-exclamation-triangle'
            ])->withInput();
        }

        $request->session()->regenerate();
        $user = Auth::user();

        if (!$user->ativo) {
            Auth::logout();

            // AQUI: Usando Session Flash em vez de Toastr
            return redirect()->back()->with('notification', [
                'type'    => 'error',
                'title'   => 'ACESSO BLOQUEADO',
                'message' => 'Entre em contato com a administração para reativar seu acesso.',
                'icon'    => 'fas fa-user-lock' // Ícone mais apropriado
            ])->withInput();
        }

        return redirect()->route('dashboard');
    }

    /**
     * Processa o registro de um novo usuário.
     */
    public function register(RegisterRequest $request)
    {
        $validated = $request->validated();

        // 1. Pega os dados do Habbo
        $habboData = $this->habboApi->getUserData($validated['nickname']);

        if (!$habboData) {
            Toastr::error('Este usuário não foi encontrado no Habbo.com.br.', 'Erro no Registro');
            return redirect()->back()->withInput();
        }

        // 2. Valida a missão
        $userMotto = $habboData['info']['motto'];
        $missionToken = session('missionToken');

        if ($userMotto !== $missionToken) {
            Toastr::error("Sua missão no Habbo ({$userMotto}) não corresponde à esperada ({$missionToken}).", 'Missão Inválida');
            return redirect()->back()->withInput();
        }

        // 3. Cria o usuário
        $user = User::create([
            'nickname'   => $validated['nickname'],
            'email'      => $validated['email'],
            'password'   => bcrypt($validated['password']),
            'permission' => 1, // Permissão padrão para novos usuários
            'ativo'      => true,
        ]);

        Toastr::success('Sua conta foi criada com sucesso!', 'Bem-vindo(a)!');
        return response()->json(['success' => true, 'message' => 'Conta criada com sucesso!'], 201);
    }

    /**
     * Exibe o perfil público de um usuário (exemplo refatorado do seu método 'show').
     */
    public function showProfile(string $nickname)
    {
        $habboData = $this->habboApi->getUserData($nickname);

        if (!$habboData) {
            abort(404, 'Usuário não encontrado.');
        }

        // Mova essa lógica para um arquivo de config, ex: config/habbo.php
        $permissionGroups = [
            'soldado' => 'g-hhbr-3804c4b79cf892bc50938d55adfdc44a',
            'cabo'    => 'g-hhbr-ca5a69adf6ba7fc025acc8faccf53cfc',
        ];

        $userGroups = collect($habboData['groups']);

        $filteredGroups = $userGroups->filter(function ($group) use ($permissionGroups) {
            return in_array($group['id'], $permissionGroups);
        })->map(function ($group) {
            // Adiciona a URL do emblema diretamente no array do grupo
            $group['badgeUrl'] = $this->habboApi->getGroupBadgeUrl($group['badgeCode']);
            return $group;
        });

        // Passe os dados para a view, que será responsável por exibir o HTML
        return view('profile.show', [
            'userInfo' => $habboData['info'],
            'groups'   => $filteredGroups,
        ]);
    }

    /**
     * Faz o logout do usuário.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
