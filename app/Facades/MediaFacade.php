<?php

namespace App\Facades;

use Eloquent as Model;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
// use phpDocumentor\Reflection\Types\Self_;
use Spatie\Image\Enums\Unit;
use Spatie\Image\Image;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

// use Spatie\MediaLibrary\Models\Media;

/**
 * this class apply only types video, logo, avatar, delete,document,mediafile[multi], mediafile[videos],mediafile[documents], mediafile[images], mediafiles[put any name].
 * add more types if you need
 *
 * @todo add media method for adding pluck of files
 */
class MediaFacade
{
    public static $types = ['videos', 'images', 'logo', 'image', 'documents', 'avatar', 'gallery']; // we don't use this variable in this model

    public static $multiFilesType = ['videos', 'images', 'documents', 'gallery'];

    public static function allSize($type)
    {
        return (int) (config("media-library.max_size.$type")
            ?? config('media-library.default_max_size')
            ?? 2048000);
    }

    protected static function mediaTypeForCollection($key): string
    {
        return match ($key) {
            'document' => 'documents',
            'video' => 'videos',
            'image' => 'images',
            default => (string) $key,
        };
    }

    protected static function validationKeyForCollection($key): string
    {
        $mediaType = self::mediaTypeForCollection($key);

        return self::allTypes($mediaType) ? $mediaType : 'multi';
    }

    protected static function fileDisplayName($file): string
    {
        return method_exists($file, 'getClientOriginalName') ? $file->getClientOriginalName() : 'file';
    }

    /**
     * Take a request and the Model to add files
     *
     * @return void
     */
    public static function mediafiles(Request $request, $item)
    {
        $error = 0;
        if ($request->mediafile) {
            $error += self::addMediaArray($request, $item);
        }
        if ($request->mediafiles) {
            $error += self::addMediafiles($request, $item);
        }
        // delete media file
        if ($request['delete']) {
            $error += self::deleteMedia($request, $item);
        }
        // add logo
        if ($request->logo && $request->logo !== 'undefined') {
            $error += self::attachLogoAvatarAndOtherSame($request->logo, $item, 'logo');
        }

        // add avatar
        if ($request->avatar && $request->avatar !== 'undefined') {
            $error += self::attachLogoAvatarAndOtherSame($request->avatar, $item, 'avatar');
        }
        // add image
        if ($request->image && $request->image !== 'undefined') {
            $error += self::attachLogoAvatarAndOtherSame($request->image, $item, 'image');
        }
        // for attach document to an item
        if ($request->document && $request->document !== 'undefined') {
            // add media function with set permissions
            $error += self::addToMedia($request->document, $item, 'document') ? 0 : 1;
        }
        // for attach video file to an item [unitType,resale,project]
        if ($request->video && $request->video !== 'undefined') {
            $error += self::addToMedia($request->video, $item, 'video') ? 0 : 1;
        }

        return $error;
    }

    /**
     * @param  $file  YouTube URL/identifier
     */
    public static function addYoutubeVideo($item, $key, $file)
    {
        // youtube videos
        // delete old youtube url
        $item->media()->where('collection_name', $key)->delete();
        if (! empty($file)) {
            $id = self::getYouTubeId($file);
            if (! $id) {
                Session::push('media_errors', 'Invalid YouTube URL.');

                return;
            }
            Media::create([
                'model_id' => $item->getKey(),
                'model_type' => $item->getMorphClass(),
                'collection_name' => $key,
                'name' => 'youtube',
                'file_name' => $id,
                'disk' => 'media',
                'size' => 255,
                'manipulations' => [],
                'custom_properties' => [],
                'generated_conversions' => [],
                'responsive_images' => [],
            ]);
        }
    }

    protected static function getYouTubeId($url): ?string
    {
        $url = trim((string) $url);
        if ($url === '') {
            return null;
        }

        if (preg_match('/^[A-Za-z0-9_-]{11}$/', $url)) {
            return $url;
        }

        $parts = parse_url($url);
        if ($parts === false) {
            return null;
        }

        parse_str($parts['query'] ?? '', $query);
        if (isset($query['v']) && preg_match('/^[A-Za-z0-9_-]{11}$/', (string) $query['v'])) {
            return (string) $query['v'];
        }

        $path = trim($parts['path'] ?? '', '/');
        $segments = array_values(array_filter(explode('/', $path)));
        $candidate = end($segments) ?: null;

        return $candidate && preg_match('/^[A-Za-z0-9_-]{11}$/', $candidate) ? $candidate : null;
    }

