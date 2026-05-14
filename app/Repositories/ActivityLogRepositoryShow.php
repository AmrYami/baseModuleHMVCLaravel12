<?php

namespace App\Repositories;

use App\Interfaces\BaseRepositoryInterface;
use App\Interfaces\RepositoryShow;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Spatie\Activitylog\Models\Activity;

/**
 * Class ActivityLogRepositoryShow
 *
 * @version December 11, 2019, 2:33 pm UTC
 */
class ActivityLogRepositoryShow extends BaseRepositoryShow implements BaseRepositoryInterface, RepositoryShow
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'log_name', 'description', 'subject_type', 'event', 'subject_id', 'causer_type', 'causer_id', 'properties', 'batch_uuid', 'created_at', 'updated_at',
    ];

    /**
     * @param  int  $perPage
     * @return LengthAwarePaginator
     */
    public function find_by(array $request, $perPage = 25)
    {
        return $this->paginate($request, $perPage, orderBy: ['id', 'desc']);
    }

    /**
     * Return searchable fields
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
        return Activity::class;
    }
}
