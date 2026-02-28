<?php

namespace App\Observers;

use App\Models\Role;

class RoleObserver
{
    /**
     * Handle the Role "saving" event.
     */
    public function saved(Role $role): void // <<-- MUDANÇA PRINCIPAL AQUI
    {
        // Se esta patente está configurada para sincronizar com outra...
        if ($role->sync_with_role_id) {
            $masterRole = Role::find($role->sync_with_role_id);
            if ($masterRole) {
                // Pega as permissões da patente mestre
                $permissionsToSync = $masterRole->permissions->pluck('name');

                // Sincroniza as permissões. Como isso acontece DEPOIS do controller,
                // esta será a palavra final.
                $role->syncPermissions($permissionsToSync);
            }
        }
    }
}
