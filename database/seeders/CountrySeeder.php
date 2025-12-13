<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CountryModel;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countries = [
            ['key' => 'AF', 'name' => ['en' => 'Afghanistan', 'ar' => 'أفغانستان']],
            ['key' => 'AL', 'name' => ['en' => 'Albania', 'ar' => 'ألبانيا']],
            ['key' => 'DZ', 'name' => ['en' => 'Algeria', 'ar' => 'الجزائر']],
            ['key' => 'AD', 'name' => ['en' => 'Andorra', 'ar' => 'أندورا']],
            ['key' => 'AO', 'name' => ['en' => 'Angola', 'ar' => 'أنغولا']],
            ['key' => 'AR', 'name' => ['en' => 'Argentina', 'ar' => 'الأرجنتين']],
            ['key' => 'AM', 'name' => ['en' => 'Armenia', 'ar' => 'أرمينيا']],
            ['key' => 'AU', 'name' => ['en' => 'Australia', 'ar' => 'أستراليا']],
            ['key' => 'AT', 'name' => ['en' => 'Austria', 'ar' => 'النمسا']],
            ['key' => 'AZ', 'name' => ['en' => 'Azerbaijan', 'ar' => 'أذربيجان']],
            ['key' => 'BH', 'name' => ['en' => 'Bahrain', 'ar' => 'البحرين']],
            ['key' => 'BD', 'name' => ['en' => 'Bangladesh', 'ar' => 'بنغلاديش']],
            ['key' => 'BE', 'name' => ['en' => 'Belgium', 'ar' => 'بلجيكا']],
            ['key' => 'BR', 'name' => ['en' => 'Brazil', 'ar' => 'البرازيل']],
            ['key' => 'CA', 'name' => ['en' => 'Canada', 'ar' => 'كندا']],
            ['key' => 'CN', 'name' => ['en' => 'China', 'ar' => 'الصين']],
            ['key' => 'CO', 'name' => ['en' => 'Colombia', 'ar' => 'كولومبيا']],
            ['key' => 'DK', 'name' => ['en' => 'Denmark', 'ar' => 'الدنمارك']],
            ['key' => 'EG', 'name' => ['en' => 'Egypt', 'ar' => 'مصر']],
            ['key' => 'FR', 'name' => ['en' => 'France', 'ar' => 'فرنسا']],
            ['key' => 'DE', 'name' => ['en' => 'Germany', 'ar' => 'ألمانيا']],
            ['key' => 'IN', 'name' => ['en' => 'India', 'ar' => 'الهند']],
            ['key' => 'ID', 'name' => ['en' => 'Indonesia', 'ar' => 'إندونيسيا']],
            ['key' => 'IR', 'name' => ['en' => 'Iran', 'ar' => 'إيران']],
            ['key' => 'IQ', 'name' => ['en' => 'Iraq', 'ar' => 'العراق']],
            ['key' => 'IT', 'name' => ['en' => 'Italy', 'ar' => 'إيطاليا']],
            ['key' => 'JP', 'name' => ['en' => 'Japan', 'ar' => 'اليابان']],
            ['key' => 'JO', 'name' => ['en' => 'Jordan', 'ar' => 'الأردن']],
            ['key' => 'KW', 'name' => ['en' => 'Kuwait', 'ar' => 'الكويت']],
            ['key' => 'LB', 'name' => ['en' => 'Lebanon', 'ar' => 'لبنان']],
            ['key' => 'LY', 'name' => ['en' => 'Libya', 'ar' => 'ليبيا']],
            ['key' => 'MY', 'name' => ['en' => 'Malaysia', 'ar' => 'ماليزيا']],
            ['key' => 'MX', 'name' => ['en' => 'Mexico', 'ar' => 'المكسيك']],
            ['key' => 'MA', 'name' => ['en' => 'Morocco', 'ar' => 'المغرب']],
            ['key' => 'NL', 'name' => ['en' => 'Netherlands', 'ar' => 'هولندا']],
            ['key' => 'NZ', 'name' => ['en' => 'New Zealand', 'ar' => 'نيوزيلندا']],
            ['key' => 'NG', 'name' => ['en' => 'Nigeria', 'ar' => 'نيجيريا']],
            ['key' => 'NO', 'name' => ['en' => 'Norway', 'ar' => 'النرويج']],
            ['key' => 'OM', 'name' => ['en' => 'Oman', 'ar' => 'عمان']],
            ['key' => 'PK', 'name' => ['en' => 'Pakistan', 'ar' => 'باكستان']],
            ['key' => 'PH', 'name' => ['en' => 'Philippines', 'ar' => 'الفلبين']],
            ['key' => 'QA', 'name' => ['en' => 'Qatar', 'ar' => 'قطر']],
            ['key' => 'RU', 'name' => ['en' => 'Russia', 'ar' => 'روسيا']],
            ['key' => 'SA', 'name' => ['en' => 'Saudi Arabia', 'ar' => 'المملكة العربية السعودية']],
            ['key' => 'SG', 'name' => ['en' => 'Singapore', 'ar' => 'سنغافورة']],
            ['key' => 'ZA', 'name' => ['en' => 'South Africa', 'ar' => 'جنوب أفريقيا']],
            ['key' => 'ES', 'name' => ['en' => 'Spain', 'ar' => 'إسبانيا']],
            ['key' => 'SD', 'name' => ['en' => 'Sudan', 'ar' => 'السودان']],
            ['key' => 'SE', 'name' => ['en' => 'Sweden', 'ar' => 'السويد']],
            ['key' => 'CH', 'name' => ['en' => 'Switzerland', 'ar' => 'سويسرا']],
            ['key' => 'SY', 'name' => ['en' => 'Syria', 'ar' => 'سوريا']],
            ['key' => 'TH', 'name' => ['en' => 'Thailand', 'ar' => 'تايلاند']],
            ['key' => 'TN', 'name' => ['en' => 'Tunisia', 'ar' => 'تونس']],
            ['key' => 'TR', 'name' => ['en' => 'Turkey', 'ar' => 'تركيا']],
            ['key' => 'UA', 'name' => ['en' => 'Ukraine', 'ar' => 'أوكرانيا']],
            ['key' => 'AE', 'name' => ['en' => 'United Arab Emirates', 'ar' => 'الإمارات العربية المتحدة']],
            ['key' => 'GB', 'name' => ['en' => 'United Kingdom', 'ar' => 'المملكة المتحدة']],
            ['key' => 'US', 'name' => ['en' => 'United States', 'ar' => 'الولايات المتحدة']],
            ['key' => 'YE', 'name' => ['en' => 'Yemen', 'ar' => 'اليمن']],
        ];
        foreach ($countries as $country) {

            CountryModel::create($country);
        }

    }
}
