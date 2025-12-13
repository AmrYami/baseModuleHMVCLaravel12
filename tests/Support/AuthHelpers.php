<?php

namespace Tests\Support;

use Illuminate\Support\Str;
use Spatie\Permission\PermissionRegistrar;
use Spatie\Permission\Models\Role;
use Users\Models\User;
use Spatie\Permission\Models\Permission;

trait AuthHelpers
{
    protected function makeHrUser(array $overrides = [], array $permissions = []): User
    {
        $defaults = [
            'name' => ['en' => 'HR Tester'],
            'user_name' => 'hr.tester.' . uniqid(),
            'email' => 'hr.tester.' . uniqid() . '@example.com',
            'mobile' => (string) random_int(1000000000, 1999999999),
            'password' => bcrypt('password'),
            'status' => 1,
            'freeze' => 0,
            'approve' => 1,
            'email_verified_at' => now(),
            'code' => uniqid('code_'),
        ];

        /** @var User $user */
        $user = User::query()->create(array_merge($defaults, $overrides));

        // Ensure baseline roles/permissions exist (guard: web)
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $guard = config('auth.defaults.guard', 'web');

        $userRole = Role::firstOrCreate(['name' => 'User', 'guard_name' => $guard]);
        $hrRole   = Role::firstOrCreate(['name' => 'HR', 'guard_name' => $guard]);

        Permission::firstOrCreate(
            [
                'name' => 'approve-profile',
                'guard_name' => $guard,
            ],
            [
                'display_name' => 'Approve Profile',
                'description' => 'Approve pending profiles',
                'permission_group' => 'users',
            ]
        );

        foreach ($permissions as $perm) {
            $permission = Permission::firstOrCreate(
                [
                    'name' => $perm,
                    'guard_name' => $guard,
                ],
                [
                    'display_name' => Str::headline($perm),
                    'description' => $perm,
                    'permission_group' => 'assessments',
                ]
            );
            $user->givePermissionTo($permission->name);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return $user;
    }
}
