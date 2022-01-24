<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function register($data) {
        $userModel = new User();
        
        $user = $userModel->createUser($data);

        return [
            $user
        ];
    }

    public function login($data) {
        $fieldType = filter_var($data['username'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';  

        $userModel = new User();
        
        $user = $userModel->readSingleByKeyValue($fieldType, $data['username']);

        if(!$user || !Hash::check($data['password'], $user->password)) {
            return [
                'data' => [
                    'message' => 'Bad credentials'
                ], 
                'status' => 401
            ];
        }

        $token = $user->createToken($data['password'])->plainTextToken;

        return [
            'data' => [
                'user' => $user,
                'token' => $token
            ],
            'status' => 201
        ];
    }

    public function logout() {
        Auth::user()->tokens->each(function($token, $key) {
            $token->delete();
        });

        return [
            'message' => 'Logged Out!'
        ];
    }
}