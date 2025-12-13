<?php

namespace App\Facades;

use Exception;
use Eloquent as Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;


//use phpDocumentor\Reflection\Types\Self_;
use Spatie\Image\Image;

//use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Support\Facades\DB;

//use Spatie\MediaLibrary\Models\Media;

/**
 * this class apply only types video, logo, avatar, delete,document,mediafile[multi], mediafile[videos],mediafile[documents], mediafile[images], mediafiles[put any name].
 * add more types if you need
 * @todo add media method for adding pluck of files
 */
class MediaFacade
{

//    protected static function disk(): string
//    {
//        return env('MEDIA_DISK', 's3');
//    }


    static $types = ["videos", "images", "logo", "image", "documents", "avatar", "gallery"]; //we don't use this variable in this model

    static $multiFilesType = ["videos", "images", "documents", "gallery"];

    static function allSize($type)
    {
        return env($type) ?? 2048000;
    }


    /**
     * Take a request and the Model to add files
     *
     * @param Request $request
     * @return void
     */
    public static function mediafiles(Request $request, $item, $sizeReq = 2048000)
    {
        $error = 0;
        if ($request->mediafile) {
            self::addMediaArray($request, $item);
        }
        if ($request->mediafiles) {
            self::addMediafiles($request, $item);
        }
        // delete media file
        if ($request['delete']) {
            $error = self::deleteMedia($request, $item);
        }
        // add logo
        if ($request->logo && $request->logo !== 'undefined') {
            $error = self::attachLogoAvatarAndOtherSame($request->logo, $item, 'logo');
        }

        // add avatar
        if ($request->avatar && $request->avatar !== 'undefined') {
            $error = self::attachLogoAvatarAndOtherSame($request->avatar, $item, 'avatar');
        }
        // add image
        if ($request->image && $request->image !== 'undefined') {
            $error = self::attachLogoAvatarAndOtherSame($request->avatar, $item, 'image');
        }
        //for attach document to an item
        if ($request->document && $request->document !== 'undefined') {
            //add media function with set permissions
            $check = self::addToMedia($request->document, $item, 'document');
            if (!$check) {
                $error++;
            }
        }
        //for attach video file to an item [unitType,resale,project]
        if ($request->video && $request->video !== 'undefined') {
            $check = self::addToMedia($request->video, $item, 'video');
        }
        return $error;
    }

    /**
     * @param $item
     * @param $key
     */
    static function addYouyubeVideo($item, $key)
    {
        //youtube videos
        //delete old youtube url
        $item->media()->where('collection_name', $key)->delete();
        if (!empty($file)) {
            $id = self::getYouTubeId($file);
            DB::statement('insert into media (model_id,model_type,collection_name,name,file_name,disk,size,manipulations,custom_properties) values (' . $item->id . ",'" . str_replace('App', 'App\\', get_class($item)) . "','" . $key . "','youtube','" . $id . "','media'," . (255) . ",'{}','{}');");
        }
    }

    /**
     * @param Request $request
     * @param $item //  related model
     */
    static function deleteMedia(Request $request, $item)
    {

        if (strpos($request['delete'], ',') !== false) {
            $dataExploded = explode(',', $request['delete']);
            $dataExploded = array_filter($dataExploded);
            Media::whereIn('id', $dataExploded)->delete();
            foreach ($dataExploded as $del) {
                self::deleteFileWithDir($del);
            }
        } else {

            Media::where('id', $request['delete'])->delete();
            self::deleteFileWithDir($request['delete']);

        }
    }

    /**
     * @param Request $request
     * @param $item // model related
     */
    static function addMediaArray(Request $request, $item)
    {
        $error = 0;
        foreach ($request->mediafile as $key => $files) {
            if (is_array($files) || is_object($files))
                foreach ($files as $file) {
                    $error += self::completeProccess($key, $file, $item, $error);
                }
            else
                $error += self::completeProccess($key, $files, $item, $error);
        }
    }

