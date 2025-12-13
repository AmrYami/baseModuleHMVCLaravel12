<?php

namespace Users\Services;

use App\Models\Exam\Exam;
use App\Abstratctions\Service;
use App\Facades\MediaFacade;
use App\Interfaces\ServiceStore;
use App\Mail\MailService;
use App\Repositories\ApplicationStatusRepositoryStore;
use App\Repositories\SettingRepositoryShow;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Mockery\Exception;
use Users\Models\User;
use Users\Repositories\UserRepositoryShow;
use Users\Repositories\UserRepositoryStore;

/**
 *
 */
class UserServiceStore extends Service implements ServiceStore
{

    /**
     * Create a new Repository instance.
     *
     * @param UserRepositoryStore $userRepositoryStore
     * @param UserRepositoryShow $userRepositoryShow
     */
    public function __construct(UserRepositoryStore              $userRepositoryStore, UserRepositoryShow $userRepositoryShow, User $model,
                                NotificationService              $notificationService, SettingRepositoryShow $settingRepositoryShow,
                                ApplicationStatusRepositoryStore $applicationStatusRepositoryStore)
    {
        $this->userRepositoryStore = $userRepositoryStore;
        $this->userRepositoryShow = $userRepositoryShow;
        $this->model = $model;
        $this->settingRepositoryShow = $settingRepositoryShow;
        $this->notificationService = $notificationService;
        $this->applicationStatusRepositoryStore = $applicationStatusRepositoryStore;

    }

    /**
     * Use save data into Repository
     *
     * @param Request $request
     * @return Boolean
     */
    public function save(Request $request)
    {
        $statusInput = $request->input('status');
        $status = $this->normalizeStatus($statusInput);
        if ($status === null) {
            $status = getDefaultRegistrationStatus();
        }

        $request->merge([
            'type' => 'user',
            'status' => $status,
            'code' => uniqid(),
            'created_by' => $request->user()?->id,
        ]);
        $payload = $request->all();
        $payload['role'] = $request->input('role');
        $user = $this->userRepositoryStore->save($payload);
        //assign role

        if ($user) {
            $this->userRepositoryStore->assignRole($user, $request->role ?? "User");
            $setting = $this->settingRepositoryShow->find_by(['key' => 'notifications']);
            try {
                if ($setting) {
                    $notify = json_decode($setting[0]->value);
                    //    send mail to new accounts
                    if ($notify && $notify->new_account == 'on') {
                        try {
                            $this->processSendMails($user, $request);
                        } catch (\Exception $exception) {

                        }
                    }
                }
            } catch (\Exception $exception) {
            }


            MediaFacade::mediafiles($request, $user);
        }
        return $user;

    }


    /**
     * @param $user
     * @param $request
     * @param $queue
     * @return void
     */
    public function processSendMails($user, $request, $queue = false): void
    {
        if ($user->slug === 0 || $user->slug === "0") {
            $validateEmail = false;
        } elseif ($user->slug === 1 || $user->slug === "1") {
            $validateEmail = true;
        } else {
            $validateEmail = validateMail($user->email);
        }
        if ($validateEmail) {
            $mailService = new MailService(
                subject: 'Welcome Email',
                template: 'welcome',
                data: ['name' => $user->name, 'email' => $user->email]
            );
            $mailService->sendMail($user->email, false);
        }
    }


    /**
     * @param $user
     * @param $request
     * @param $queue
     * @return void
     */
    public function processApprovedMail($user, $request, $queue = false): void
    {
        if ($user->slug === 0 || $user->slug === "0") {
            $validateEmail = false;
        } elseif ($user->slug === 1 || $user->slug === "1") {
            $validateEmail = true;
        } else {
            $validateEmail = validateMail($user->email);
        }
        if ($validateEmail) {
            $mailService = new MailService(
                subject: 'Competency Exam',
                template: 'competency_exam',
                data: ['examLink' => route('dashboard.exams.start')]
            );
            $mailService->sendMail($user->email, false);
        }
    }

