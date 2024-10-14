<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = request(['email', 'password']);
  
        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
  
        return $this->respondWithToken($token);
    }

    public function register(Request $request)
    {
        $validate = Validator::make( $request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|min:6',
        ]);

        if ($validate->fails()) {
            return response()->json([$validate->errors(),], 422);
        }

        $user = User::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json(compact('user','token'), 201);
    }

    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'User successfully signed out']); 
    }

    // Get authenticated user
    public function getUser()
    {
        try {
            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['error' => 'User not found'], 404);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'Invalid token'], 400);
        }

        return response()->json(compact('user'));
    }

    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    public function userProfile()
    {
        return response()->json(auth()->user());
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}
