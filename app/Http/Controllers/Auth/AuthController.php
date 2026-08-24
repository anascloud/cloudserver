<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Mail\ResetPasswordCodeMail;
use App\Notifications\ResetPasswordNotification;
use App\Repositories\AuthRepository;
use App\Repositories\ResponseRepository;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use DB;

class AuthController extends Controller
{
    public $responseRepository;
    public $authRepository;

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct(ResponseRepository $rr, AuthRepository $ar)
    {
        $this->middleware('auth:api', ['except' => ['login', 'register', 'forgotPassword', 'resetPassword']]);
        $this->responseRepository = $rr;
        $this->authRepository = $ar;
    }

    /**
     * @OA\POST(
     *     path="/api/auth/login",
     *     tags={"Authentication"},
     *     summary="Login",
     *     description="Login",
     *     @OA\RequestBody(
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="email", type="string", example="manirujjamanakash@gmail.com"),
     *              @OA\Property(property="password", type="string", example="123456")
     *          ),
     *      ),
     *      @OA\Response(response=200, description="Login" ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found")
     * )
     * @OA\SecurityScheme(
     *   securityScheme="Bearer",type="apiKey",description="JWT",name="Authorization",in="header",
     * )
     */
    // Example login function
    public function login(LoginRequest $request)
    {
        try {
            $credentials = $request->only('email', 'password');

            if ($token = $this->guard()->attempt($credentials)) {
                $data =  $this->respondWithToken($token);
            }else{
                return $this->responseRepository->ResponseError(null, 'Invalid Email and Password !', Response::HTTP_UNAUTHORIZED);
            }

            return $this->responseRepository->ResponseSuccess($data, 'Logged In Successfully !');
        } catch (\Exception $e) {
            return $this->responseRepository->ResponseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }



    /**
     * @OA\POST(
     *     path="/api/auth/register",
     *     tags={"Authentication"},
     *     summary="Register User",
     *     description="Register New User",
     *     @OA\RequestBody(
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="firstName", type="string", example="Jhon Doe"),
     *              @OA\Property(property="email", type="string", example="jhondoe@example.com"),
     *              @OA\Property(property="password", type="string", example="123456"),
     *              @OA\Property(property="password_confirmation", type="string", example="123456")
     *          ),
     *      ),
     *      @OA\Response(response=200, description="Register New User Data" ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found")
     * )
     */

     public function register(RegisterRequest $request)
{
    try {
        // Extract the correct fields from the request
        $requestData = $request->only('firstName', 'mobileNo', 'email', 'password');

        // Check if the user already exists by email
        $existingUser = User::where('email', $requestData['email'])->first();

        if ($existingUser) {
            return $this->responseRepository->ResponseError(null, 'User already exists', Response::HTTP_CONFLICT);
        }

        // Create new user instance
        $user = new User();
        $user->firstName = $requestData['firstName'];
        $user->mobileNo = $requestData['mobileNo'];
        $user->email = $requestData['email'];
        $user->password = bcrypt($requestData['password']); // Hash the password
        // Save user to the database
        $user->save();

        \Log::info('User registered successfully', ['user_id' => $user->id]);

        // Return success response
        return $this->responseRepository->ResponseSuccess(
            $user,
            'User Registered Successfully. Please login.',
            Response::HTTP_OK
        );
    } catch (\Exception $e) {
        \Log::error('Exception during registration', ['error' => $e->getMessage()]);
        return $this->responseRepository->ResponseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}



    /**
     * @OA\GET(
     *     path="/api/auth/me",
     *     tags={"Authentication"},
     *     summary="Authenticated User Profile",
     *     description="Authenticated User Profile",
     *     @OA\Response(response=200, description="Authenticated User Profile" ),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=404, description="Resource Not Found"),
     * )
     */
    public function me()
    {
        try {
            $data = $this->guard()->user();
            return $this->responseRepository->ResponseSuccess($data, 'Profile Fetched Successfully !');
        } catch (\Exception $e) {
            return $this->responseRepository->ResponseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\POST(
     *     path="/api/auth/logout",
     *     tags={"Authentication"},
     *     summary="Logout",
     *     description="Logout",
     *     @OA\Response(response=200, description="Logout" ),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=404, description="Resource Not Found"),
     * )
     */
    public function logout()
    {
        try {
            $this->guard()->logout();
            return $this->responseRepository->ResponseSuccess(null, 'Logged out successfully !');
        } catch (\Exception $e) {
            return $this->responseRepository->ResponseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\POST(
     *     path="/api/auth/refresh",
     *     tags={"Authentication"},
     *     summary="Refresh",
     *     description="Refresh",
     *     @OA\Response(response=200, description="Refresh" ),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=404, description="Resource Not Found"),
     * )
     */
    public function refresh()
    {
        try {
            $data = $this->respondWithToken($this->guard()->refresh());
            return $this->responseRepository->ResponseSuccess($data, 'Token Refreshed Successfully !');
        } catch (\Exception $e) {
            return $this->responseRepository->ResponseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        // Get the currently authenticated user
        $user = $this->guard()->user();

        // Update the user's token in the database
        $user->token = $token; // Assuming you have a 'token' column in the users table
        $user->save(); // Save the user record with the new token

        $roles = json_decode($user->roles);

        // Prepare the response data
        $data = [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $this->guard()->factory()->getTTL() * 60 * 24 * 30, // 2592000 Seconds = 30 Days
            'user' => [
                'id' => $user->id,
                'firstName' => $user->firstName,
                'mobileNo' => $user->mobileNo,
                'email' => $user->email,
                'email_verified_at' => $user->email_verified_at,
                'reset_code' => $user->reset_code,
                'roles' => $roles, // No need to decode again, already decoded above
                'avatar' => url($user->avatar), // Full URL to the avatar
                'country' => $user->country,
                'address' => $user->address,
                'token' => $token, // Include the token in the user data
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
                'permissions' => $user->permissions, // Include aggregated permissions
            ],
        ];

        return $data; // Return the response data
    }


    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\Guard
     */
    public function guard()
    {
        return Auth::guard();
    }

    /**
     * @OA\POST(
     *     path="/api/auth/forgot-password",
     *     tags={"Authentication"},
     *     summary="Forgot Password",
     *     description="Send password reset link",
     *     @OA\RequestBody(
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="email", type="string", example="user@example.com"),
     *          ),
     *      ),
     *      @OA\Response(response=200, description="Password reset link sent" ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found")
     * )
     */
    public function forgotPassword(Request $request)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first(), 'type' => 'invalid']);
        }

        // Generate a 12-digit random code
        $code = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);

        // Find the user
        $user = User::where('email', $request->email)->first();

        // Save the code to the database (consider using a more secure approach in production)
        DB::table('users')->where('email', $request->email)->update(['reset_code' => $code]);

        // Send the email
        // Mail::to($user->email)->send(new ResetPasswordCodeMail($code));

        return response()->json(['message' => 'Reset code sent successfully.', 'type' => 'success', 'data' => $request->email]);
    }

