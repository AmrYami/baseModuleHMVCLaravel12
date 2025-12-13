<?php

namespace Users\Http\Controllers;

use Users\Http\Requests\UpdateUserProfileAwardRequest;
use Users\Models\Topic;
use App\Services\ActivityLogServiceShow;
use Illuminate\Contracts\Support\Renderable;
use Users\Http\Requests\CreateUserRequest;
use Users\Http\Requests\UpdateUserPasswordRequest;
use Users\Http\Requests\UpdateUserRequest;
use Users\Http\Requests\UpdateUserProfileRequest;
use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Users\Models\User;
use Users\Services\UserServiceShow;
use Users\Services\UserServiceStore;

class UsersController extends BaseController
{

    public function __construct(
        protected UserServiceShow        $serviceShow,
        protected UserServiceStore       $userServiceStore,
        protected ActivityLogServiceShow $activityLogServiceShow
    )
    {
    }

    /**
     * Display a listing of the user.
     *
     * @param Request $request
     *
     * @return Renderable
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
        $users = $this->serviceShow->dataTable($request, \Users\Http\Resources\UserResource::class);
        $data = $users['data'];
        unset($users['data']);
        return jsonMessage(data: $data, extra: $users );
    }


    /**
     * Display a listing of the paramedics.
     *
     * @param Request $request
     *
     * @return Renderable
     */
    public function paramedics(Request $request): Renderable
    {
        $request->merge(['speciality_experience' => 'yes']);
        $users = $this->serviceShow->find_by($request);
        return view('users::users.paramedics')
            ->with('users', $users)
            ->with('action', 'users');
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
            'action' => 'create'
        ]);
    }

    /**
     * Show the form for creating a new user.
     *
     * @return Renderable
     */
    public function createForm($id, Request $request)
    {
        $user = $this->serviceShow->find($id, $request);

        $Topic = Topic::where('active', true)->orderBy('order')->get();
        return view('users::users.show_form')->with([
            'topics' => $Topic ?? '',
            'user' => $user ?? [],
        ]);
    }

    /**
     * Store a newly created user in storage.
     *
     * @param CreateUserRequest $request
     *
     * @return object
     */
    public function store(CreateUserRequest $request, $fromFortiFy = false): object|bool
    {
        try {
            $user = $this->userServiceStore->save($request);
            if ($fromFortiFy)
                return $user;
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
            if ($fromFortiFy)
                return $user;
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
     * @param int $id
     *
     * @return object
     */
    public function show(int $id): object
    {
        $user = $this->serviceShow->find($id);
        if (empty($user)) {
            Flash::error('User not found');

            return redirect(route('dashboard.users.index'));
        }
        return view('users::users.show')->with('user', $user);
    }

    /**
     * Show the form for editing the user.
     *
     * @param int $id
     *
     * @return Renderable
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

    public function activitiesDoctor($id, Request $request)
    {
        $activities = [];
        $request->merge(['request_to' => auth()->user()->id]);
        $doctor = $this->serviceShow->find($id, $request);
        $HRList = $this->serviceShow->listUsersHasRole('HR');
        $marketingList = $this->serviceShow->listUsersHasRole('Marketing');
        $approvalUsers = $this->serviceShow->listUsersHasPemission('approve-profile');
//        $cycleRequests = $this->requestServiceShow->find_by($request);
//        $myRequests = $this->requestServiceShow->myRequests($request);

        if ($doctor) {
            $request->merge([
                'subject_type' => User::class,
                'subject_id' => $id
            ]);
            $activities = $this->activityLogServiceShow->find_by($request);
        }
        $fields = array_diff($doctor->getFillable(), [
            'password',
            'remember_token',
            'two_factor_secret',
            'two_factor_recovery_codes',
            'profile_photo_path',
            'current_team_id',
            'freeze',
            'type',
            'code',
            'banned_until',
            'two_factor_confirmed_at',
        ]);


        $translatable = $doctor->getTranslatableAttributes();

        return view('users::users.show_doctor',
            compact('doctor', 'activities', 'fields', 'translatable', 'HRList', 'marketingList', 'approvalUsers'));
    }

    /**
     * Update the specified user in storage.
     *
     * @param int $id
     * @param UpdateUserRequest $request
     *
     * @return object|bool
     */
    public function update($id, UpdateUserRequest $request): object|bool
    {
//        try {
        $request->merge([
            'type' => 'user'
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
     * @param int $id
     * @param UpdateUserRequest $request
     *
     * @return object|bool
     */
    public function updateUser($id, Request $request): object|bool
    {
        if (!$id)
            $id = auth()->user()->id;
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
            return false;
        }

    }

    /**
     * @param $id
     * @param UpdateUserPasswordRequest $request
     * @return \Illuminate\Http\RedirectResponse
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
     * @param int $id
     *
     * @return Response
     * @throws \Exception
     *
     */
    public function destroy(Request $request, $id)
    {
        if ($id == 1)
            return back()->withErrors(__('common.You cant delete this user'));
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
     * @param int $id
     * @param UpdateUserProfileRequest $request
     *
     * @return Response
     */
    public function updateMyProfile($id, UpdateUserProfileRequest $request)
    {
        $user = $this->userServiceStore->update($id, $request);
        if ($user) {
            return redirect()->back()->with('updated', __('messages.Updated', ['thing' => 'Your Profile']));
        } else {
            return back()->withErrors(__('common.Sorry But there Was an issue in saving Data please try again'));
        }
    }


    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|object
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
     * @param int $id
     *
     * @return Response
     * @throws \Exception
     *
     */
    public function freeze(Request $request, $id)
    {
        $res = $this->userServiceStore->freeze($request, $id);
        if ($res) {
            return redirect()->route('dashboard.users.index')->with('deleted', __('messages.Freezed', ['thing' => 'User']));
        }
        return back()->withErrors(__('common.Sorry But there Was an issue in saving Data please try again'));
    }

    /**
     * Show freeze form for a user (forever or until date).
     */
    public function freezeForm($id, Request $request)
    {
        $user = $this->serviceShow->find($id, $request);
        abort_if(!$user, 404);
        return view('users::users.freeze_form', [
            'user' => $user,
        ]);
    }

    /**
     * activate user.
     *
     * @param int $id
     *
     * @throws \Exception
     *
     */
    public function activate(Request $request, $id)
    {
        $activate = $this->userServiceStore->activate($request, $id);
        if ($activate) {
            return redirect()->back()->with(__('messages.activate', ['thing' => 'User']));
        } else {
            return back()->withErrors(__('common.Sorry But there Was an issue in saving Data please try again'));
        }
    }

    /**
     * approve user.
     *
     * @param int $id
     *
     * @throws \Exception
     *
     */
    public function approve(Request $request, $id)
    {
        $activate = $this->userServiceStore->approve($request, $id);
        if ($activate) {
            return redirect()->back()->with(__('messages.approve', ['thing' => 'User']));
        } else {
            return back()->withErrors(__('common.Sorry But there Was an issue in saving Data please try again'));
        }
    }

    /**
     * de activate user.
     *
     * @param int $id
     *
     * @throws \Exception
     *
     */
    public function deActivate(Request $request, $id)
    {
        $activate = $this->userServiceStore->deActivate($request, $id);
        if ($activate) {
            return redirect()->back()->with(__('messages.activate', ['thing' => 'User']));
        } else {
            return back()->withErrors(__('common.Sorry But there Was an issue in saving Data please try again'));
        }
    }

    /**
     * unApprove user.
     *
     * @param int $id
     *
     * @throws \Exception
     *
     */
    public function unApprove(Request $request, $id)
    {
        $unApprove = $this->userServiceStore->unApprove($request, $id);
        if ($unApprove) {
            return redirect()->back()->with(__('messages.unApprove', ['thing' => 'User']));
        } else {
            return back()->withErrors(__('common.Sorry But there Was an issue in saving Data please try again'));
        }
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function un_freeze(Request $request, $id)
    {
        $delete = $this->userServiceStore->un_freeze($request, $id);
        if ($delete) {
            return redirect()->back()->with('deleted', __('messages.Un-Freezed', ['thing' => 'User']));
        } else {
            return back()->withErrors(__('common.Sorry But there Was an issue in saving Data please try again'));
        }
    }

    public function requestDoctorToCompleteProfile()
    {

    }


}
