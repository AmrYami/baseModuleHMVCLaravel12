<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Users\Models\User as JetstreamUser;

class LoginResponse implements LoginResponseContract
{
    /**
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function toResponse($request)
    {
        $user = $request->user();
        $to = ($user && method_exists($user, 'hasRole') && $user->hasRole('User'))
            ? route('requests.create')
            : route('dashboard');

        return redirect()->intended($to);
    }
}
