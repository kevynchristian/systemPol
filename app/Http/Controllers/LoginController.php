<?php

namespace App\Http\Controllers;

use App\Models\SystemConfig;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class LoginController extends Controller
{
    protected $habboController;

    public function __construct(HabboController $habboController)
    {
        $this->habboController = $habboController;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $system = SystemConfig::where('id', 1)->firstOrFail();

        $tokenMissao = $system->system_init_motto;
        $caracteres = 'AOCPTQW0123456789#&=';
        $randomString = substr(str_shuffle($caracteres), 0, 5);
        $tokenMissao = $tokenMissao . $randomString;

        session(['tokenMissao' => 'teste']);


        return view('login');

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {

        dd($request->all());
        $nickname = $request->nickname;
        $response = $this->habboController->getHabbo($nickname);

        // Verifica se a resposta foi bem sucedida
        if ($response->isSuccessful()) {
            // Acessando os dados da resposta diretamente
            $data = $response->original;

            // Acessando as propriedades do objeto $data
            $userInfo = $data['userInfo'];
            $userGroups = $data['userGroups'];


        } else {
            // Se a requisição falhar, retorna uma resposta de erro
            return $response;
        }

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $response = $this->habboController->getHabbo($request->nickname);

        // Verifica se a resposta foi bem sucedida
        if ($response->isSuccessful()) {
            // Acessando os dados da resposta diretamente
            $data = $response->original;

            // Acessando as propriedades do objeto $data
            $userMotto = $data['userInfo']['motto'];


            if($userMotto == session('tokenMissao')){
                $valid = $request->validate([
                    'nickname' => 'required|unique:users|max:50',
                    'email' => 'required|unique:users|max:70',
                    'password' => 'required|max:50',
                ], [
                    'nickname.required' => 'O campo NickName não pode ficar em branco.',
                    'nickname.max' => 'O campo NickName não pode ter mais de 50 caracteres.',
                    'nickname.unique' => 'Já existe um NickName com esse nickname.',
                    'email.required' => 'O campo Email não pode ficar em branco.',
                    'email.max' => 'O campo Email não pode ter mais de 70 caracteres.',
                    'email.unique' => 'Este Email já foi cadastrado.',
                    'password.required' => 'O campo Senha não pode ficar em branco.',
                    'password.max' => 'O campo Senha não pode ter mais de 50 caracteres.',
                ]);

                $user = [
                    'nickname' => $valid['nickname'],
                    'email' => $valid['email'],
                    'password' => bcrypt($valid['password']),
                    'permission' => 1,
                ];

                User::create($user);
                return redirect('login')->with('msg', 'Conta cadastrada');
            }
            session()->forget('tokenMissao');
            return redirect('login')->with('msg', 'Código invalido');

        } else {
            // Se a requisição falhar, retorna uma resposta de erro
            return $response;
        }


        //$novoUsuario = User::with('departamento', 'permissao')->find($usuarioCriado->id);
    }

    /**
     * Display the specified resource.
     */
    public function show($nickname)
    {
        $response = $this->habboController->getHabbo($nickname);


        if ($response->isSuccessful()) {
            // Acessando os dados da resposta diretamente
            $data = $response->original;

            // Acessando as propriedades do objeto $data
            $userInfo = $data['userInfo'];
            $userGroups = $data['userGroups'];


        } else {
            // Se a requisição falhar, retorna uma resposta de erro
            return $response;
        }

        $idPermissaoBancoDados = [
            'soldado' => 'g-hhbr-3804c4b79cf892bc50938d55adfdc44a',
            'cabo' => 'g-hhbr-ca5a69adf6ba7fc025acc8faccf53cfc',
        ];

        // Função de filtro para verificar se o ID do grupo está presente no array de permissões
        $filtro = function($grupo) use ($idPermissaoBancoDados) {
            // Verifica se o ID do grupo está presente no array de permissões
            return in_array($grupo['id'], $idPermissaoBancoDados);
        };

        // Aplica o filtro no array de grupos
        $gruposFiltrados = array_filter($userGroups, $filtro);

        // Imprime os grupos filtrados

        foreach ($gruposFiltrados as $item){
            echo "<img src=". $this->habboController->getGroupImg($item['badgeCode']) .">";
        }
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
