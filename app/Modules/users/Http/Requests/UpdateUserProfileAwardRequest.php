<?php

namespace Users\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class UpdateUserProfileAwardRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

//    protected function failedValidation(Validator $validator)
//    {
//        dd($validator->errors());
//    }

    public function rules(): array
    {
        $user = auth()->user();
        $avatarRule = $user->getMedia('avatar')->isEmpty() && !$this->avatar ? 'required' : 'nullable';

        //mediafiles SCFHS_license
        $license          = $this->input('SCFHS_license');
        $hasExistingFiles = $user->getMedia('SCFHS')->isNotEmpty();

        // Determine whether to require the SCFHS uploads
        if ($license === 'yes' && ! $hasExistingFiles) {
            $scfhsRule = ['required', 'array'];
        } else {
            $scfhsRule = ['nullable', 'array'];
        }
        //mediafiles SCFHS_license

//         mediafiles[BLS]
        $BLSlicense          = $this->input('BLS_certification');
        $hasExistingFilesBLS = $user->getMedia('BLS')->isNotEmpty();

        // Determine whether to require the SCFHS uploads
        if ($BLSlicense === 'yes' && ! $hasExistingFilesBLS) {
            $BLSRule = ['required', 'array'];
        } else {
            $BLSRule = ['nullable', 'array'];
        }
//        BLS_certification mediafiles[BLS]

//        ACLS_certification  mediafiles[ACLS]
//        $ACLSlicense          = $this->input('ACLS_certification');
//        $hasExistingFilesACLS = $user->getMedia('ACLS')->isNotEmpty();
//
//        // Determine whether to require the SCFHS uploads
//        if ($ACLSlicense === 'yes' && ! $hasExistingFilesACLS) {
//            $ACLSRule = ['required', 'array'];
//        } else {
//            $ACLSRule = ['nullable', 'array'];
//        }
//        ACLS_certification  mediafiles[ACLS]


        // Base rules for all requests.
        $rules = [
            // ensure the arrays exist
            'first_name' => 'required|array',
            'second_name' => 'required|array',
            'last_name' => 'required|array',

            // English entries: only A–Z and spaces
            'first_name.en' => [
                'required', 'string', 'max:255',
                'regex:/^[A-Za-z\s]+$/'
            ],
            'second_name.en' => [
                'required', 'string', 'max:255',
                'regex:/^[A-Za-z\s]+$/'
            ],
            'last_name.en' => [
                'required', 'string', 'max:255',
                'regex:/^[A-Za-z\s]+$/'
            ],

            // Arabic entries: only Arabic letters and spaces
            'first_name.ar' => [
                'required', 'string', 'max:255',
                'regex:/^[\p{Arabic}\s]+$/u'
            ],
            'second_name.ar' => [
                'required', 'string', 'max:255',
                'regex:/^[\p{Arabic}\s]+$/u'
            ],
            'last_name.ar' => [
                'required', 'string', 'max:255',
                'regex:/^[\p{Arabic}\s]+$/u'
            ],
            'national_id' => [
                'required',
                'digits:10',               // exactly 10 digits
                'regex:/^(?:1|2)[0-9]{9}$/',
                'unique:users,national_id,'.$this->route('user') // if you need to enforce uniqueness
            ],
            'nationality' => ['required', 'string', 'min:1', 'max:255'],
            'religion' => ['required', 'string', 'min:1', 'max:255'],
//            'city' => ['required', 'string', 'min:1', 'max:255'],
//            'educational' => ['required', 'string', 'min:1', 'max:255'],
//            'study' => ['required', 'string', 'min:1', 'max:255'],
            'speciality_experience' => ['required', 'string', 'min:1', 'max:255'],
//            'speciality_experience_years' => ['required_if:speciality_experience,yes', 'string', 'min:1', 'max:255'],
            'SCFHS_classification' => ['required', 'string', 'min:1', 'max:255'],
            'SCFHS_expiry_date' => ['required', 'string', 'min:1', 'max:255'],
            'SCFHS_license' => ['required', 'string', 'min:1', 'max:255'],
            'BLS_certification' => ['required', 'string', 'min:1', 'max:255'],
            'BLS_expiry_date' => ['required', 'string', 'min:1', 'max:255'],
//            'ACLS_certification' => ['required', 'string', 'min:1', 'max:255'],
//            'ACLS_expiry_date' => ['required', 'string', 'min:1', 'max:255'],
            'PHTLS_certification' => ['required', 'string', 'min:1', 'max:255'],
            'PALS_certification' => ['required', 'string', 'min:1', 'max:255'],
            'NRP_certification' => ['required', 'string', 'min:1', 'max:255'],
            'EVOC_certification' => ['required', 'string', 'min:1', 'max:255'],
            'availability' => ['required', 'string', 'min:1', 'max:255'],
            'experience_working_in_the_hajj' => ['required', 'string', 'min:1', 'max:255'],
            'available_to_work_in_makkah' => ['required', 'string', 'min:1', 'max:255'],
            'avatar' => [
                $avatarRule,              // required if no existing avatar, otherwise nullable
//                'image',
//                'mimes:jpeg,png,jpg,svg',
//                'max:10240',              // 10 MB
//                'dimensions:min_width=100,min_height=100,max_width=1000,max_height=1000',
            ],
            'existing_multi_ids' => ['nullable', 'array'],

            // new uploads: required if they didn’t keep any existing file
//            'mediafile.multi' => [
//                'required_without:existing_multi_ids',
//                'array',
//            ],

            'mediafiles.SCFHS'       => $scfhsRule,
            'mediafiles.BLS'       => $BLSRule,
//            'mediafiles.ACLS'       => $ACLSRule,

        ];

        return $rules;
    }

    public function messages()
    {
        return [
            'avatar.required' => 'Please upload an avatar.',
            'avatar.image' => 'Avatar must be an image.',
            'avatar.mimes' => 'Avatar must be a jpeg, png, jpg or svg.',
            'avatar.max' => 'Avatar may not be larger than 10 MB.',
            'avatar.dimensions' => 'Avatar must be between 100×100 and 1000×1000 pixels.',

        ];            // other custom messages
    }
}
