<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    /**
     * Register a new user
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|confirmed|min:6',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        // Mengirim email verifikasi
        event(new Registered($user));

        return response()->json([
            'message' => 'User successfully registered. Please check your email for verification link.'
        ], 201);
    }

    /**
     * Login user and return a token
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:100',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if (! $token = Auth::attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = Auth::user();

        if (!$user->hasVerifiedEmail()) {
            return response()->json(['error' => 'Email belum diverifikasi.'], 403);
        }

        return $this->createNewToken($token);
    }

    /**
     * Log the user out (Invalidate the token)
     */
    public function logout()
    {
        Auth::logout();

        return response()->json(['message' => 'User successfully signed out']);
    }

    /**
     * Get the authenticated User
     */
    public function userProfile()
    {
        return response()->json(Auth::user());
    }

    /**
     * Verify email
     */
    public function verifyEmail(Request $request, $id, $hash)
    {
        $user = User::findOrFail($id);

        if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return response()->json(['message' => 'Invalid verification link.'], 400);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email sudah diverifikasi.'], 200);
        }

        $user->markEmailAsVerified();

        return response()->json(['message' => 'Email berhasil diverifikasi.'], 200);
    }

    /**
     * Resend verification email
     */
    public function resendVerificationEmail(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email sudah diverifikasi.'], 400);
        }

        $request->user()->sendEmailVerificationNotification();

        return response()->json(['message' => 'Verifikasi email telah dikirim ulang.'], 200);
    }

    /**
     * Get the token array structure
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token){
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()
        ]);
    }
}
