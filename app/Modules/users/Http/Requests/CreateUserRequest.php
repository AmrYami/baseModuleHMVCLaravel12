<?php

namespace Users\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class CreateUserRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'role' => hid_decode($this->input('role')) ?? $this->input('role'),
        ]);
    }

    public function rules(): array
    {

        return [
//            'name' => 'required|array',
            'user_name' => 'required|max:255|regex:/^(?!\d)[A-Za-z0-9._]+$/|unique:users,user_name',
            'email' => [
                'required',
                'string',
                'max:255',
                'email:rfc,dns',
                'unique:users,email',

                // ← ZeroBounce check runs here, with the actual email value
//                function ($attribute, $value, $fail) {
//                    $apiKey  = env('ZERO_BOUNCE_API', '');
//                    $baseUrl = rtrim(env('ZERO_URL', ''), '/');
//
//                    $response = Http::withHeaders([
//                        'Content-Type' => 'application/json',
//                    ])->get("$baseUrl/validate", [
//                        'api_key' => $apiKey,
//                        'email'   => $value,
//                    ]);
//
//                    // if the HTTP call failed or the status isn’t valid/unknown
//                    if ($response->failed()
//                        || ! in_array(
//                            $response->json('status', ''),
//                            ['valid', 'unknown', 'catch-all'],
//                            true
//                        )
//                    ) {
//                        $fail('The provided email address could not be verified by our provider.');
//                    }
//                },
            ],
            'mobile' => [
                'required',
                // allow either:
                //  • local format: 05 + 8 digits
                //  • international: +9665 + 8 digits
                'regex:/^(?:\+966|0)5[0-9]{8}$/',
                'unique:users,mobile',
            ],
            'status' => 'nullable|in:0,1',
            'password' => [
                'required', 'string', 'confirmed',
                // Strong password requirements:
                Password::min(8)
                    ->mixedCase()      // upper & lower
                    ->letters()        // at least one letter
                    ->numbers()        // at least one number
                    ->symbols()        // at least one symbol
//                    ->uncompromised(), // checks against known leaks
            ],
            'password_confirmation' => 'same:password',
        ];
    }

    public function messages()
    {
        return [
            'user_name.regex' => 'Username may only contain letters, numbers, dots and underscores, and cannot start with a number.',
            'password.min' => 'Password must be at least :min characters.',
            'password.mixed' => 'Password must contain both uppercase and lowercase letters.',
            'password.letters' => 'Password must contain at least one letter.',
            'password.numbers' => 'Password must contain at least one number.',
            'password.symbols' => 'Password must contain at least one symbol.',
            'password.uncompromised' => 'This password has appeared in a data leak. Please choose a different one.',

            'mobile.regex' => 'Mobile must be a valid Saudi number, e.g. 05XXXXXXXX or +9665XXXXXXXX.',


        ];
    }

    protected function isUserRole($roleId): bool
    {
        $roleId = hid_decode($roleId) ?? $roleId;
        // Accept both a role name ('User') and a role id
        if (is_string($roleId) && strcasecmp($roleId, 'User') === 0) {
            return true;
        }

        if ($roleId) {
            // If numeric/id-like, load and check actual role name
            $role = Role::find($roleId);
            if ($role && strcasecmp($role->name, 'User') === 0) {
                return true;
            }
        }

        // Fallback: compare against the configured 'User' role id if present
        $userRoleId = Role::where('name', 'User')->value('id');
        return $userRoleId && (string) $roleId === (string) $userRoleId;
    }

}
