<?php

namespace App\Http\Controllers;

use App\Services\IdCardService;
use Illuminate\Http\Request;
use Users\Models\User;
use Users\Services\UserServiceShow;

class IdCardController extends Controller
{
    public function __construct(
        IdCardService   $generator,
        UserServiceShow $serviceShow
    )
    {
        $this->generator = $generator;
        $this->serviceShow = $serviceShow;
    }


    public function create(Request $request)
    {
        $users = $this->serviceShow->contractApproved($request, 0, doesntHaveId: true);
        foreach ($users as $user) {
            $media = $this->generator->generate($user);
        }

        return response()->json([
            'status' => 'success',
            'url' => $media->getUrl(),
        ]);
    }
}
