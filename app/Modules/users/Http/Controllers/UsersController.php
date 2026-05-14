<?php

namespace Users\Http\Controllers;

use App\Http\Controllers\BaseController;
use App\Services\ActivityLogServiceShow;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Users\Http\Requests\CreateUserRequest;
use Users\Http\Requests\UpdateUserPasswordRequest;
use Users\Http\Requests\UpdateUserProfileRequest;
use Users\Http\Requests\UpdateUserRequest;
use Users\Http\Resources\UserResource;
use Users\Models\User;
use Users\Services\UserServiceShow;
use Users\Services\UserServiceStore;

class UsersController extends BaseController
{
    public function __construct(
        protected UserServiceShow $serviceShow,
        protected UserServiceStore $userServiceStore,
        protected ActivityLogServiceShow $activityLogServiceShow
    ) {}

    /**
     * Display a listing of the user.
     */
    public function index(Request $request): Renderable
    {
        $users = $this->serviceShow->find_by($request, 10);

        return view('users::users.index')
            ->with('users', $users)
            ->with('action', 'users');
    }

    public function list(Request $request)
    {
        $users = $this->serviceShow->dataTable($request, UserResource::class);
        $data = $users['data'];
        unset($users['data']);

        return jsonMessage(data: $data, extra: $users);
    }

    /**
     * Show the form for creating a new user.
     *
     * @return Renderable
     */
    public function create(Request $request)
    {
        $status = [
            0 => 'pending',
            1 => 'active',
        ];

        return view('users::users.create', [
            'status' => $status,
            'action' => 'create',
            'defaultStatus' => getDefaultRegistrationStatus(),
        ]);
    }

    /**
     * Show the form for creating a new user.
     *
     * @return Renderable
     */
    public function createRelatedToCompany(Request $request)
    {
        $status = [
            0 => 'pending',
            1 => 'active',
        ];

        return view('users::company_users.create', [
            'status' => $status,
            'hasCompany' => true,
            'action' => 'create',
        ]);
    }

    /**
     * Store a newly created user in storage.
     *
     *
     * @return object
     */
    public function store(CreateUserRequest $request, $fromFortiFy = false): object|bool
    {
        try {
            $user = $this->userServiceStore->save($request);
            if ($fromFortiFy) {
                return $user;
            }
            if ($user) {
                return redirect()->route('dashboard.users.index')->with('created', __('messages.Created', ['thing' => 'User']));
            } else {
                return back()->withErrors(__('common.Sorry But there Was an issue in saving Data please try again'));
            }
        } catch (\Exception $exception) {
            if ($fromFortiFy) {
                throw $exception;
            }

            return back()->withErrors(__('common.Sorry But there Was an issue in saving Data please try again'));
        }
    }

    public function storeRelatedToCompany(CreateUserRequest $request, $fromFortiFy = false): object|bool
    {
        try {
            $request->merge([
                'company_id' => auth()->user()->company_id,
                'created_by' => auth()->user()->id,
            ]);
            $user = $this->userServiceStore->save($request);
            if ($fromFortiFy) {
                return $user;
            }
            if ($user) {
                return redirect()->route('dashboard.users.edit_profile', $user->id)->with('created', __('messages.Created', ['thing' => 'User']));
            } else {
                return back()->withErrors(__('common.Sorry But there Was an issue in saving Data please try again'));
            }
        } catch (\Exception $exception) {
            return back()->withErrors(__('common.Sorry But there Was an issue in saving Data please try again'));
        }
    }

    /**
     * Display the user.
     *
     *
     * @return object
     */
    public function show(int $id)
    {
        $user = $this->serviceShow->find($id);
        if (empty($user)) {
            return redirect(route('dashboard.users.index'))
                ->withErrors(['user' => 'User not found']);
        }

        return view('users::users.show')->with('user', $user);
    }

    /**
     * Show the form for editing the user.
     *
     * @param  int  $id
     */
    public function edit($id, Request $request): Renderable
    {
        $status = [
            0 => 'pending',
            1 => 'active',
        ];
        $user = $this->serviceShow->find($id, $request);
        $selectedRoleId = $user->roles->first()?->id;

        return view('users::users.edit', [
            'user' => $user,
            'status' => $status,
            'action' => 'edit',
            'selected' => $selectedRoleId,
            'defaultStatus' => getDefaultRegistrationStatus(),
        ]);
    }

    /**
     * Update the specified user in storage.
     *
     * @param  int  $id
     */
    public function update($id, UpdateUserRequest $request): object|bool
    {
        //        try {
        $request->merge([
            'type' => 'user',
        ]);

        $user = $this->userServiceStore->update($id, $request);
        if ($user) {
            return redirect()
                ->route('dashboard.users.index')
                ->with('updated', __('messages.Updated', ['thing' => 'User']));
        }

        return back()->withErrors(__('common.Sorry But there Was an issue in saving Data please try again'));
        //        } catch (\Exception $exception) {
        //            return false;
        //        }

    }

    /**
     * Update the specified user.
     *
     * @param  int  $id
     * @param  UpdateUserRequest  $request
     */
    public function updateUser($id, UpdateUserProfileRequest $request): object|bool
    {
        if (! $id) {
            $id = auth()->user()->id;
        }
        try {
            $request->merge([
                'type' => 'user',
            ]);
            if (auth()->user()->hasRole('HR')) {
                $request->merge([
                    'approve' => 2,
                ]);
            }
            $user = $this->userServiceStore->updateUser($id, $request);

            if ($user) {
                return redirect()->back()->with('updated', __('messages.Updated', ['thing' => 'User']));
            } else {
                return back()->withErrors(__('common.Sorry But there Was an issue in saving Data please try again'));
            }
        } catch (\Exception $exception) {
            report($exception);

            return back()->withErrors(__('common.Sorry But there Was an issue in saving Data please try again'));
        }

    }

