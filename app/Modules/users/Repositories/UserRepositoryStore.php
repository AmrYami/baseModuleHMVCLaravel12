<?php

namespace Users\Repositories;

use App\Interfaces\BaseRepositoryInterface;
use App\Interfaces\RepositoryStore;
use App\Repositories\BaseRepositoryStore;
use Users\Models\Doctor;
use Users\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

/**
 * Class Repository
 * @package App\Repositories
 * @version December 11, 2019, 2:33 pm UTC
 */
class UserRepositoryStore extends BaseRepositoryStore implements RepositoryStore, BaseRepositoryInterface
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'email',
        'type',
        'quota',
        'expiration_from',
        'expiration_to',
        'created_at',
        'updated_at'
    ];


    /**
     * Use save data into Model
     *
     * @param Request $request
     * @return Boolean
     */
    public function save($data)
    {
        $data['password'] = Hash::make($data['password']);
        $user = $this->create($data);
        return $user;
    }

    public function assignRole($user, $role)
    {
        $roleValue = $this->resolveRole($role);
        if ($roleValue) {
            $guard = property_exists($user, 'guard_name') && $user->guard_name
                ? $user->guard_name
                : config('auth.defaults.guard', 'web');

            // Ensure the role exists for the expected guard to avoid runtime errors during registration
            Role::firstOrCreate([
                'name' => $roleValue,
                'guard_name' => $guard,
            ]);

            $user->assignRole($roleValue);
        }
    }

    /**
     * Use save data into Model
     *
     * @param Request $request
     * @param Int $id
     * @return Boolean
     */
//    public function update($id, $data, $filter = null)
//    {
//        $user = $this->model->WHERE('id', $id);
//        if ($filter)
//            $user = $user->WHERE($filter);
//        $user = $user->update($data);
//
//        return $user;
//    }

    public function updatePassword($id, $data, $filter = null)
    {
        $user = $this->model->WHERE('id', $id);
        if ($filter)
            $user = $user->WHERE($filter);
        return $user = $user->update($data);
    }

    public function syncRole($user, $role)
    {
        $roleValue = $this->resolveRole($role);
        if ($roleValue) {
            $user->syncRoles($roleValue);
        }
    }

    protected function resolveRole($role)
    {
        if (!$role) {
            return null;
        }

        if (is_numeric($role)) {
            $roleModel = Role::find($role);
            return $roleModel?->name;
        }

        if ($role instanceof \Spatie\Permission\Models\Role) {
            return $role->name;
        }

        return $role;
    }

    public function restore(Request $request, $id = null)
    {
        $user = $this->find($id, ['*'], [], [], [], $trashed = true);
        $restored = $user->restore();
        return $restored;
    }


    /**
     * Remove data from the Repository
     *
     * @param Request $request
     * @param Int $id
     * @return Boolean
     */
//    public function delete($id, $request)
//    {
//        $delete = $this->model->newQuery();
//        if ($request)
//            $delete = $delete->where($request);
//        if ($delete->findOrFail($id)->delete())
//            return true;
//        return false;
//    }

    /**
     * Return searchable fields
     *
     * @return array
     */
    public function getFieldsSearchable() : array
    {
        return $this->fieldSearchable;
    }

    /**
     * Configure the Model
     **/
    public function model() : string
    {
            return User::class;
    }

}
