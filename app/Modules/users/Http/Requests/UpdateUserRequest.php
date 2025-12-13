<?php

namespace Users\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Users\Models\User;

class UpdateUserRequest extends FormRequest
{

    public function rules()
    {
        $userParam = $this->route('user');
        $userId = $userParam instanceof User ? $userParam->getKey() : (hid_decode($userParam) ?? $userParam);

        $roleInput = hid_decode($this->input('role')) ?? $this->input('role');
        $this->merge(['role' => $roleInput]);

        return [
            'email' => 'required|max:255|unique:users,email,'.$userId,
            'mobile' => 'required|regex:/[0-9]{6,20}/|nullable',
            'user_name' => 'required|max:255|unique:users,user_name,'.$userId,
        ];
    }
}
