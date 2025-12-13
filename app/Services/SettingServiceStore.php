<?php

namespace App\Services;


use App\Abstratctions\Service;
use App\Facades\MediaFacade;
use App\Interfaces\ServiceStore;
use App\Models\SettingModel;
use App\Repositories\SettingRepositoryShow;
use App\Repositories\SettingRepositoryStore;
use Illuminate\Http\Request;

class SettingServiceStore extends Service implements ServiceStore
{
    public $repo;
    /**
     * @var SettingRepositoryStore
     */
    private $settingRepositoryStore;
    /**
     * @var SettingRepositoryShow
     */
    private $settingRepositoryShow;
    /**
     * @var Setting
     */
    private $model;

    /**
     * Create a new Repository instance.
     *
     * @param SettingRepositoryStore $settingRepositoryStore
     * @param SettingRepositoryShow $settingRepositoryShow
     */
    public function __construct(SettingRepositoryStore $settingRepositoryStore,
                                SettingRepositoryShow $settingRepositoryShow, SettingModel $model)
    {
        $this->settingRepositoryStore = $settingRepositoryStore;
        $this->settingRepositoryShow = $settingRepositoryShow;
        $this->model = $model;
    }


    /**
     * Use save data into Repository
     *
     * @param Request $request
     * @return Boolean
     */
    public function save(Request $request)
    {
        try {
            $setting = $this->settingRepositoryStore->save($request->all());
            return $setting;
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * Use save data into Repository
     *
     * @param Request $request
     * @return Boolean
     */
    public function update($id = null, Request $request)
    {
        $data = $request->only($this->model->getFillable());
        $allowedVideoMimeTypes = ['avi','mp4','mov','ogg'];
        $mediaFile = $request->image;
        if (isset($mediaFile) && in_array($mediaFile->extension(), $allowedVideoMimeTypes)){
            $request->merge(['video' => $request->image]);
        }
        $setting = $this->settingRepositoryStore->updateOrCreate( ['id'=> $id], $data);
        if ($setting) {
            $setting->media()->delete();
            MediaFacade::mediafiles($request->has('video') ? new Request($request->only('video')) : new Request($request->only('image')), $setting);
        }
        return $setting;
    }

    public function updateGeneralStatistics($key, Request $request){
        try {
            $setting = $this->settingRepositoryShow->find_by(['key'=>$key])->first();
            $image = $request->only('image') ?? null;
            $this->prepareGeneralStatisticsRequest($setting->key, $request);
            $data = $request->only($this->model->getFillable());
            $setting = $this->settingRepositoryStore->update($setting->id, $data);
            if ($setting && $image) {
                MediaFacade::mediafiles(new Request($image), $setting);
            }
            return $setting;
        } catch (\Exception $exception) {
            return false;
        }
    }

    public function prepareGeneralStatisticsRequest($key, Request $request){
        $request->replace(['key'=> $key, 'value' => json_encode($request->except('image'))]);
    }
}
?>
