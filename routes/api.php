<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DocumentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/documents', [DocumentController::class, 'readAll']);

    Route::get('/document', [DocumentController::class, 'readOne']);
    Route::post('/document', [DocumentController::class, 'create']);
    Route::patch('/document', [DocumentController::class, 'update']);
    Route::delete('/document', [DocumentController::class, 'delete']);

    Route::get('/document/download', [DocumentController::class, 'download']);
});