<?php

namespace App\Http\Responses;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    /**
     * @param  Request  $request
     * @return mixed
     */
    public function toResponse($request)
    {
        $user = $request->user();
        $isUserRole = $user && method_exists($user, 'hasRole') && $user->hasRole('User');
        $to = ($isUserRole && Route::has('requests.create'))
            ? route('requests.create')
            : route('dashboard');

        return redirect()->intended($to);
    }
}