    /**
     * @param  $item  //  related model
     */
    public static function deleteMedia(Request $request, $item): int
    {
        $mediaIds = collect(explode(',', (string) $request->input('delete')))
            ->map(fn ($id) => (int) trim($id))
            ->filter()
            ->unique()
            ->values();

        if ($mediaIds->isEmpty() || ! method_exists($item, 'media')) {
            return 0;
        }

        $mediaItems = $item->media()->whereIn('id', $mediaIds)->get();
        foreach ($mediaItems as $media) {
            self::deleteFileWithDir($media->id);
            $media->delete();
        }

        return 0;
    }

    /**
     * @param  $item  // model related
     */
    public static function addMediaArray(Request $request, $item): int
    {
        $error = 0;
        foreach ($request->mediafile as $key => $files) {
            if (is_iterable($files)) {
                foreach ($files as $file) {
                    $error += self::completeProccess($key, $file, $item, 0);
                }
            } else {
                $error += self::completeProccess($key, $files, $item, 0);
            }
        }

        return $error;
    }

    public static function addMediafiles(Request $request, $item): int
    {
        $error = 0;
        foreach ($request->mediafiles as $key => $files) {
            if (is_array($files)) {

                foreach ($files as $file) {
                    $error += self::completeProccessUnKnownMedia($key, $file, $item, 0);
                }
            } else {
                //                dd(123123);
                $error += self::completeProccessUnKnownMedia($key, $files, $item, 0);
            }
        }

        return $error;
    }

    public static function allTypes($key)
    {
        $allTypes = [
            'avatar' => ['JPG', 'PNG', 'GIF', 'WEBP', 'JPEG', 'jpg', 'png', 'gif', 'webp', 'jpeg'],
            'logo' => ['JPG', 'PNG', 'GIF', 'WEBP', 'JPEG', 'jpg', 'png', 'gif', 'webp', 'jpeg'],
            'images' => ['JPG', 'PNG', 'GIF', 'WEBP', 'JPEG', 'jpg', 'png', 'gif', 'webp', 'jpeg'],
            'gallery' => ['JPG', 'PNG', 'GIF', 'WEBP', 'JPEG', 'jpg', 'png', 'gif', 'webp', 'jpeg'],
            'videos' => ['WEBM', 'MPG', 'MP2', 'MPEG', 'MPE', 'MPV', 'OGG', 'MP4', 'M4P', 'M4V', 'AVI', 'WMV', 'MOV', 'QT', 'FLV', 'SWF', 'AVCHD', 'webm', 'mpg', 'mp2', 'mpeg', 'mpe', 'mpv', 'ogg', 'mp4', 'm4p', 'm4v', 'avi', 'wmv', 'mov', 'qt', 'flv', 'swf', 'avchd'],
            'documents' => ['DOC', 'DOCX', 'ODT', 'PDF', 'XLS', 'XLSX', 'ODS', 'PPT', 'PPTX', 'TXT',
                'doc', 'docx', 'odt', 'pdf', 'xls', 'xlsx', 'ods', 'ppt', 'pptx', 'txt',
            ],
        ];

        return $allTypes[$key] ?? [];
    }

    /**
     * @param  $key  // type media
     * @return bool
     */
    public static function validateFiles($key, $file, $sizeReq)
    {
        if (! method_exists($file, 'getClientOriginalExtension')) {
            return false;
        }

        $fileSize = method_exists($file, 'getSize')
            ? $file->getSize()
            : (is_string($file) && is_file($file) ? filesize($file) : null);
        if ($fileSize === null) {
            return false;
        }

        $extension = $file->getClientOriginalExtension();
        if ($key == 'multi') {
            foreach (self::$multiFilesType as $type) {
                if (in_array($extension, self::allTypes($type))) {
                    if ($fileSize <= self::allSize($type)) {
                        return true;
                    }
                }
            }

            return false;
        } else {
            if (in_array($extension, self::allTypes($key))) {
                if (! ($fileSize <= $sizeReq)) {
                    return false;
                }
            } else {
                return false;
            }

            return true;
        }
    }

    /**
     * @param  $key  // type media
     * @param  $item  // model related
     * @param  $error  //count errors
     */
    public static function completeProccess($key, $file, $item, $error): int
    {
        if (! empty($file) && $file !== 'undefined') {
            //            try {
            if ($key == 'multi') {
                if (self::validateFiles($key, $file, self::allSize('default'))) {// put limit for image
                    $check = self::addToMedia($file, $item, $key); // add media function
                    if (! $check) {
                        $error++;
                    }
                } else {
                    $error++;
                }
            } elseif ($key == 'videos' || $key == 'images' || $key == 'documents' || $key == 'gallery') {
                if ($key == 'images' || $key == 'gallery') {// if file is image
                    if (self::validateFiles($key, $file, self::allSize($key))) {// put limit for image
                        $check = self::addToMedia($file, $item, $key); // add media function
                        if (! $check) {
                            $error++;
                        }
                    } else {
                        $error++;
                    }
                } elseif ($key == 'videos') {// if file not image
                    if (self::validateFiles($key, $file, self::allSize($key))) {// put limit for image
                        $check = self::addToMedia($file, $item, $key); // add media function
                        if (! $check) {
                            $error++;
                        }
                    } else {
                        $error++;
                    }
                } elseif ($key == 'documents') {
                    if (self::validateFiles($key, $file, self::allSize($key))) {// put limit for image
                        $check = self::addToMedia($file, $item, $key); // add media function
                        if (! $check) {
                            $error++;
                        }
                    } else {
                        $error++;
                    }
                } else {
                    $error++;
                }
            } else {
                if ($file != '') {
                    // youtube videos
                    self::addYoutubeVideo($item, $key, $file);
                }

            }
            //            } catch (Exception $e) {
            //                $error++;
            //            }
        }
        if ($key == 'videos' && $file == null) {
            $item->media()->where('collection_name', $key)->delete();
        }

        return $error;
    }

