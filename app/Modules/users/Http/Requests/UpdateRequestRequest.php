<?php

namespace Users\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequestRequest extends FormRequest
{

    public function rules()
    {
        return [
            'title' => 'required',
            'description' => 'required',
        ];
    }
}
