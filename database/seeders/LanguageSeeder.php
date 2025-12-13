<?php

namespace Database\Seeders;

use App\Models\LanguageModel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $languages = [
            ['key' => 'english', 'name' => ['en' => 'English', 'ar' => 'الإنجليزية']],
            ['key' => 'arabic', 'name' => ['en' => 'Arabic', 'ar' => 'العربية']],
            ['key' => 'french', 'name' => ['en' => 'French', 'ar' => 'الفرنسية']],
            ['key' => 'spanish', 'name' => ['en' => 'Spanish', 'ar' => 'الإسبانية']],
            ['key' => 'german', 'name' => ['en' => 'German', 'ar' => 'الألمانية']],
            ['key' => 'italian', 'name' => ['en' => 'Italian', 'ar' => 'الإيطالية']],
            ['key' => 'portuguese', 'name' => ['en' => 'Portuguese', 'ar' => 'البرتغالية']],
            ['key' => 'russian', 'name' => ['en' => 'Russian', 'ar' => 'الروسية']],
            ['key' => 'chinese', 'name' => ['en' => 'Chinese', 'ar' => 'الصينية']],
            ['key' => 'japanese', 'name' => ['en' => 'Japanese', 'ar' => 'اليابانية']],
            ['key' => 'korean', 'name' => ['en' => 'Korean', 'ar' => 'الكورية']],
            ['key' => 'hindi', 'name' => ['en' => 'Hindi', 'ar' => 'الهندية']],
            ['key' => 'urdu', 'name' => ['en' => 'Urdu', 'ar' => 'الأردية']],
            ['key' => 'turkish', 'name' => ['en' => 'Turkish', 'ar' => 'التركية']],
            ['key' => 'persian', 'name' => ['en' => 'Persian', 'ar' => 'الفارسية']],
            ['key' => 'dutch', 'name' => ['en' => 'Dutch', 'ar' => 'الهولندية']],
            ['key' => 'swedish', 'name' => ['en' => 'Swedish', 'ar' => 'السويدية']],
            ['key' => 'norwegian', 'name' => ['en' => 'Norwegian', 'ar' => 'النرويجية']],
            ['key' => 'danish', 'name' => ['en' => 'Danish', 'ar' => 'الدنماركية']],
            ['key' => 'greek', 'name' => ['en' => 'Greek', 'ar' => 'اليونانية']],
            ['key' => 'hebrew', 'name' => ['en' => 'Hebrew', 'ar' => 'العبرية']],
            ['key' => 'thai', 'name' => ['en' => 'Thai', 'ar' => 'التايلاندية']],
            ['key' => 'indonesian', 'name' => ['en' => 'Indonesian', 'ar' => 'الإندونيسية']],
            ['key' => 'malay', 'name' => ['en' => 'Malay', 'ar' => 'الملايوية']],
            ['key' => 'polish', 'name' => ['en' => 'Polish', 'ar' => 'البولندية']],
            ['key' => 'romanian', 'name' => ['en' => 'Romanian', 'ar' => 'الرومانية']],
            ['key' => 'czech', 'name' => ['en' => 'Czech', 'ar' => 'التشيكية']],
            ['key' => 'hungarian', 'name' => ['en' => 'Hungarian', 'ar' => 'الهنغارية']],
        ];

        foreach ($languages as $lang) {
            LanguageModel::updateOrCreate($lang);
        }
    }
}