    /**
     * @param $user
     * @param $request
     * @param $queue
     * @return void
     */
    public function userAcceptContract($user, $request, $queue = false): void
    {
        if ($user->slug === 0 || $user->slug === "0") {
            $validateEmail = false;
        } elseif ($user->slug === 1 || $user->slug === "1") {
            $validateEmail = true;
        } else {
            $validateEmail = validateMail($user->email);
        }
        if ($validateEmail) {
            $mailService = new MailService(
                subject: 'congratulation',
                template: 'user_accept_contract',
                data: ['link' => route('dashboard')]
            );
            $mailService->sendMail($user->email, false);
        }
    }

    /**
     * @param $user
     * @param $request
     * @param $queue
     * @return void
     */
    public function processSendOfferMail($user, $request, $queue = false): void
    {
        if ($user->slug === 0 || $user->slug === "0") {
            $validateEmail = false;
        } elseif ($user->slug === 1 || $user->slug === "1") {
            $validateEmail = true;
        } else {
            $validateEmail = validateMail($user->email);
        }
        if ($validateEmail) {
            $mailService = new MailService(
                subject: 'Contract',
                template: 'contract',
                data: ['link' => route('dashboard'), 'name' => $user->first_name]
            );
            $res = $mailService->sendMail($user->email, false);
        }
    }

    /**
     * @param $user
     * @param $request
     * @param $queue
     * @return void
     */
    public function processRejectedMailDoesntMeetRequirements($user, $request, $queue = false): void
    {
        if ($user->slug === 0 || $user->slug === "0") {
            $validateEmail = false;
        } elseif ($user->slug === 1 || $user->slug === "1") {
            $validateEmail = true;
        } else {
            $validateEmail = validateMail($user->email);
        }
        if ($validateEmail) {
            $mailService = new MailService(
                subject: 'Reject Email',
                template: 'application_rejected_doesnt_meed_requirements',
                data: []
            );
            $mailService->sendMail($user->email, false);
        }
    }

    /**
     * @param $user
     * @param $request
     * @param $queue
     * @return void
     */
    public function processRejectedMailAfterCompetency($user, $request, $queue = false): void
    {
        if ($user->slug === 0 || $user->slug === "0") {
            $validateEmail = false;
        } elseif ($user->slug === 1 || $user->slug === "1") {
            $validateEmail = true;
        } else {
            $validateEmail = validateMail($user->email);
        }
        if ($validateEmail) {
            $mailService = new MailService(
                subject: 'Reject Email',
                template: 'application_rejected_after_competency',
                data: ['name' => $user->name, 'email' => $user->email]
            );
            $mailService->sendMail($user->email, false);
        }
    }


    /**
     * @param $id
     * @param Request $request
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    public function update($id, Request $request)
    {
        $decodedId = hid_decode($id) ?? $id;
        $data = $request->only($this->model->getFillable());
        $userObject = $this->userRepositoryShow->find($decodedId);
        if ($userObject->approve == 1 && auth()->user()->hasRole('User'))
            return false;
        $user = $this->userRepositoryStore->update($decodedId, $data);
        if ($user) {
            if ($request->role)
                $this->userRepositoryStore->syncRole($userObject, $request->role);
            MediaFacade::mediafiles($request, $userObject);
        }
        return $user;
    }

    public function updateUser($id, Request $request)
    {
        $decodedId = hid_decode($id) ?? $id;
        $userObject = $this->userRepositoryShow->find($decodedId);
//        $data = $request->only($this->model->getFillable());
        if ($request->religion != 'muslim') {
            $request->merge(['approve' => 0]);
            $this->applicationStatusRepositoryStore->save([
                'status' => 'rejected',
                'reason' => 'non-muslim',
                'user_id' => $decodedId,
            ]);
            try {
                $this->processRejectedMailDoesntMeetRequirements($userObject, $request);
            } catch (\Exception $exception) {

            }
        }
//        if ($userObject->approve == 1)
//            return false;
        $user = $this->userRepositoryStore->update($decodedId, $request->all());
        if ($user) {
//            if ($request->role)
//                $this->userRepositoryStore->syncRole($userObject, $request->role);
            MediaFacade::mediafiles(new Request($request->only(['delete', 'avatar'])), $userObject);
        }
        return $user;

    }

    /**
     * @param $id
     * @param Request $request
     * @return false|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
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
     * @param Request $request
     * @param Int $id
     * @return Boolean
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
     * @param Request $request
     * @param Int $id
     * @return Boolean
     */
    public function freeze(Request $request, $id = null)
    {
        $this->clean_request($request);
        $mode = $request->input('mode', 'forever'); // forever | until
        $until = $request->input('banned_until');

        $data = [];
        if ($mode === 'until' && $until) {
            // Time-bound suspension: rely on banned_until; do not set freeze flag so it auto-unlocks after date
            $data['banned_until'] = \Carbon\Carbon::parse($until);
            $data['freeze'] = 0; // ensure auto unfreeze by date
        } else {
            // Forever until admin unfreezes
            $data['freeze'] = 1;
            $data['banned_until'] = null;
        }

        return $this->userRepositoryStore->update($id, $data, $request->all());
    }

