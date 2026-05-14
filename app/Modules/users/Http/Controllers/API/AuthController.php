<?php

namespace Users\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Rules\ValidMail;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Users\Http\Resources\UserResource;
use Users\Models\User;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    //    public function __construct()
    //    {
    //        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    //    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $credentials = $this->credentials($request);
        $password = $credentials['password'];
        unset($credentials['password']);

        $field = array_key_first($credentials);
        $user = User::where($field, $credentials[$field])->first();
        $dummyHash = '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';
        $passwordValid = Hash::check((string) $password, $user?->password ?? $dummyHash);

        if ($user && $passwordValid) {
            return $this->respondWithToken($user->createToken('mobile-api'), $user);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @return array
     */
    protected function credentials(Request $request)
    {
        if (is_numeric($request->get('email'))) {
            return ['mobile' => $request->get('email'), 'password' => $request->get('password')];
        } elseif (filter_var($request->get('email'), FILTER_VALIDATE_EMAIL)) {
            return ['email' => $request->get('email'), 'password' => $request->get('password')];
        }

        return ['user_name' => $request->get('email'), 'password' => $request->get('password')];
    }

    /**
     * Get the authenticated User
     *
     * @return JsonResponse
     */
    public function me()
    {
        return UserResource::make(auth()->user());
    }

    /**
     * Log the user out by revoking the current Passport access token.
     *
     * @return JsonResponse
     */
    public function logout(Request $request)
    {
        $token = $request->user()?->token();
        if (! $token) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $token->revoke();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Get the token array structure.
     *
     * @param  mixed  $tokenResult
     * @return JsonResponse
     */
    protected function respondWithToken($tokenResult, ?User $user = null)
    {
        $expiresAt = $tokenResult->token?->expires_at;
        $expiresAtIso = $expiresAt instanceof \DateTimeInterface
            ? $expiresAt->format(\DateTimeInterface::ATOM)
            : null;

        return response()->json([
            'access_token' => $tokenResult->accessToken,
            'token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => $expiresAtIso,
            'expires_in' => $expiresAt ? max(0, now()->diffInSeconds($expiresAt, false)) : null,
            'user' => UserResource::make($user ?? $this->guard()->user()),
        ]);
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return Guard
     */
    public function guard()
    {
        return Auth::guard('api');
    }

    public function getMessageErrors()
    {
        return [
            'required' => ':attribute is required.',
            'numeric' => ':attribute must be a number.',
            'phone_number.regex' => ':attribute is invalid.',
            'in' => ':attribute is invalid.',
            'phone_number.min' => ':attribute must be at least :min characters.',
            'phone_number.max' => ':attribute may not be greater than :max characters.',
            'birthdate.date_format' => ':attribute is invalid.',
            'birthdate.before' => ':attribute must be a date before today.',
            'avatar.mimes' => ':attribute has an invalid content type.',
            'unique' => ':attribute has already been taken.',
        ];
    }

    public function validateRequest($request, $messages)
    {
        return Validator::make($request->all(), [
            'name' => 'required|string|max:75',
            //            'last_name' => 'required|string|max:75',
            //            'phone_number' => 'required|unique:users,phone_number|min:10|max:15|regex:/(01)[0-9]{9}/',
            //            'gender' => [Rule::in(['male', 'female'])],
            //            'birthdate' => 'required|string|max:75|date_format:YYYY-MM-DD|before:today',
            //            'avatar' => 'required|file|mimes:jpeg,png,jpg,gif',
            'email' => ['required', 'unique:users,email', new ValidMail],
            'password' => 'required|string|confirmed|min:6',
        ], $messages);
    }

    /**
     * Register a User.
     *
     * @return JsonResponse
     */
    public function register(Request $request)
    {
        // specify your custom message here
        $messages = $this->getMessageErrors();

        $validator = $this->validateRequest($request, $messages);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create(array_merge(
            $validator->validated(),
            ['password' => bcrypt($request->password),
                'code' => Str::random(40),
            ]
        ));

        return response()->json([
            'message' => 'User successfully registered',
            'user' => UserResource::make($user),
        ], 201);
    }
}