    /**
     * @OA\POST(
     *     path="/api/auth/reset-password",
     *     tags={"Authentication"},
     *     summary="Reset Password",
     *     description="Reset user password",
     *     @OA\RequestBody(
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="token", type="string"),
     *              @OA\Property(property="email", type="string", example="user@example.com"),
     *              @OA\Property(property="password", type="string", example="newpassword"),
     *              @OA\Property(property="password_confirmation", type="string", example="newpassword")
     *          ),
     *      ),
     *      @OA\Response(response=200, description="Password reset successful" ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found")
     * )
     */
    public function resetPassword(Request $request)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'code' => 'required|string|size:6',
            'newPassword' => 'required|string|min:8',
            'confirmPassword' => 'required|string|min:6|same:newPassword',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first(), 'type' => 'error']);
        }

        // Find the user
        $user = User::where('email', $request->email)->first();
        // return response()->json($user);
        // Check the reset code
        if ($user->reset_code !== $request->code) {
            return response()->json(['message' => 'Invalid reset code.', 'type' => 'error']);
        }

        // Update the password using query builder
        DB::table('users') // Assuming your users table is named 'users'
            ->where('email', $request->email)
            ->update([
                'password' => bcrypt($request->newPassword), // Update the password with the hashed value
                'reset_code' => null, // Clear the reset code after successful reset
            ]);

        return response()->json(['message' => 'Password reset successfully.', 'type' => 'success']);
    }

    public function profileResetPassword(Request $request)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'previousPassword' => 'required|string|min:6',
            'newPassword' => 'required|string|min:6',
            'confirmPassword' => 'required|string|min:6|same:newPassword',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 400);
        }

        // Find the user by email
        $user = User::where('email', Auth()->user()->email)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        // Verify if the previous password matches the hashed password in the database
        if (!\Hash::check($request->previousPassword, $user->password)) {
            return response()->json(['message' => 'The previous password is incorrect.'], 400);
        }

        // Update the password using query builder
        DB::table('users') // Assuming your users table is named 'users'
            ->where('email', Auth()->user()->email)
            ->update([
                'password' => bcrypt($request->newPassword), // Hash and update the new password
            ]);

        return response()->json(['message' => 'Password reset successfully.', 'type' => 'success']);
    }

}