    /**
     * activate data from the Repository
     *
     * @param Request $request
     * @param Int $id
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
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
     * @param Request $request
     * @param Int $id
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */

    public function approve(Request $request, $id = null)
    {
        $this->clean_request($request);
        $data = ['approve' => true];
        $res = $this->userRepositoryStore->update($id, $data, $request->all());
        $this->applicationStatusRepositoryStore->save([
            'status' => 'approved',
            'reason' => 'Made By ' . auth()->user()->user_name,
            'user_id' => $id,
            'changed_by' => auth()->user()->id,
        ]);
        $user = $this->userRepositoryShow->find($id);
        try {
            $this->processApprovedMail($user, $request);
        } catch (\Exception $exception) {

        }

        return $res;
    }

    /**
     * de activate data from the Repository
     *
     * @param Request $request
     * @param Int $id
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
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
     * @param Request $request
     * @param Int $id
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */

    public function unApprove(Request $request, $id = null)
    {
        $this->clean_request($request);
        $data = ['approve' => false];
        $this->applicationStatusRepositoryStore->save([
            'status' => 'rejected',
            'reason' => 'Made By ' . auth()->user()->user_name,
            'user_id' => $id,
            'changed_by' => auth()->user()->id,
        ]);
        $activate = $this->userRepositoryStore->update($id, $data, $request->all());
        $user = $this->userRepositoryShow->find($id);
        try {
            $this->processRejectedMailDoesntMeetRequirements($user, $request);
        } catch (\Exception $exception) {

        }
        return $activate;
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    public function un_freeze(Request $request, $id = null)
    {
        $data = ['freeze' => 0, 'banned_until' => null];
        $this->clean_request($request);
        $delete = $this->userRepositoryStore->update($id, $data, $request->all());
        return $delete;
    }

    /**
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function restore(Request $request, $id = null)
    {
        $restored = $this->repo->restore($request, $id);
        return $restored;
    }

    public function assignToExam(User $user, Exam $exam): void
    {

        $user->exams()->attach($exam->id, [
            "attempts" => 0,
            "remaining_time" => $exam->duration,
            'active' => 1,
        ]);
        storeLogs('$user 342', $user);
//        $this->userServiceStore->sendMail(
//            $user->email,
//            'Competency Exam ... ',
//            'competency_exam',
//            ['examLink' => route('dashboard.exams.start')],
//            true,
//        );

//        $mailService = new MailService(
//            subject: 'Competency Exam',
//            template: 'competency_exam',
//            data: ['examLink' => route('dashboard.exams.start')]
//        );
//        $mailService->sendMail($user->email, false);
        storeLogs('$mailService 357', '$mailService');
//        $this->processApprovedMail($user, $exam);
    }
}
