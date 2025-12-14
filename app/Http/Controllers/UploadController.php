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
        $files = $request->allFiles();
        if (empty($files)) {
            return response()->json(['message' => 'No file provided.'], 422);
        }

        // Per-field validation: mimes/sizes per category
        $rules = [];

        // Avatar (single image)
        if ($request->hasFile('avatar')) {
            $rules['avatar'] = 'file|max:5120|mimes:jpg,jpeg,png';
        }

        $mediaGroups = $request->file('mediafiles', []);
        foreach ($mediaGroups as $group => $fileOrArray) {
            $maxKb = 20480; // 20 MB default
            $mimes = 'jpg,jpeg,png,pdf,doc,docx,xls,xlsx,txt';

            // Tighten specific groups
            if ($group === 'national_id_iqama') {
                $mimes = 'jpg,jpeg,png,pdf';
            } elseif (in_array($group, ['SCFHS', 'BLS', 'ACLS'], true)) {
                $mimes = 'jpg,jpeg,png,pdf';
                $maxKb = 10240; // 10 MB
            }

            $filesList = is_array($fileOrArray) ? $fileOrArray : [$fileOrArray];
            foreach ($filesList as $idx => $f) {
                $rules["mediafiles.{$group}.{$idx}"] = "file|max:{$maxKb}|mimes:{$mimes}";
            }
        }

        $request->validate($rules);

        // Additional safeguard: cap image dimensions to prevent very large payloads
        $allToCheck = [];
        if ($request->hasFile('avatar')) {
            $allToCheck[] = $request->file('avatar');
        }
        foreach ($mediaGroups as $fileOrArray) {
            $list = is_array($fileOrArray) ? $fileOrArray : [$fileOrArray];
            foreach ($list as $f) {
                $allToCheck[] = $f;
            }
        }

        foreach ($allToCheck as $uploaded) {
            if ($uploaded && str_starts_with($uploaded->getMimeType(), 'image/')) {
                $size = @getimagesize($uploaded->getPathname());
                if ($size && (($size[0] ?? 0) > 6000 || ($size[1] ?? 0) > 6000)) {
                    return response()->json([
                        'message' => 'Image dimensions exceed the allowed limit (6000x6000).',
                    ], 422);
                }
            }
        }

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
