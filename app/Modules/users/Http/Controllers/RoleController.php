<?php

namespace Users\Http\Controllers;

use App\Http\Controllers\BaseController;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Users\Http\Requests\CreateRoleRequest;
use Users\Http\Requests\UpdateRoleRequest;
use Users\Services\RoleServiceShow;
use Users\Services\RoleServiceStore;

class RoleController extends BaseController
{
    /**
     * @var RoleServiceShow
     */
    private $serviceShow;

    /**
     * @var RoleServiceStore
     */
    private $roleServiceStore;

    public function __construct(RoleServiceShow $serviceShow, RoleServiceStore $roleServiceStore)
    {
        $this->serviceShow = $serviceShow;
        $this->roleServiceStore = $roleServiceStore;
    }

    /**
     * Display a listing of the Role.
     *
     *
     * @return Response
     */
    public function index(Request $request): Renderable
    {
        $roles = $this->serviceShow->find_by($request);

        return view('users::roles.index')
            ->with('roles', $roles);
    }

    /**
     * Show the form for creating a new Role.
     *
     * @return Response
     */
    public function create(): Renderable
    {
        return view('users::roles.create', [
            'action' => 'create',
            'active' => 'Users Roles',
        ]);
    }

    /**
     * Store a newly created Role in storage.
     *
     * @return Response
     */
    public function store(CreateRoleRequest $request): RedirectResponse
    {

        $role = $this->roleServiceStore->save($request);
        if ($role) {
            Artisan::call('permission:cache-reset');

            return redirect()->route('dashboard.roles.index')->with('created', __('messages.Created', ['thing' => 'User Role']));
        } else {
            return back()->withErrors(__('common.Sorry But You Should Select Permission To Role'));
        }

    }

    /**
     * Display the specified Role.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        abort(404, "Role {$id} is not available for display.");
    }

    /**
     * Show the form for editing the specified Role.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id, Request $request)
    {
        if ($id == 1) {
            return back();
        }
        $role = $this->serviceShow->find($id, $request);
        $user = $request->user();
        if (! $user || (! $user->hasRole('CRM Admin') && ! $role->users->contains($user))) {
            return back()->withErrors(__('You can only edit roles you are assigned to.'));
        }
        $rolePermissions = $role->permissions->pluck('id');
        $selected = $rolePermissions;

        return view('users::roles.edit', [
            'role' => $role,
            'rolePermissions' => $rolePermissions,
            'selected' => $selected,
            'action' => 'edit',
            'active' => 'Users Roles',
        ]);
    }

    /**
     * Update the specified Role in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id, UpdateRoleRequest $request)
    {
        try {
            if ($id == 1) {
                return back();
            }
            $roleModel = $this->serviceShow->find($id, $request);
            $user = $request->user();
            if (! $user || (! $user->hasRole('CRM Admin') && ! $roleModel->users->contains($user))) {
                return back()->withErrors(__('You can only edit roles you are assigned to.'));
            }

            $role = $this->roleServiceStore->update($id, $request);
            if ($role) {
                Artisan::call('permission:cache-reset');

                return redirect()->route('dashboard.roles.index')->with('updated', __('messages.Updated', ['thing' => 'User Role']));
            } else {
                return back()->withErrors(__('common.Sorry But there Was an issue in saving Data please try again'));
            }
        } catch (\Exception $exception) {
            report($exception);

            return back()->withErrors(__('common.Sorry But there Was an issue in saving Data please try again'));
        }

    }

    /**
     * Remove the specified Role from storage.
     *
     * @param  int  $id
     * @return Response
     *
     * @throws \Exception
     */
    public function destroy(Request $request, $id)
    {
        if ((int) $id === 1) {
            return back()->withErrors(__('You cannot delete the base role.'));
        }

        $role = $this->serviceShow->find($id, $request);
        $user = $request->user();
        if (! $user || (! $user->hasRole('CRM Admin') && ! $role->users->contains($user))) {
            return back()->withErrors(__('You can only delete roles you are assigned to.'));
        }

        $delete = $this->roleServiceStore->delete($request, $id);
        if ($delete) {
            return redirect()->route('dashboard.roles.index')->with('deleted', __('messages.Deleted', ['thing' => 'User Role']));
        } else {
            return redirect()->route('dashboard.roles.index')->withErrors(__('messages.You can\'t delete role that has users!!'));
        }
    }
}
