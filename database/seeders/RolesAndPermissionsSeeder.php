<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Permissions
        Permission::firstOrCreate(['name' => 'aplicar_treinamento']);
        $pScript = Permission::firstOrCreate(['name' => 'gerenciar_scripts']);
        
        // Find existing Superadmin role and attach permission
        $superAdmin = Role::where('name', 'superadmin')->first();
        if ($superAdmin) {
            $superAdmin->givePermissionTo('aplicar_treinamento');
            $superAdmin->givePermissionTo($pScript);
        }

        foreach(['Líder Guias', 'Vice Líder Guias'] as $r) {
            $role = Role::where('name', $r)->first();
            if($role) {
                $role->givePermissionTo($pScript);
            }
        }

        // Output to console
        $this->command->info('Roles and Permissions seeded successfully.');
    }
}
