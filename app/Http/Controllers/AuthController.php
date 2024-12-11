<?php

namespace App\Http\Controllers;

use App\Models\ResetCodePassword;
use App\Models\Role;
use App\Models\User;
use App\Notifications\PasswordResetNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function getRoles()
    {
        $roles = Role::all();
        return response()->json(['success' => true, 'data' => $roles], 200);
    }

    /**
     * Register a new user.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {   
        // Validate input
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed. Please check the errors.',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Create user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'email_verified_at' => now(),
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User registered successfully',
            'data' =>  $user,
        ], 201);
    }

    /**
     * Login a user.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed. Please check the errors.',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Check if the email exists
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Email does not exist in our records',
            ], 404);
        }

        // Check if the password is correct
        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Incorrect password',
            ], 401);
        }

         // Attempt to create a JWT token
        $credentials = $request->only('email', 'password');
        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Incorrect password',
            ], 401);
        }

        return response()->json([
            'success' => true,
            'message' => 'User logged in successfully',
            'data' => $user,
            'token' => $token,
        ], 200);
    }

    public function forget_password(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            'email' => 'required|email|exists:users',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed. Please check the errors.',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        // Delete all old code that user send before.
        ResetCodePassword::where('email', $request->email)->delete();

        // Generate random code
        $code = mt_rand(100000, 999999);

        // Create a new code
        $codeData = ResetCodePassword::create([
            'email' => $request->email,
            'code' => $code
        ]);

        // Send the notification
        $user->notify(new PasswordResetNotification($codeData));


        return response()->json([
            'success' => true,
            'message' => "We have emailed your password reset code.",
        ], 200);
    }

    public function reset_password(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            'code' => 'required|string|exists:reset_code_passwords',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed. Please check the errors.',
                'errors' => $validator->errors()
            ], 422);
        }

        if (ResetCodePassword::where('code', '=', $request->code)->exists()) {
            // find the code
            $passwordReset = ResetCodePassword::firstWhere('code', $request->code);

            // check if it does not expired: the time is one hour
            if ($passwordReset->created_at > now()->addHour()) {
                $passwordReset->delete();

                return response()->json([
                    'success' => false,
                    'message' => 'Password reset code expired'
                ], 401);
            }

            // find user's email
            $user = User::where('email', $passwordReset->email)->first();

            // update user password
            $user->update([
                'password' => Hash::make($request->password),
                'current_password' => $request->password
            ]);

            // delete current code
            $passwordReset->delete();

            return response()->json([
                'success' => true,
                'message' => 'Password has been successfully reset, Please login',
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => "Code doesn't exist in our database."
            ], 401);
        }
    }

    public function logout(Request $request)
    {
        try {
            // Invalidate the token
            auth()->logout();

            return response()->json([
                'success' => true,
                'message' => 'User successfully logged out',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to logout, please try again',
            ], 500);
        }
    }
}
