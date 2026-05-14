<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserRoleSeeder extends Seeder
{
    public function run()
    {

        // ✅ Create or find the Admin Role
        $user = Role::firstOrCreate(['name' => 'User', 'guard_name' => 'web']);

        // ✅ Ensure permissions exist before assigning
        $userPermissions = Permission::query()
            ->where('guard_name', 'web')
            ->whereIn('name', [
                'dashboard',
                'complete-own-profile',
            ])
            ->get();

        if ($userPermissions->isNotEmpty()) {
            $user->syncPermissions($userPermissions);
        }

    }
}