    static function addMediafiles(Request $request, $item)
    {
        $error = 0;
        foreach ($request->mediafiles as $key => $files) {
            if (is_array($files)) {

                foreach ($files as $file) {
                    $error += self::completeProccessUnKnownMedia($key, $file, $item, $error);
                }
            } else {
//                dd(123123);
                $error += self::completeProccessUnKnownMedia($key, $files, $item, $error);
            }
        }

        return $error;
    }

    static function allTypes($key)
    {
        $allTypes = [
            'avatar' => ['JPG', 'PNG', 'GIF', 'WEBP', 'JPEG', 'jpg', 'png', 'gif', 'webp', 'jpeg'],
            'logo' => ['JPG', 'PNG', 'GIF', 'WEBP', 'JPEG', 'jpg', 'png', 'gif', 'webp', 'jpeg'],
            'images' => ['JPG', 'PNG', 'GIF', 'WEBP', 'JPEG', 'jpg', 'png', 'gif', 'webp', 'jpeg'],
            'gallery' => ['JPG', 'PNG', 'GIF', 'WEBP', 'JPEG', 'jpg', 'png', 'gif', 'webp', 'jpeg'],
            'videos' => ['WEBM', 'MPG', 'MP2', 'MPEG', 'MPE', 'MPV', 'OGG', 'MP4', 'M4P', 'M4V', 'AVI', 'WMV', 'MOV', 'QT'
                , 'FLV', 'SWF', 'AVCHD', 'webm', 'mpg', 'mp2', 'mpeg', 'mpe', 'mpv', 'ogg', 'mp4', 'm4p', 'm4v', 'avi', 'wmv', 'mov', 'qt'
                , 'flv', 'swf', 'avchd'],
            'documents' => ['DOC', 'DOCX', 'ODT', 'PDF', 'XLS', 'XLSX', 'ODS', 'PPT', 'PPTX', 'TXT',
                'doc', 'docx', 'odt', 'pdf', 'xls', 'xlsx', 'ods', 'ppt', 'pptx', 'txt'
            ]
        ];
        if ($allTypes[$key])
            return $allTypes[$key];
        return [];
    }

    /**
     * @param $key // type media
     * @param $file
     * @param $sizeReq
     * @return bool
     */
    static function validateFiles($key, $file, $sizeReq)
    {
        $res = false;
        $fileSize = filesize($file);
        if ($key == 'multi') {
            foreach (self::$multiFilesType as $type) {
                if (in_array($file->getClientOriginalExtension(), self::allTypes($type))) {
                    if ($fileSize < self::allSize($type)) {
                        return true;
                    }
                }
            }
        } else {
            if (in_array($file->getClientOriginalExtension(), self::allTypes($key))) {
                if (!($fileSize < $sizeReq))
                    return false;
            } else
                return false;
            return true;
        }
    }

    /**
     * @param $key // type media
     * @param $file
     * @param $item // model related
     * @param $error //count errors
     */
    static function completeProccess($key, $file, $item, $error)
    {
        if (!empty($file) && $file !== 'undefined') {
//            try {
                if ($key == 'multi') {
                    if (self::validateFiles($key, $file, self::allSize('default'))) {//put limit for image
                        $check = self::addToMedia($file, $item, $key);//add media function
                        if (!$check)
                            $error++;
                    } else {
                        $error++;
                    }
                } else if ($key == 'videos' || $key == 'images' || $key == 'documents' || $key == 'gallery') {
                    if ($key == 'images' || $key == 'gallery') {// if file is image
                        if (self::validateFiles($key, $file, self::allSize($key))) {//put limit for image
                            $check = self::addToMedia($file, $item, $key);//add media function
                            if (!$check)
                                $error++;
                        } else {
                            $error++;
                        }
                    } else if ($key == 'videos') {// if file not image
                        if (self::validateFiles($key, $file, self::allSize($key))) {//put limit for image
                            $check = self::addToMedia($file, $item, $key);//add media function
                            if (!$check)
                                $error++;
                        } else {
                            $error++;
                        }
                    } else if ($key == 'documents') {
                        if (self::validateFiles($key, $file, self::allSize($key))) {//put limit for image
                            $check = self::addToMedia($file, $item, $key);//add media function
                            if (!$check)
                                $error++;
                        } else {
                            $error++;
                        }
                    } else {
                        $error++;
                    }
                } else {
                    if ($file != '') {
                        //youtube videos
                        self::addYouyubeVideo($item, $key);
                    }

                }
//            } catch (Exception $e) {
//                $error++;
//            }
        }
        if ($key == 'videos' && $file == null) {
            $item->media()->where('collection_name', $key)->delete();
        }
    }

