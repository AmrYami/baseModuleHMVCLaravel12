<?php

namespace Database\Seeders;

use App\Models\SettingModel;
use Illuminate\Database\Seeder;

class RegistrationSettingSeeder extends Seeder
{
    public function run(): void
    {
        SettingModel::query()->updateOrCreate(
            ['key' => 'registration'],
            ['value' => json_encode(['default_status' => 'pending'])]
        );
    }
}
