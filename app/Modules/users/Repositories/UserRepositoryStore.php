<?php

namespace Users\Repositories;

use App\Interfaces\BaseRepositoryInterface;
use App\Interfaces\RepositoryStore;
use App\Repositories\BaseRepositoryStore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use InvalidArgumentException;
use Spatie\Permission\Models\Role;
use Users\Models\User;

/**
 * Class Repository
 *
 * @version December 11, 2019, 2:33 pm UTC
 */
class UserRepositoryStore extends BaseRepositoryStore implements BaseRepositoryInterface, RepositoryStore
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
        'updated_at',
    ];

    /**
     * Use save data into Model
     *
     * @param  Request  $request
     * @return bool
     */
    public function save($data)
    {
        $data['password'] = Hash::make($data['password']);
        $user = $this->create($data);

        return $user;
    }

    public function assignRole($user, $role)
    {
        if ($role) {
            $guard = $this->guardForUser($user);
            $roleModel = $this->resolveRoleModel($role, $guard);
            if (! $roleModel) {
                throw new InvalidArgumentException("Role [{$this->roleLabel($role)}] is not configured for guard [{$guard}].");
            }

            $user->assignRole($roleModel);
        }
    }

    /**
     * Use save data into Model
     *
     * @param  Request  $request
     * @param  int  $id
     * @return bool
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
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user = $this->model->WHERE('id', $id);
        if ($filter) {
            $user = $user->WHERE($filter);
        }

        return $user->update($data);
    }

    public function syncRole($user, $role)
    {
        if ($role) {
            $guard = $this->guardForUser($user);
            $roleModel = $this->resolveRoleModel($role, $guard);
            if (! $roleModel) {
                throw new InvalidArgumentException("Role [{$this->roleLabel($role)}] is not configured for guard [{$guard}].");
            }

            $user->syncRoles($roleModel);
        }
    }

    protected function resolveRoleModel($role, string $guard): ?Role
    {
        if ($role instanceof Role) {
            return $role->guard_name === $guard ? $role : null;
        }

        if (is_numeric($role)) {
            return Role::whereKey($role)
                ->where('guard_name', $guard)
                ->first();
        }

        return Role::where('name', $role)
            ->where('guard_name', $guard)
            ->first();
    }

    protected function guardForUser($user): string
    {
        return property_exists($user, 'guard_name') && $user->guard_name
            ? $user->guard_name
            : config('auth.defaults.guard', 'web');
    }

    protected function roleLabel($role): string
    {
        if ($role instanceof Role) {
            return $role->name;
        }

        if (! is_scalar($role)) {
            return get_debug_type($role);
        }

        return (string) $role;
    }

    public function restore(Request $request, $id = null)
    {
        $user = $this->model->withTrashed()->findOrFail($id);

        return $user->restore();
    }

    /**
     * Remove data from the Repository
     *
     * @param  Request  $request
     * @param  int  $id
     * @return bool
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
        return User::class;
    }
}
