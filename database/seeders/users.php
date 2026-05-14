<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Users\Models\User;

class users extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Seeds a single platform admin user. Real credentials must come from
     * env vars; if SEED_ADMIN_PASSWORD is unset, a strong random password
     * is generated and the admin must reset it via the standard
     * forgot-password flow before logging in.
     *
     * @return void
     */
    public function run()
    {
        $password = env('SEED_ADMIN_PASSWORD') ?: Str::random(32);

        $data = [
            [
                'name' => json_encode(['en' => 'admin', 'ar' => 'admin']),
                'user_name' => env('SEED_ADMIN_USERNAME', 'admin'),
                'email' => env('SEED_ADMIN_EMAIL', 'admin@example.local'),
                'mobile' => env('SEED_ADMIN_MOBILE', '00000000000'),
                'password' => Hash::make($password),
                'type' => 'crm admin',
                'code' => uniqid(),
                'status' => 1,
            ],
        ];

        User::insert($data);
    }
}
