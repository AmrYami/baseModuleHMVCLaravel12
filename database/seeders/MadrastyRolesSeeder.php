<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Seeds the three Madrasty roles for the `api` guard (Passport).
 *
 * - `user`      — default role for every registered Madrasty user.
 * - `moderator` — can review pending listings/schools, resolve reports.
 * - `admin`     — full Madrasty admin (block users, manage taxonomies).
 *
 * Seeded under guard_name = 'api' because Madrasty is a mobile-only
 * product authenticated via Passport on the api guard. The pre-existing
 * `web` roles (CRM Admin etc., from the basemodule) are untouched.
 */
class MadrastyRolesSeeder extends Seeder
{
    public function run(): void
    {
        // Make sure the cache doesn't shadow our writes during the seed run.
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (['user', 'moderator', 'admin'] as $name) {
            Role::firstOrCreate([
                'name' => $name,
                'guard_name' => 'api',
            ]);
        }
    }
}
