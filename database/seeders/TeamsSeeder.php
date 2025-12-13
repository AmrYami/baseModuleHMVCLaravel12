<?php

namespace Database\Seeders;

use App\Models\Team;
use Illuminate\Database\Seeder;

class TeamsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdminTeam = Team::firstOrCreate([
            'name' => 'CRM Admin',
            'user_id' => 1, // Assign owner (first user)
            'personal_team' => false,
        ]);

        $fakeehTeam = Team::firstOrCreate([
            'name' => 'Fakeeh',
            'user_id' => 1, // Temporary owner
            'personal_team' => false,
        ]);

        $guestsTeam = Team::firstOrCreate([
            'name' => 'Guests',
            'user_id' => 1, // Temporary owner
            'personal_team' => false,
        ]);
    }
}
