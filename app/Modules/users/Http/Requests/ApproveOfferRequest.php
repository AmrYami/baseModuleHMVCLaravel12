<?php

namespace Users\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApproveOfferRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

//    protected function failedValidation(Validator $validator)
//    {
//        dd($validator->errors());
//    } national_id_iqama

    public function rules(): array
    {
        $user = auth()->user();

        $signedContractObj = $user->getMedia('signed_contract');
        $signedContract = $signedContractObj->isNotEmpty();


        // Determine whether to require the SCFHS uploads
        if (!$signedContract) {
            $signedContractRule = ['required', 'array'];
        } else {
            $signedContractRule = ['nullable', 'array'];
        }


        $IBANObj = $user->getMedia('IBAN');
        $IBAN = $IBANObj->isNotEmpty();

        // Determine whether to require the $IBAN uploads
        if (!$IBAN) {
            $IBANRule = ['required', 'array'];
        } else {
            $IBANRule = ['nullable', 'array'];
        }

        $nationalIdIqamaObj = $user->getMedia('national_id_iqama');
        $nationalIdIqama = $nationalIdIqamaObj->isNotEmpty();

        // Determine whether to require the SCFHS uploads
        if (!$nationalIdIqama) {
            $nationalIdIqama = ['required', 'array'];
        } else {
            $nationalIdIqama = ['nullable', 'array'];
        }
        if (isset($this->delete) && $this->delete) {

            $ids = explode(',', $this->delete);
            if ($this->checkIfIdExist($ids, $signedContractObj)) {
                $signedContractRule = ['required', 'array'];
            }
            if ($this->checkIfIdExist($ids, $IBANObj)) {
                $IBANRule = ['required', 'array'];
            }
            if ($this->checkIfIdExist($ids, $nationalIdIqamaObj)) {
                $nationalIdIqama = ['required', 'array'];
            }
        }


        // Base rules for all requests.
        $rules = [
            // ensure the arrays exist
            'IBAN' => [
                'required',
                'string',
                'size:24',
                'alpha_num'],
            'mediafiles.national_id_iqama' => $nationalIdIqama,
            'mediafiles.IBAN' => $IBANRule,
            'mediafiles.signed_contract' => $signedContractRule,

        ];

        return $rules;
    }

    public function checkIfIdExist($ids, $object): bool
    {
        foreach ($object as $val) {
            if (in_array($val->id, $ids)) {
                return true;
            }
        }

        return false;

    }
}
