<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Users\Models\User;

class RolesTableSeeder extends Seeder
{
    public function run()
    {

        $roleDefinitions = [
            'CRM Admin' => [
                'guard' => 'web',
                'permissions' => null,
                'assign' => fn () => User::find(1),
            ],
            'COO' => [
                'guard' => 'web',
                'permissions' => [
                    'dashboard',
                    'delete-users',
                    'list-users',
                    'create-users',
                    'edit-users',
                    'edit-doctor',
                    'edit-my-profile',
                    'export-doctors',
                    'list-doctors',
                    'show-doctor-activities',
                    'approve-profile',
                    'send-request-to-marketing',
                    'send-request-to-open-edit-profile',
                    'send-request-to-hr',
                    'send-request-to-doctor',
                    'send-request-to-complete-profile',
                ],
                'assign' => fn () => User::where('user_name', 'COO')->first(),
            ],
            'HR' => [
                'guard' => 'web',
                'permissions' => [
                    'dashboard',
                    'list-users',
                    'create-users',
                    'edit-users',
                    'edit-doctor',
                    'edit-my-profile',
                    'export-doctors',
                    'list-doctors',
                    'show-doctor-activities',
                    'send-request-to-marketing',
                    'send-request-to-open-edit-profile',
                    'send-request-to-hr',
                ],
                'assign' => fn () => User::where('user_name', 'HR')->first(),
            ],
            'Marketing' => [
                'guard' => 'web',
                'permissions' => [
                    'dashboard',
                    'create-users',
                    'edit-users',
                    'edit-doctor',
                    'edit-my-profile',
                    'export-doctors',
                    'list-doctors',
                    'show-doctor-activities',
                    'send-request-to-open-edit-profile',
                    'send-request-to-hr',
                ],
                'assign' => fn () => User::where('user_name', 'Marketing')->first(),
            ],
            'Doctor' => [
                'guard' => 'doctor',
                'permissions' => [
                    'dashboard',
                    'edit-my-profile',
                    'send-request-to-open-edit-profile',
                ],
                'assign' => null,
            ],
        ];

        $permissionCache = Permission::all();
        $roleModels = [];

        foreach ($roleDefinitions as $name => $meta) {
            $role = Role::firstOrCreate([
                'name' => $name,
                'guard_name' => $meta['guard'],
            ]);
            $roleModels[$name] = $role;

            if ($permissionCache->isNotEmpty() && !empty($meta['permissions'])) {
                $allowed = $permissionCache
                    ->whereIn('name', $meta['permissions'])
                    ->where('guard_name', $meta['guard']);
                $role->syncPermissions($allowed);
            }
        }

        app(PermissionRegistrar::class)->setPermissionsTeamId(null);

        foreach ($roleDefinitions as $name => $meta) {
            if (!is_callable($meta['assign'] ?? null)) {
                continue;
            }
            $user = $meta['assign']();
            if ($user) {
                $user->assignRole($name);
            }
        }

    }
}
