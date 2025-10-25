<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Resources\UserResource;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class AuthController extends Controller
{
    use ApiResponse;

    public function getUser($request)
    {
        return User::where('email', $request->email)->first() ?? null;
    }


    public function login(Request $request)
    {
        try {
            $validator = Validator::make(
                $request->input(),
                [
                    'email' => 'required|email|exists:users,email',
                    'password' => 'required|min:1|max:16',
                ]
            );

            if ($validator->fails()) {
                return $this->error($validator->errors()->first());
            }

            $user = User::where('email', $request->email)->first() ?? null;

            if (!$user) {
                return $this->error('User not found');
            }

            if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                $abilities = $user->abilities ?? [];
                $user->token = $user->createToken(env('APP_NAME'),$abilities)->plainTextToken;
                return $this->success('Logged in successfully', new UserResource($user));
            }

            return $this->error('Invalid credentials');
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->tokens()->delete();
            return $this->success('Logged out successfully');
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}
