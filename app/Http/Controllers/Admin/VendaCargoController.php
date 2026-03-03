<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\DB;
use App\Models\PromocaoLog;

class VendaCargoController extends Controller
{
    public function index()
    {
        // Get all users who could potentially buy a rank. 
        // We'll exclude super-admins and get only regular users.
        $users = User::orderBy('nickname')->get();
        
        // Get only 'executivo' roles
        $cargosExecutivos = Role::where('tipo', 'executivo')->where('hierarquia', '>', 0)->orderBy('hierarquia')->get();
        
        return view('pages.admin.vendas.index', compact('users', 'cargosExecutivos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role_id' => 'required|exists:roles,id',
            'observation' => 'required|string|max:1000'
        ]);

        $user = User::findOrFail($request->user_id);
        $roleInfo = Role::findOrFail($request->role_id);
        
        if ($roleInfo->tipo !== 'executivo') {
            return back()->with('error', 'O cargo selecionado não é executivo.');
        }

        // Get old role info for logging. Taking the first patentes role they have (or null).
        $oldRole = $user->roles()->where('hierarquia', '>', 0)->first();
        
        // Ensure atomic operation
        DB::beginTransaction();
        try {
            // Log the sale first
            DB::table('promocao_logs')->insert([
                'user_id' => $user->id,
                'promoter_id' => auth()->id(),
                'old_role_id' => $oldRole ? $oldRole->id : null,
                'new_role_id' => $roleInfo->id,
                'description' => 'VENDA DE CARGO: ' . $request->observation,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Sync user roles. This will replace the old rank with the new one.
            // Be careful to keep other non-hierarchical roles if they exist.
            $rolesToKeep = $user->roles()->where('hierarquia', 0)->pluck('name')->toArray();
            $rolesToKeep[] = $roleInfo->name;
            
            $user->syncRoles($rolesToKeep);

            DB::commit();
            return back()->with('success', 'Cargo vendido com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao processar a venda do cargo.');
        }
    }
}
