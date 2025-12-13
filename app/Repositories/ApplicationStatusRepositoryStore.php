<?php

namespace App\Repositories;

use App\Interfaces\BaseRepositoryInterface;
use App\Models\ApplicationStatus;
use App\Models\SettingModel;
use Faker\Core\Number;
use Illuminate\Http\Request;
use App\Interfaces\RepositoryStore;

/**
 * Class SettingRepositoryStore
 * @package App\Repositories
 * @version December 11, 2019, 2:33 pm UTC
 */
class ApplicationStatusRepositoryStore extends BaseRepositoryStore implements RepositoryStore, BaseRepositoryInterface
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        ['user_id', 'status', 'reason', 'changed_by']
    ];

    /**
     * Use save data into Model
     *
     * @param Request $request
     * @return Boolean
     */
    public function save($data)
    {
        // check weather is there id or not
        $setting = $this->model->create($data);
        $setting->save();

        return $setting;
    }

    /**
     * Return searchable fields
     *
     * @return array
     */
    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    /**
     * Configure the Model
     **/
    public function model(): string
    {
        return ApplicationStatus::class;
    }
}
