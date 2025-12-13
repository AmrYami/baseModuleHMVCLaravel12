<?php

namespace Users\Repositories;

use App\Interfaces\BaseRepositoryInterface;
use App\Interfaces\RepositoryShow;
use App\Repositories\BaseRepositoryShow;
use Users\Models\Role;

/**
 * Class Repository
 * @package App\Repositories
 * @version December 11, 2019, 2:33 pm UTC
*/

class RoleRepositoryShow extends BaseRepositoryShow implements RepositoryShow, BaseRepositoryInterface
{

    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'guard_name',
        'created_at',
        'updated_at'
    ];

    /**
     * Use Search Criteria from request to find from model
     *
     * @param  Array $request
     * @return Collection
     */

    public function find_by(array $request, $perPage = 200)
    {
        $query = $this->allQuery($request, null, null, null)
            ->where('guard_name', config('auth.defaults.guard', 'web'));

        $user = auth()->user();
        if ($user && !$user->hasRole('CRM Admin')) {
            $query->where(function ($q) use ($user) {
                $q->whereHas('users', function ($q2) use ($user) {
                    $q2->where('model_has_roles.model_id', $user->id);
                })
                ->orWhere('created_by', $user->id);
            });
        }

        return $query->paginate($perPage);
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
        return Role::class;
    }

}
