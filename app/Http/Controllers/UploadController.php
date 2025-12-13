<?php

namespace App\Http\Controllers;

use App\Facades\MediaFacade;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Users\Services\UserServiceShow;
use Users\Services\UserServiceStore;

class UploadController extends BaseController
{

    public function __construct(
        UserServiceStore $userServiceStore,
        UserServiceShow  $serviceShow,
    )
    {
        $this->serviceShow = $serviceShow;
        $this->userServiceStore = $userServiceStore;
    }

    public function upload(Request $request)
    {
        if ($request->user_id) {
            $user = $this->serviceShow->find($request->user_id, $request);
        } else {
            $user = auth()->user();
        }
        if ($user) {
            MediaFacade::mediafiles($request, $user);
        }

        $mediaErrors = Session::get('media_errors', []);

        if (!empty($mediaErrors)) {
            // Optionally clear the session key
            Session::forget('media_errors');
            return response()->json(['errorsUpload' => $mediaErrors], 422);
        } else {
            return response()->json(['message' => 'File uploaded successfully.']);
        }


    }
}
