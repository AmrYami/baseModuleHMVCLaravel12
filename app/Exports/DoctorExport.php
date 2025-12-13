<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Users\Models\User;

class DoctorExport implements FromCollection, WithHeadings
{
    /**
     * Return a collection of users.
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return User::where('approve' , 1)->whereHas('roles', function ($query) {
            $query->where('name', 'doctor');
        })->get()->map(function ($user) {
            $mediaUrls = null;
            if ($user->media)
                $mediaUrls = $user->media->map(fn($media) => $media->getUrl())->implode(' | '); // Join URLs with a separator
                return [
                    'Name (English)' => $user->getTranslation('name', 'en'),
                    'Name (Arabic)' => $user->getTranslation('name', 'ar'),
                    'Specialization (English)' => $user->getTranslation('specialization', 'en'),
                    'Specialization (Arabic)' => $user->getTranslation('specialization', 'ar'),
                    'Hospital (English)' => $user->getTranslation('hospital', 'en'),
                    'Hospital (Arabic)' => $user->getTranslation('hospital', 'ar'),
                    'Designation (English)' => $user->getTranslation('designation', 'en'),
                    'Designation (Arabic)' => $user->getTranslation('designation', 'ar'),
                    'Specialty (English)' => $user->getTranslation('specialty', 'en'),
                    'Specialty (Arabic)' => $user->getTranslation('specialty', 'ar'),
                    'Languages (English)' => $user->getTranslation('languages', 'en'),
                    'Languages (Arabic)' => $user->getTranslation('languages', 'ar'),
                    'Experience (English)' => $user->getTranslation('experience', 'en'),
                    'Experience (Arabic)' => $user->getTranslation('experience', 'ar'),
                    'Description (English)' => $user->getTranslation('description', 'en'),
                    'Description (Arabic)' => $user->getTranslation('description', 'ar'),
                    'Achievements (English)' => $user->getTranslation('achievements', 'en'),
                    'Achievements (Arabic)' => $user->getTranslation('achievements', 'ar'),
                    'Studies (English)' => $user->getTranslation('studies', 'en'),
                    'Studies (Arabic)' => $user->getTranslation('studies', 'ar'),
                    'Work Experience (English)' => $user->getTranslation('work_experience', 'en'),
                    'Work Experience (Arabic)' => $user->getTranslation('work_experience', 'ar'),
                    'Media' => $mediaUrls,
                ];
        });
    }

    /**
     * Define the headings for the CSV file.
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'Name (English)',
            'Name (Arabic)',
            'Specialization (English)',
            'Specialization (Arabic)',
            'Hospital (English)',
            'Hospital (Arabic)',
            'Designation (English)',
            'Designation (Arabic)',
            'Specialty (English)',
            'Specialty (Arabic)',
            'Languages (English)',
            'Languages (Arabic)',
            'Experience (English)',
            'Experience (Arabic)',
            'Description (English)',
            'Description (Arabic)',
            'Achievements (English)',
            'Achievements (Arabic)',
            'Studies (English)',
            'Studies (Arabic)',
            'Work Experience (English)',
            'Work Experience (Arabic)',
            'Media',
        ];
    }
}
