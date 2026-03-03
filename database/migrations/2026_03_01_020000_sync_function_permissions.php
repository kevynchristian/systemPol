<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Pega todas as Funções (Hierarquia == 0 ou null)
        $funcoes = Role::where('hierarquia', 0)->orWhereNull('hierarquia')->get();

        foreach ($funcoes as $funcao) {
            $permNome = 'atribuir_' . Str::slug($funcao->name, '_');
            Permission::firstOrCreate(['name' => $permNome]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Deleta todas as permissões que começam com atribuir_
        Permission::where('name', 'like', 'atribuir_%')->delete();
    }
};
