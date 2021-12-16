<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::post('/login', [AuthController::class, 'userLogin']);
Route::post('/register', [AuthController::class, 'registerUser']);
Route::post('/confirm-account', [AuthController::class, 'confirmAccount']);
Route::post('/confirm-mobile-code', [AuthController::class, 'confirmCode']);
Route::post('/resend-confirmation-code', [AuthController::class, 'resendCode']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);


Route::group(['middleware' => 'auth:api'],  function () {
    Route::post('/change-password', [AuthController::class, 'changePassword']);
    Route::post('/logout', [AuthController::class, 'logout']);
});