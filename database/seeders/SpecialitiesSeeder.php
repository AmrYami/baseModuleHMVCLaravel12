<?php

namespace Database\Seeders;

use App\Models\SpecialityModel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SpecialitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $specialities = [
            'urology',
            'internal-medicine',
            'orthopedics',
            'pediatric-department',
            'obstetrics-and-gynecology',
            'general-surgery',
            'cardiology',
            'mental-health-psychiatry',
            'plastic-surgery',
            'physical-medicine-rehabilitation',
            'neurology',
            'dental-and-maxillofacial',
            'breast-feeding-clinic',
            'ear-nose-throat-otorhinolaryngology',
            'chest-and-respiratory-diseases',
            'eyes-ophthalmology',
            'nephrology',
            'vascular-surgery',
            'dermatology',
            'dietary',
            'endocrinology',
            'pediatric-surgery',
            'consultant-obstetrics-and-gynecology',
            'infectious-diseases',
            'family-medicine',
            'pain-treatment-service',
            'gastroenterology',
            'hematology',
            'chiropractic-medicine',
            'oncology',
            'cardiac-surgery',
            'hyperbaric-oxygen-service',
            'bariatric-surgery',
            'speech-therapy',
            'thoracic-surgery',
            'radiology',
            'rheumatology',
            'neuro-surgery',
            'audiology'
        ];

        foreach ($specialities as $speciality) {

            SpecialityModel::create(['name' => ['en' => $speciality, 'ar' => $speciality]]);
        }
    }
}
