<?php

namespace Users\Services;

use App\Abstratctions\Service;
use App\Facades\MediaFacade;
use App\Interfaces\ServiceStore;
use App\Mail\MailService;
use App\Repositories\SettingRepositoryShow;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Users\Models\User;
use Users\Repositories\UserRepositoryShow;
use Users\Repositories\UserRepositoryStore;

class UserServiceStore extends Service implements ServiceStore
{
    /**
     * Create a new Repository instance.
     */
    public function __construct(
        protected UserRepositoryStore $userRepositoryStore,
        protected UserRepositoryShow $userRepositoryShow,
        protected User $model,
        protected NotificationService $notificationService,
        protected SettingRepositoryShow $settingRepositoryShow,
    ) {}

    /**
     * Use save data into Repository
     *
     * @return bool
     */
    public function save(Request $request)
    {
        $statusInput = $request->input('status');
        $status = $this->normalizeStatus($statusInput);
        if ($status === null) {
            $status = getDefaultRegistrationStatus();
        }

        $payload = $request->only([
            'name',
            'user_name',
            'email',
            'mobile',
            'password',
        ]);
        $payload = array_merge($payload, [
            'type' => 'user',
            'status' => $status,
            'code' => Str::random(40),
            'created_by' => $request->user()?->id,
        ]);
        $payload['role'] = $request->user()?->hasRole('CRM Admin')
            ? $request->input('role', 'User')
            : 'User';
        $user = $this->userRepositoryStore->save($payload);
        // assign role

        if ($user) {
            $this->userRepositoryStore->assignRole($user, $payload['role']);
            $setting = $this->settingRepositoryShow->find_by(['key' => 'notifications']);
            try {
                if ($setting) {
                    $notify = json_decode($setting[0]->value);
                    //    send mail to new accounts
                    if ($notify && $notify->new_account == 'on') {
                        try {
                            $this->processSendMails($user);
                        } catch (\Exception $exception) {
                            logger()->error('Failed to send new account notification email.', [
                                'exception' => $exception,
                            ]);
                        }
                    }
                }
            } catch (\Exception $exception) {
                logger()->error('Failed to process new account notification setting.', [
                    'exception' => $exception,
                ]);
            }

            MediaFacade::mediafiles($request, $user);
        }

        return $user;

    }

    private function shouldValidateEmail($user): bool
    {
        if ($user->slug === 0 || $user->slug === '0') {
            return false;
        }

        if ($user->slug === 1 || $user->slug === '1') {
            return true;
        }

        return validateMail($user->email);
    }

    public function processSendMails($user): void
    {
        if ($this->shouldValidateEmail($user)) {
            $mailService = new MailService(
                subject: 'Welcome Email',
                template: 'welcome',
                data: ['name' => $user->name, 'email' => $user->email]
            );
            $mailService->sendMail($user->email, false);
        }
    }

    public function userAcceptContract($user): void
    {
        if ($this->shouldValidateEmail($user)) {
            $mailService = new MailService(
                subject: 'congratulation',
                template: 'user_accept_contract',
                data: ['link' => route('dashboard')]
            );
            $mailService->sendMail($user->email, false);
        }
    }

    public function processSendOfferMail($user): void
    {
        if ($this->shouldValidateEmail($user)) {
            $mailService = new MailService(
                subject: 'Contract',
                template: 'contract',
                data: ['link' => route('dashboard'), 'name' => $user->first_name]
            );
            $mailService->sendMail($user->email, false);
        }
    }

    public function processRejectedMailDoesntMeetRequirements($user): void
    {
        if ($this->shouldValidateEmail($user)) {
            $mailService = new MailService(
                subject: 'Reject Email',
                template: 'application_rejected_doesnt_meed_requirements',
                data: []
            );
            $mailService->sendMail($user->email, false);
        }
    }

    public function processRejectedMailAfterCompetency($user): void
    {
        if ($this->shouldValidateEmail($user)) {
            $mailService = new MailService(
                subject: 'Reject Email',
                template: 'application_rejected_after_competency',
                data: ['name' => $user->name, 'email' => $user->email]
            );
            $mailService->sendMail($user->email, false);
        }
    }

    /**
     * @return Builder|Builder[]|Collection|Model
     */
    public function update($id, Request $request)
    {
        $decodedId = hid_decode($id) ?? $id;
        $data = $request->only($this->model->getFillable());
        $userObject = $this->userRepositoryShow->find($decodedId);
        $currentUser = $request->user() ?? auth()->user();
        if ($userObject->approve == 1 && $currentUser?->hasRole('User')) {
            return false;
        }
        $role = $request->input('role');
        if ($role) {
            abort_unless($currentUser?->hasRole('CRM Admin'), 403);
        }
        $user = $this->userRepositoryStore->update($decodedId, $data);
        if ($user) {
            if ($role) {
                $this->userRepositoryStore->syncRole($userObject, $role);
            }
            MediaFacade::mediafiles($request, $userObject);
        }

        return $user;
    }

