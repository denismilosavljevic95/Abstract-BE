<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AuthService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $AuthService)
    {
        $this->authService = $AuthService;
    }

    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|unique:users,username',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string|min:8'
        ]);

        if ($validator->fails()) {
            return response([
                'message' => 'Not correct inputs'
            ], 400);
        }

        $fields = $request->all();

        $response = $this->authService->register($fields);

        return response($response, 201);
    }

    public function login(Request $request) {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response([
                'message' => 'Not correct inputs'
            ], 400);
        }
        $fields = $request->all();

        $response = $this->authService->login($fields);

        return response($response['data'], $response['status']);
    }

    public function logout(Request $request) {
        return $this->authService->logout();
    }
}