    public static function completeProccessUnKnownMedia($key, $file, $item, $error): int
    {
        if (! empty($file) && $file !== 'undefined') {
            //            try {
            //            if (self::validateFiles($key, $file, self::allSize('default'))) {//put limit for image
            $check = self::addToMedia($file, $item, $key); // add media function
            if (! $check) {
                $error++;
            }
            //            } else {
            //                $error++;
            //            }

            //            } catch (Exception $e) {
            //                $error++;
            //            }
        }

        return $error;
    }

    /**
     * @param  $item  // model related
     * @return int
     */
    public static function attachLogoAvatarAndOtherSame($image, $item, $collectionName)
    {

        //        try {
        $error = 0;
        if (self::validateFiles($collectionName, $image, self::allSize($collectionName))) {// put limit for image
            $itemData = $item->media()->where('collection_name', $collectionName)->get(); // get item data for check and delete

            if ($itemData) {
                $item->media()->where('collection_name', $collectionName)->delete(); // delete form database
                if (isset($itemData[0]) && $itemData[0]) {
                    self::deleteFileWithDir($itemData[0]->id);
                }// delete from storage
            }
            $check = self::addToMedia($image, $item, $collectionName); // add media function

            if (! $check) {
                $error++;
            }
        } else {

            $error++;
        }

        //        } catch (Exception $e) {
        //            $error++;
        //        }
        return $error;
    }

    /**
     * @param  $item  // model related
     * @param  $key  // collection name
     * @return bool
     */
    public static function addToMedia($file, $item, $key)
    {
        try {
            $validationKey = self::validationKeyForCollection($key);
            $sizeReq = $validationKey === 'multi'
                ? self::allSize('default')
                : self::allSize($validationKey);
            if (! self::validateFiles($validationKey, $file, $sizeReq)) {
                Session::push('media_errors', 'Invalid or oversized upload for '.$key.': '.self::fileDisplayName($file));

                return false;
            }

            if ($key === 'video') {
                $item->clearMediaCollection($key);
            }

            $item
                ->addMedia($file)
                ->usingName(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME))
                ->usingFileName($file->getClientOriginalName())
                ->toMediaCollection(
                    $key,
                    config('media-library.disk_name')
                );

            return true;
        } catch (Exception $e) {
            Session::push('media_errors', 'Failed to upload '.self::fileDisplayName($file)." to {$key}: {$e->getMessage()}");

            return false;
        }
    }

    public static function waterMark($pathToFile)
    {
        $image = Image::load(config('app.url').$pathToFile);
        $image->watermark(
            filePath: public_path().'/storage/'.getSettingVal('logo'),
            opacity: 50,
            width: 10,
            widthUnit: Unit::Percent,
            height: 10,
            heightUnit: Unit::Percent,
            paddingX: 10,
            paddingY: 10,
        )->save();
    }

    /**
     * @param  $del  // id directory same in database
     */
    public static function deleteFileWithDir($directory)
    {
        $diskName = config('media-library.disk_name');
        $disk = Storage::disk($diskName);

        try {
            // deleteDirectory will remove all objects under that prefix
            if ($disk->exists($directory)) {
                $disk->deleteDirectory($directory);
            }
        } catch (Exception $e) {
            // push a user‐facing error
            Session::push('media_errors', "Could not remove media files for “{$directory}”.");
        }

        //        $pathFIle = storage_path('app/public/' . $del);
        //        if (is_dir($pathFIle)) {
        //            exec('sudo chmod ' . '-$pathFIle-0777');
        //            $files = glob($pathFIle . '/*');
        //            //Loop through the file list.
        //            foreach ($files as $file) {
        //                //Make sure that this is a file and not a directory.
        //                if (is_file($file)) {
        //                    //Use the unlink function to delete the file.
        //                    unlink($file);
        //                }
        //            }
        //            if ($del)
        //                rmdir(storage_path('app/public/' . $del));
        //        }
    }
}
