<?php

namespace Database\Seeders;

use App\Models\StoreSeedersImplemented;
use App\Models\User;
use Fakeeh\Assessments\Database\Seeders\AssessmentsCandidateDemoSeeder;
use Fakeeh\Assessments\Database\Seeders\AssessmentsDemoSeeder;
use Fakeeh\Assessments\Database\Seeders\AssessmentsPermissionSeeder;
use Fakeeh\Assessments\Database\Seeders\AssessmentsRolePermissionAssignSeeder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Explicit ordered seeders — ensure dependencies are respected.
        // 1) Core users/teams; 2) permissions; 3) roles; 4) role-permission assignments;
        // 5) domain data; 6) demo data.
        $ordered = [
            __NAMESPACE__.'\\users',
            __NAMESPACE__.'\\TeamsSeeder',

            __NAMESPACE__.'\\TopMGTPermissionsTableSeeder',
            AssessmentsPermissionSeeder::class,

            __NAMESPACE__.'\\RolesTableSeeder',
            __NAMESPACE__.'\\UserRoleSeeder',
            AssessmentsRolePermissionAssignSeeder::class,

            __NAMESPACE__.'\\addAttachFilesToUsersSeeder',
            __NAMESPACE__.'\\CountrySeeder',
            __NAMESPACE__.'\\LanguageSeeder',
            __NAMESPACE__.'\\SpecialitiesSeeder',
            __NAMESPACE__.'\\RegistrationSettingSeeder',
            __NAMESPACE__.'\\NotificationSetting',
            __NAMESPACE__.'\\EmailVerifiedAtSeeder',

            // Demo/fixtures
            AssessmentsDemoSeeder::class,
            AssessmentsCandidateDemoSeeder::class,
        ];

        // Run ordered seeders first (idempotent via seeders table)
        foreach ($ordered as $seeder) {
            if (class_exists($seeder) && !StoreSeedersImplemented::where('seeder', $seeder)->first()) {
                $this->call($seeder);
                StoreSeedersImplemented::create(['seeder' => $seeder, 'batch' => 1]);
            }
        }

        // Auto-discover any remaining seeders in this directory and run them once.
        $directory = __DIR__;
        $files = File::files($directory);

        $skip = array_merge([
            __CLASS__,
        ], $ordered);

        $seeders = collect($files)
            ->map(fn($file) => $file->getFilename())
            ->filter(fn($name) => Str::endsWith($name, '.php') && $name !== 'DatabaseSeeder.php')
            ->map(function ($filename) {
                $class = Str::before($filename, '.php');
                return __NAMESPACE__ . '\\' . $class;
            })
            ->filter(fn($class) => class_exists($class))
            ->filter(fn($class) => !in_array($class, $skip, true))
            ->sort()
            ->values();

        foreach ($seeders as $seeder) {
            if (!StoreSeedersImplemented::where('seeder', $seeder)->first()) {
                $this->call($seeder);
                StoreSeedersImplemented::create(['seeder' => $seeder, 'batch' => 1]);
            }
        }

    }
}
