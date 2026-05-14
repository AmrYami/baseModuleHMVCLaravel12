<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class EmailVerifiedAtSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Auto-verifying every account is only safe for local/testing.
        if (! app()->environment(['local', 'testing'])) {
            return;
        }

        DB::table('users')
            ->whereNull('email_verified_at')
            ->update([
                'email_verified_at' => Carbon::now(),
            ]);
    }
}