    static function completeProccessUnKnownMedia($key, $file, $item, $error)
    {
        if (!empty($file) && $file !== 'undefined') {
//            try {
//            if (self::validateFiles($key, $file, self::allSize('default'))) {//put limit for image
            $check = self::addToMedia($file, $item, $key);//add media function
            if (!$check)
                $error++;
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
     * @param $image
     * @param $item // model related
     * @param $collectionName
     * @return int
     */
    static function attachLogoAvatarAndOtherSame($image, $item, $collectionName)
    {

//        try {
        $error = 0;
        if (self::validateFiles($collectionName, $image, self::allSize($collectionName))) {//put limit for image
            $itemData = $item->media()->where('collection_name', $collectionName)->get();// get item data for check and delete

            if ($itemData) {
                $item->media()->where('collection_name', $collectionName)->delete();// delete form database
                if (isset($itemData[0]) && $itemData[0])
                    self::deleteFileWithDir($itemData[0]->id);// delete from storage
            }
            $check = self::addToMedia($image, $item, $collectionName);//add media function

            if (!$check)
                $error++;
        } else {

            $error++;
        }
//        } catch (Exception $e) {
//            $error++;
//        }
        return $error;
    }

    /**
     * @param $file
     * @param $item // model related
     * @param $key // collection name
     * @return bool
     */
    static function addToMedia($file, $item, $key)
    {
//        try {
//        if ($key == 'video') {
//            $item->media()->where('collection_name', $key)->delete();
//        }
            if ($key === 'video') {
                $item->clearMediaCollection($key);
            }

//        $filename = md5(uniqid() . time()) . '.' . $file->getClientOriginalExtension();
//        Storage::disk('media')->put($filename, file_get_contents($file));
//        $pathToFile = Storage::disk('media')->path($filename);
//            if (getSettingVal('water_mark') && in_array($file->getClientOriginalExtension(), self::allTypes('images'))) {
//                self::waterMark($pathToFile);
//            }
//        $id_item = $item->addMedia($pathToFile)->setName($file->getClientOriginalName())->toMediaCollection($key);
            $item
                ->addMedia($file)
                ->usingName(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME))
                ->usingFileName($file->getClientOriginalName())
                ->toMediaCollection(
                    $key,
                    env('MEDIA_DISK', 's3')  // make sure MEDIA_DISK=s3 in your .env
                );
            return true;
//        } catch (Exception $e) {
//            Session::push('media_errors', "Failed to upload “{$file->getClientOriginalName()}” to {$key}.");
//
//            return false;
//        }
    }

    public static function waterMark($pathToFile)
    {
        $image = Image::load(env('APP_URL') . $pathToFile);
        $image->watermark(public_path() . '/storage/' . getSettingVal('logo'))
            ->watermarkOpacity(50)
            ->watermarkHeight(10, Manipulations::UNIT_PERCENT)
            ->watermarkWidth(10, Manipulations::UNIT_PERCENT)
            ->watermarkPadding(10)
            ->save();
    }

    /**
     * @param $del // id directory same in database
     */
    static function deleteFileWithDir($directory)
    {
        $diskName = env('MEDIA_DISK', 's3');
        $disk = Storage::disk($diskName);

        try {
            // deleteDirectory will remove all objects under that prefix
            if ($disk->exists($directory)) {
                $disk->deleteDirectory($directory);
            }
        } catch (\Exception $e) {
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
