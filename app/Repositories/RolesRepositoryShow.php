<?php

namespace App\Repositories;

use App\Interfaces\BaseRepositoryInterface;
use App\Interfaces\RepositoryShow;
use App\Models\RoleModel;

/**
 * Class CampaignRepository
 * @package App\Repositories
 * @version December 11, 2019, 2:33 pm UTC
*/

class RolesRepositoryShow extends BaseRepositoryShow implements RepositoryShow, BaseRepositoryInterface
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name', 'guard_name'
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
        return RoleModel::class;
    }
}
