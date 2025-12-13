<?php

namespace App\Actions\Fortify;

use App\Models\Team;
use Laravel\Fortify\Actions\EnableTwoFactorAuthentication;
use Laravel\Fortify\RecoveryCode;
use Laravel\Fortify\TwoFactorAuthenticationProvider;
use Users\Http\Controllers\UsersController;
use Users\Http\Requests\CreateUserRequest;
use Users\Models\User;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use PragmaRX\Google2FA\Google2FA;
use Carbon\Carbon;


class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    public function __construct(NotificationService $notificationService, User $user)
    {
        $this->notificationService = $notificationService;
        $this->user = $user;
    }

    /**
     * Create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        $request = new CreateUserRequest();

        $input['role'] = 'User';
        $input['status'] = getDefaultRegistrationStatus();
        $input['email_verified_at'] = Carbon::now();

//        $input = array_merge($input, ['name' => [
//            'ar' => $input['name'],
//           'en' => $input['name']
//        ]
//        ]);
        // Manually run the validation — merge input first so Rule::requiredIf can see 'role'
        $request->merge($input);
        $validator = Validator::make($input, $request->rules(), $request->messages(), $request->attributes());
        $validator->validate();

        $controller = app(UsersController::class);
       $result = $controller->store($request, true);

       // Send welcome email if enabled
       try {
           sendActionMail('welcome', $result, [
               'body' => 'Welcome to Fakeeh Care. Your account has been created successfully.',
           ]);
       } catch (\Throwable $e) {
           // ignore mail errors
       }
//        $enable2fa = app(EnableTwoFactorAuthentication::class);
//
//        $enable2fa($result);
        // ✅ Generate TwoFactorAuthenticationProvider
//        $provider = app(TwoFactorAuthenticationProvider::class);
//
//        $secret = $provider->generateSecretKey();
//        $result->forceFill([
//            'two_factor_secret' => encrypt($secret),
//            'two_factor_recovery_codes' => encrypt(json_encode(
//                collect(range(1, 8))->map(fn () => RecoveryCode::generate())->all()
//            )),
//            'two_factor_confirmed_at' => now(),
//        ])->save();
//        Validator::make($input, [
//            'name' => ['required', 'string', 'max:255'],
//            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
//            'password' => $this->passwordRules(),
//            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
//        ])->validate();
//
//        $result = DB::transaction(function () use ($input, $ownerRequest) {
//            return tap(User::create([
//                'name' => $input['name'],
//                'email' => $input['email'],
//                'password' => Hash::make($input['password']),
//            ]), function (User $user) use ($ownerRequest) {
//                $ownerRequest->request->add(['related_id' => $user->id]);
//                $dataNotification = [
//                    'title' => 'newClientNotificationTitle',
//                    'body' => 'newClientNotificationBody',
//                    'type' => 'new client',
//                    'icon' => 'flaticon2-line-chart kt-font-success',
//                ];
//                $this->notificationService->notificationAction(1, $ownerRequest, "admin/users/$user->id", $this->user, $dataNotification);
//
//                $this->createTeam($user);
//            });
//        });
         return $result;
    }

    /**
     * Create a personal team for the user.
     */
    protected function createTeam(User $user): void
    {
        $user->ownedTeams()->save(Team::forceCreate([
            'user_id' => $user->id,
            'name' => explode(' ', $user->name, 2)[0]."'s Team",
            'personal_team' => true,
        ]));
    }
}
