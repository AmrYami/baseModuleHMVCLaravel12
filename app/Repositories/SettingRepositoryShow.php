<?php

namespace App\Repositories;

use App\Interfaces\BaseRepositoryInterface;
use App\Interfaces\RepositoryShow;
use App\Models\SettingModel;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class CampaignRepository
 * @package App\Repositories
 * @version December 11, 2019, 2:33 pm UTC
*/

class SettingRepositoryShow extends BaseRepositoryShow implements RepositoryShow, BaseRepositoryInterface
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'key',
        'value',
    ];

    /**
     * @param array $request
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function find_by(array $request, $perPage = 25)
    {
        return $this->paginate($request, $perPage);
    }

    /**
     * to get general statistics from settings table
     * @return \Illuminate\Database\Eloquent\Builder[]|Collection
     */
    public function statisticsSettings(){
        return $this->allQuery()->whereIn('key', Setting::$generalStatistics)->get();
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
        return SettingModel::class;
    }
}
