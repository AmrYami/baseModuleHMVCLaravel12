<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class addAttachFilesToUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $guards = config('auth.guards');
        foreach ($guards as $guardName => $guardConfig) {

            if (!in_array($guardName, ['api', 'sanctum'])) {
                //Top MGT permission_group
                DB::table('permissions')->insert([
                    [
                        "name" => "attach-files",
                        "display_name" => "Dashboard",
                        "description" => "attach files",
                        "permission_group" => "user",
                        "guard_name" => $guardName,
                        "created_at" => Carbon::now()->toDateTimeString(),
                    ],
                ]);
            }
        }
    }
}
