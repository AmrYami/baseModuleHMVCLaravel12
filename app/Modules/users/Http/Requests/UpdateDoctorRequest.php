<?php

namespace Users\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDoctorRequest extends FormRequest
{

    public function rules()
    {
        return [
            'specialization' => 'nullable|array',
            'specialization.*' => 'nullable|string|max:255',

            'hospital' => 'required|array',
            'hospital.*' => 'required|string|max:255',

            'designation' => 'required|array',
            'designation.*' => 'required|string|max:255',

            'specialty' => 'required',

            'languages' => 'required|array',
//            'languages.*' => 'nullable|string|max:255',

            'experience' => 'required|array',
            'experience.*' => 'required|string|max:255',

            'description' => 'required|array',
            'description.*' => 'required|string|max:1000',

            'achievements' => 'required|array',
            'achievements.*' => 'required|string|max:1000',

            'studies' => 'required|array',
            'studies.*' => 'required|string|max:1000',

            'work_experience' => 'required|array',
            'work_experience.*' => 'required|string|max:1000',

            'nationality' => 'nullable|array',
            'nationality.*' => 'nullable|string|max:1000',

            'speciality_text' => 'nullable|array',
            'speciality_text.*' => 'nullable|string|max:1000',

            'facilities' => 'nullable|array',
            'facilities.*' => 'nullable|string|max:1000',

            'clinics' => 'nullable|array',
            'clinics.*' => 'nullable|string|max:1000',

            'clinic_text' => 'nullable|array',
            'clinic_text.*' => 'nullable|string|max:1000',

            'head_of_department' => 'nullable|string|max:1000',
            'book_an_appointment_URL' => 'nullable|string|max:1000',
        ];
    }
}






