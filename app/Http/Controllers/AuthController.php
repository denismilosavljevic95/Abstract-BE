<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response([
                'message' => 'Not correct inputs'
            ], 400);
        }

        $fields = $request->all();

        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => bcrypt($fields['password'])
        ]);

        $token = $user->createToken($fields['password'])->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response($response, 201);
    }

    public function login(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string',
            'password' => 'required|string'
          ]);
          
        if ($validator->fails()) {
            return response([
                'message' => 'Not correct inputs'
            ], 400);
        }
        $fields = $request->all();

        // Check email
        $user = User::where('email', $fields['email'])->first();

        // Check password
        if(!$user || !Hash::check($fields['password'], $user->password)) {
            return response([
                'message' => 'Bad credentials'
            ], 401);
        }

        $token = $user->createToken($fields['password'])->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response($response, 201);
    }

    public function logout(Request $request) {
        Auth::user()->tokens->each(function($token, $key) {
            $token->delete();
        });

        return [
            'message' => 'Logged out'
        ];
    }
}