    public function updateUser($id, Request $request)
    {
        $decodedId = hid_decode($id) ?? $id;
        $userObject = $this->userRepositoryShow->find($decodedId);
        $data = method_exists($request, 'validated')
            ? $request->validated()
            : $request->only(['name', 'user_name', 'email', 'mobile']);
        $data['type'] = 'user';
        if ($request->user()?->hasRole('HR')) {
            $data['approve'] = 2;
        }
        $user = $this->userRepositoryStore->update($decodedId, $data);
        if ($user) {
            //            if ($request->role)
            //                $this->userRepositoryStore->syncRole($userObject, $request->role);
            MediaFacade::mediafiles($request, $userObject);
        }

        return $user;

    }

    /**
     * @return false|Builder|Builder[]|Collection|Model
     */
    public function updatePassword($id, Request $request)
    {
        $this->clean_request($request);
        try {
            $data = ['password' => Hash::make($request->password)];
            $user = $this->userRepositoryStore->update($id, $data);

            return $user;
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * Remove data from the Repository
     *
     * @param  int  $id
     * @return bool
     */
    public function delete(Request $request, $id = null)
    {
        $this->clean_request($request);
        $delete = $this->userRepositoryStore->delete($id, $request->all());

        return $delete;
    }

    protected function normalizeStatus($value): ?int
    {
        if (is_null($value) || $value === '') {
            return null;
        }

        if (is_numeric($value)) {
            return (int) $value === 1 ? 1 : 0;
        }

        $string = strtolower((string) $value);

        return $string === 'active' ? 1 : ($string === 'pending' ? 0 : null);
    }

    /**
     * freeze data from the Repository
     *
     * @param  int  $id
     * @return bool
     */
    public function freeze(Request $request, $id = null)
    {
        $this->clean_request($request);
        $mode = $request->input('mode', 'forever'); // forever | until
        $until = $request->input('banned_until');

        $data = [];
        if (! in_array($mode, ['forever', 'until'], true)) {
            throw ValidationException::withMessages([
                'mode' => __('Invalid freeze mode.'),
            ]);
        }

        if ($mode === 'until') {
            if (! $until) {
                throw ValidationException::withMessages([
                    'banned_until' => __('Invalid banned until date.'),
                ]);
            }

            // Time-bound suspension: rely on banned_until; do not set freeze flag so it auto-unlocks after date
            try {
                $bannedUntil = Carbon::parse($until);
            } catch (\Throwable) {
                throw ValidationException::withMessages([
                    'banned_until' => __('Invalid banned until date.'),
                ]);
            }
            if (! $bannedUntil->isFuture()) {
                throw ValidationException::withMessages([
                    'banned_until' => __('Invalid banned until date.'),
                ]);
            }
            $data['banned_until'] = $bannedUntil;
            $data['freeze'] = 0; // ensure auto unfreeze by date
        } elseif ($mode === 'forever') {
            // Forever until admin unfreezes
            $data['freeze'] = 1;
            $data['banned_until'] = null;
        }

        return $this->userRepositoryStore->update($id, $data, $request->all());
    }

    /**
     * activate data from the Repository
     *
     * @param  int  $id
     * @return Builder|Builder[]|Collection|Model
     */
    public function activate(Request $request, $id = null)
    {
        $this->clean_request($request);
        $data = ['status' => 1];

        return $this->userRepositoryStore->update($id, $data, $request->all());
    }

    /**
     * approve data from the Repository
     *
     * @param  int  $id
     * @return Builder|Builder[]|Collection|Model
     */
    public function approve(Request $request, $id = null)
    {
        $this->clean_request($request);
        $data = ['approve' => true];

        return $this->userRepositoryStore->update($id, $data, $request->all());
    }

    /**
     * de activate data from the Repository
     *
     * @param  int  $id
     * @return Builder|Builder[]|Collection|Model
     */
    public function deActivate(Request $request, $id = null)
    {
        $this->clean_request($request);
        $data = ['status' => 0];
        $activate = $this->userRepositoryStore->update($id, $data, $request->all());

        return $activate;
    }

    /**
     * unApprove data from the Repository
     *
     * @param  int  $id
     * @return Builder|Builder[]|Collection|Model
     */
    public function unApprove(Request $request, $id = null)
    {
        $this->clean_request($request);
        $data = ['approve' => false];

        return $this->userRepositoryStore->update($id, $data, $request->all());
    }

    /**
     * @return Builder|Builder[]|Collection|Model
     */
    public function un_freeze(Request $request, $id = null)
    {
        $data = ['freeze' => 0, 'banned_until' => null];
        $this->clean_request($request);
        $delete = $this->userRepositoryStore->update($id, $data, $request->all());

        return $delete;
    }

    /**
     * @return mixed
     */
    public function restore(Request $request, $id = null)
    {
        return $this->userRepositoryStore->restore($request, $id);
    }
}
