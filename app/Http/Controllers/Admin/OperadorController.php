<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class OperadorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $operadores = User::with('roles')->paginate(15);
        return view('pages.admin.operadores.index', compact('operadores'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $patentes = Role::where('hierarquia', '>', 0)->orderBy('hierarquia')->get();
        $funcoes = Role::whereNull('hierarquia')->orWhere('hierarquia', 0)->get();
        return view('pages.admin.operadores.create', compact('patentes', 'funcoes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nickname' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'nickname' => $request->nickname,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        if ($request->has('roles') && !empty($request->roles)) {
            $user->syncRoles($request->roles);
        } else {
            $recrutaRole = Role::firstOrCreate([
                'name' => 'Recruta',
                'guard_name' => 'web'
            ], [
                'hierarquia' => 1
            ]);
            $user->assignRole($recrutaRole);
        }

        return redirect()->route('admin.operadores.index')->with('success', 'Operador criado com sucesso!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $operador = User::findOrFail($id);
        $patentes = Role::where('hierarquia', '>', 0)->orderBy('hierarquia')->get();
        $funcoes = Role::whereNull('hierarquia')->orWhere('hierarquia', 0)->get();
        return view('pages.admin.operadores.edit', compact('operador', 'patentes', 'funcoes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'nickname' => 'required|string|max:255|unique:users,nickname,' . $user->id,
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $user->nickname = $request->nickname;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        if ($request->has('roles')) {
            $user->syncRoles($request->roles);
        } else {
            $user->syncRoles([]);
        }

        return redirect()->route('admin.operadores.index')->with('success', 'Operador atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        
        // Prevent deleting yourself
        if (auth()->id() == $user->id) {
            return redirect()->route('admin.operadores.index')->with('error', 'Você não pode excluir a si mesmo.');
        }

        $user->delete();
        return redirect()->route('admin.operadores.index')->with('success', 'Operador removido com sucesso!');
    }
}
