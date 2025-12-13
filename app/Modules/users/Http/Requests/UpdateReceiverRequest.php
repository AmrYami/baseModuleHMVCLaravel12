<?php

namespace Users\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReceiverRequest extends FormRequest
{

    public function rules()
    {
        return [
            'name'   => 'required|string|max:255',
            'fields' => 'required|array',
        ];
    }
}