    /**
     * @return RedirectResponse
     */
    public function update_password($id, UpdateUserPasswordRequest $request)
    {
        $decodedId = hid_decode($id) ?? $id;
        $user = $this->userServiceStore->updatePassword($decodedId, $request);
        if ($user) {
            return redirect()->back()->with('updated', __('messages.Updated', ['thing' => 'User']));
        } else {
            return back()->withErrors(__('common.Sorry But there Was an issue in saving Data please try again'));
        }
    }

    /**
     * Remove the specified user.
     *
     * @param  int  $id
     * @return Response
     *
     * @throws \Exception
     */
    public function destroy(Request $request, $id)
    {
        if ($id == 1) {
            return back()->withErrors(__('common.You cant delete this user'));
        }
        $delete = $this->userServiceStore->delete($request, $id);
        if ($delete) {
            return redirect()->back()->with('deleted', __('messages.Deleted', ['thing' => 'User']));
        } else {
            return back()->withErrors(__('common.Sorry But there Was an issue in saving Data please try again'));
        }
    }

    /**
     * Update user's profile.
     *
     * @param  int  $id
     * @return Response
     */
    public function updateMyProfile($id, UpdateUserProfileRequest $request)
    {
        $user = $this->userServiceStore->updateUser($id, $request);
        if ($user) {
            return redirect()->back()->with('updated', __('messages.Updated', ['thing' => 'Your Profile']));
        } else {
            return back()->withErrors(__('common.Sorry But there Was an issue in saving Data please try again'));
        }
    }

    /**
     * @return Factory|View|Application|object
     */
    public function myProfile(Request $request)
    {
        $fields = [];
        $translatable = [];
        //        if (auth()->user()->hasRole('Doctor')) {
        //            $user = $this->doctorServiceShow->find(auth()->user()->id, $request);
        //        } else {
        $user = $this->serviceShow->find(auth()->user()->id, $request);
        //        }
        $status = [
            0 => 'pending',
            1 => 'active',
        ];

        return view('users::users.profile', [
            'action' => 'my profile',
            'status' => $status,
            'user' => $user,
            'fields' => $fields,
            'translatable' => $translatable,
        ]);
    }

    /**
     * Freeze user.
     *
     * @param  int  $id
     * @return Response
     *
     * @throws \Exception
     */
    public function freeze(Request $request, $id)
    {
        $res = $this->userServiceStore->freeze($request, $id);
        if ($res) {
            return redirect()->route('dashboard.users.index')->with('deleted', __('messages.Frozen', ['thing' => 'User']));
        }

        return back()->withErrors(__('common.Sorry But there Was an issue in saving Data please try again'));
    }

    /**
     * Show freeze form for a user (forever or until date).
     */
    public function freezeForm($id, Request $request)
    {
        $user = $this->serviceShow->find($id, $request);
        abort_if(! $user, 404);

        return view('users::users.freeze_form', [
            'user' => $user,
        ]);
    }

    /**
     * activate user.
     *
     * @param  int  $id
     *
     * @throws \Exception
     */
    public function activate(Request $request, $id)
    {
        $activate = $this->userServiceStore->activate($request, $id);
        if ($activate) {
            return redirect()->back()->with('success', __('messages.activate', ['thing' => 'User']));
        } else {
            return back()->withErrors(__('common.Sorry But there Was an issue in saving Data please try again'));
        }
    }

    /**
     * approve user.
     *
     * @param  int  $id
     *
     * @throws \Exception
     */
    public function approve(Request $request, $id)
    {
        $activate = $this->userServiceStore->approve($request, $id);
        if ($activate) {
            return redirect()->back()->with('success', __('messages.approve', ['thing' => 'User']));
        } else {
            return back()->withErrors(__('common.Sorry But there Was an issue in saving Data please try again'));
        }
    }

    /**
     * de activate user.
     *
     * @param  int  $id
     *
     * @throws \Exception
     */
    public function deActivate(Request $request, $id)
    {
        $activate = $this->userServiceStore->deActivate($request, $id);
        if ($activate) {
            return redirect()->back()->with('success', __('messages.deactivate', ['thing' => 'User']));
        } else {
            return back()->withErrors(__('common.Sorry But there Was an issue in saving Data please try again'));
        }
    }

    /**
     * unApprove user.
     *
     * @param  int  $id
     *
     * @throws \Exception
     */
    public function unApprove(Request $request, $id)
    {
        $unApprove = $this->userServiceStore->unApprove($request, $id);
        if ($unApprove) {
            return redirect()->back()->with('success', __('messages.unApprove', ['thing' => 'User']));
        } else {
            return back()->withErrors(__('common.Sorry But there Was an issue in saving Data please try again'));
        }
    }

    /**
     * @return RedirectResponse
     */
    public function un_freeze(Request $request, $id)
    {
        $delete = $this->userServiceStore->un_freeze($request, $id);
        if ($delete) {
            return redirect()->back()->with('deleted', __('messages.Unfrozen', ['thing' => 'User']));
        } else {
            return back()->withErrors(__('common.Sorry But there Was an issue in saving Data please try again'));
        }
    }

    public function requestDoctorToCompleteProfile() {}
}
