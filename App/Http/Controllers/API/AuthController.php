<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AppUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Validator;
use Log;

class AuthController extends Controller
{


    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $credentials = $request->only('email', 'password');


        if (Auth::guard('web')->attempt($credentials)) {
            $user = Auth::guard('web')->user(); 

            $scopes = json_decode($user->role->scopes);
            Log::info(json_encode($scopes));
        

            $token = $user->createToken('LaravelAuthApp', $scopes)->accessToken;

            return response()->json(['token' => $token], 200);
        } else {
            return response()->json(['error' => 'Unauthorized. Invalid email or password.'], 401);
        }
    }

    public function logout(Request $request)
    {
        $user = Auth::guard('api')->user(); 
        if ($user) {
            $user->token()->revoke();
            return response()->json(['message' => 'Successfully logged out'], 200);
        } else {
            return response()->json(['error' => 'User not authenticated'], 401);
        }
    }
}

