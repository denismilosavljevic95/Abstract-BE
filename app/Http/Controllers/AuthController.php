<?php

namespace App\Http\Controllers;

use App\Helpers\Helpers;
use Illuminate\Http\Request;
use App\Services\AuthService;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    protected $authService;
    protected $helpers;

    public function __construct(AuthService $AuthService, Helpers $Helpers)
    {
        $this->authService = $AuthService;
        $this->helpers = $Helpers;
    }

    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|unique:users,username',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string|min:8'
        ]);

        if ($validator->fails()) {
            return $this->helpers->response(['message' => 'Not correct inputs'], 400);
        }

        $fields = $request->all();

        $response = $this->authService->register($fields);

        return $this->helpers->response($response, 201);
    }

    public function login(Request $request) {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string'
        ]);

        if ($validator->fails()) {
            return $this->helpers->response(['message' => 'Not correct inputs'], 400);
        }
        $fields = $request->all();

        $response = $this->authService->login($fields);

        return $this->helpers->response($response['data'], $response['status']);
    }

    public function logout(Request $request) {
        $response = $this->authService->logout();
        return $this->helpers->response($response, 200);
    }
}