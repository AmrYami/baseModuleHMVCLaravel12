<?php

namespace Database\Seeders;

use App\Models\Team;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Users\Models\User;

class UserRoleSeeder extends Seeder
{
    public function run()
    {

        // ✅ Create or find the Admin Role
        $user = Role::firstOrCreate(['name' => 'User', 'guard_name' => 'web']);

        // ✅ Ensure permissions exist before assigning
        $permissions = Permission::all();
        if ($permissions->isNotEmpty()) {
            $userPermissions = $permissions;
            $user->syncPermissions($userPermissions->whereIn('name', [
                'dashboard',
                'complete-form',
                'complete-owen-profile'
            ])->where('guard_name', 'web'));
        }

    }
}
