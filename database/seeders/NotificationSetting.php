<?php

namespace Database\Seeders;

use App\Models\SettingModel;
use Illuminate\Database\Seeder;

class NotificationSetting extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SettingModel::updateOrCreate(
            ['key' => 'notifications'],
            ['value' => json_encode([
                'approval_profile' => 'on',
                'new_account' => 'on',
                'notifiers' => 'on',
            ])]
        );
    }
}
