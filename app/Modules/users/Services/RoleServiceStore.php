<?php

namespace Users\Services;

use App\Abstratctions\Service;
use App\Interfaces\ServiceStore;
use Illuminate\Http\Request;
use Users\Models\Role;
use Users\Repositories\RoleRepositoryShow;
use Users\Repositories\RoleRepositoryStore;

class RoleServiceStore extends Service implements ServiceStore
{
    public $repo;
    /**
     * @var RoleRepositoryStore
     */
    private $roleRepositoryStore;
    /**
     * @var RoleRepositoryShow
     */
    private $roleRepositoryShow;
    /**
     * @var Role
     */
    private $model;

    /**
     * Create a new Repository instance.
     *
     * @param RoleRepositoryStore $roleRepositoryStore
     * @param RoleRepositoryShow $roleRepositoryShow
     */
    public function __construct(RoleRepositoryStore $roleRepositoryStore, RoleRepositoryShow $roleRepositoryShow, Role $model)
    {
        $this->roleRepositoryStore = $roleRepositoryStore;
        $this->roleRepositoryShow = $roleRepositoryShow;
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
//        try {
            $request->request->add(['guard_name' => 'web']);
            $data = $request->only($this->model->getFillable());
            $role = $this->roleRepositoryStore->create($data);
            $this->roleRepositoryStore->syncPermissions($request->selected, $role);
            return $role;
//        } catch (\Exception $exception) {
//            return false;
//        }
    }

    /**
     * Use save data into Repository
     *
     * @param Request $request
     * @return Boolean
     */
    public function update($id, Request $request)
    {
        $data = $request->only($this->model->getFillable());
        $role = $this->roleRepositoryStore->update($id, $data);
        if ($role) {
            $roleObject = $this->roleRepositoryShow->find($id);
            $this->roleRepositoryStore->syncPermissions($request->selected, $roleObject);
        }
        return $role;
//
    }

    /**
     * Remove data from the Repository
     *
     * @param Request $request
     * @param Int $id
     * @return Boolean
     */
    public function delete(Request $request, $id = null)
    {
        $this->clean_request($request);
        $delete = $this->roleRepositoryStore->delete($id, $request->all());
        return $delete;
    }

    /**
     * freeze data from the Repository
     *
     * @param Request $request
     * @param Int $id
     * @return Boolean
     */
    public function freeze(Request $request, $id = null)
    {
        $data = ['freeze' => 1];
        $this->clean_request($request);
        return $this->roleRepositoryStore->update($id, $data, $request->all());
    }

    public function restore(Request $request, $id = null)
    {
        return $this->repo->restore($request, $id);
    }
}

