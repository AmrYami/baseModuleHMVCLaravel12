<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Users\Models\User;

class ParamedicsExport implements FromCollection, WithHeadings
{

    protected $users;

    public function __construct($users)
    {
        $this->users = $users;
    }

    /**
     * Return a collection of users.
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->users->map(function ($user) {
            $mediaUrls = null;
            $grade = '';
            if (isset($user?->exams) && isset($user?->exams[0]) && $user?->exams[0]?->pivot) {
                $grade = $user?->exams[0]?->pivot?->grade;
            }
            if ($user->media)
                $mediaUrls = $user->media->map(fn($media) => $media->getUrl())->implode(' | '); // Join URLs with a separator
//            {{ isset($user?->exams) ?
//                (isset($user?->exams[0]) ? $user?->exams[0]?->pivot?->grade :
//                    '')
//                : ''}}
            $offerStatus = '';
            if (isset($user?->offers[0])) {
                $offerStatus = $user?->offers[0]?->approved == 2 ? 'Pending' : ($user?->offers[0]?->approved == 1 ? "Approved" : "Rejected");
            }

            $company = $user->company_id ? $user->company->name : 'System';

            return [
                'id' => $user->id,
                'first_name (English)' => $user->getTranslation('first_name', 'en'),
                'first_name (Arabic)' => $user->getTranslation('first_name', 'ar'),
                'second_name (English)' => $user->getTranslation('second_name', 'en'),
                'second_name (Arabic)' => $user->getTranslation('second_name', 'ar'),
                'last_name (English)' => $user->getTranslation('last_name', 'en'),
                'last_name (Arabic)' => $user->getTranslation('last_name', 'ar'),
                'user_name' => $user->user_name,
                'email' => $user->email,
                'mobile' => $user->mobile,
                'grade' => $grade,
                'source' => $company,
                'offer_status' => $offerStatus,
                'approve' => $user->approve == 1 ? 'approved' : ($user->approve == 2 ? 'sumitted' : 'rejected'),
                'nationality' => getCountry($user->nationality, fromCompany: $user->company_id),
                'study' => $user->study,
                'past_participations' => $user->past_participations,
                'valid_malpractice_insurance' => $user->valid_malpractice_insurance,
                'ATLS_certification' => $user->ATLS_certification,
                'speciality' => $user->speciality,
                'speciality_experience' => $user->speciality_experience,
                'speciality_experience_years' => $user->speciality_experience_years,
                'BLS_expiry_date' => $user->BLS_expiry_date,
                'SCFHS_classification' => $user->SCFHS_classification,
                'religion' => $user->religion,
                'ACLS_expiry_date' => $user->ACLS_expiry_date,
                'ALSO_certification' => $user->ALSO_certification,
                'SCFHS_classification_other' => $user->SCFHS_classification_other,
                'SCFHS_expiry_date' => $user->SCFHS_expiry_date,
                'availability' => $user->availability,
                'educational' => $user->educational,
                'city' => $user->city,
                'national_id' => $user->national_id,
                'SCFHS_license' => $user->SCFHS_license,
                'PALS_certification' => $user->PALS_certification,
                'available_to_work_in_makkah' => $user->available_to_work_in_makkah,
                'ACLS_certification' => $user->ACLS_certification,
                'NRP_certification' => $user->NRP_certification,
                'PHTLS_certification' => $user->PHTLS_certification,
                'experience_working_in_paramedic' => $user->experience_working_in_the_hajj,
                'experience_working_in_paramedic_years' => $user->experience_working_in_the_hajj_years,
                'EVOC_certification' => $user->EVOC_certification,
                'BLS_certification' => $user->BLS_certification,
                'commitments_description' => $user->commitments_description,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
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
            'id',
            'first_name (English)',
            'first_name (Arabic)',
            'second_name (English)',
            'second_name (Arabic)',
            'last_name (English)',
            'last_name (Arabic)',
            'user_name',
            'email',
            'mobile',
            'grade',
            'source',
            'offer_status',
            'approve',
            'nationality',
            'study',
            'past_participations',
            'valid_malpractice_insurance',
            'ATLS_certification',
            'speciality',
            'speciality_experience',
            'speciality_experience_years',
            'BLS_expiry_date',
            'SCFHS_classification',
            'religion',
            'ACLS_expiry_date',
            'ALSO_certification',
            'SCFHS_classification_other',
            'SCFHS_expiry_date',
            'availability',
            'educational',
            'city',
            'national_id',
            'SCFHS_license',
            'PALS_certification',
            'available_to_work_in_makkah',
            'ACLS_certification',
            'NRP_certification',
            'PHTLS_certification',
            'experience_working_in_paramedic',
            'experience_working_in_paramedic_years',
            'EVOC_certification',
            'BLS_certification',
            'commitments_description',
            'created_at',
            'updated_at',
            'Media'
        ];
    }
}
