<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LaundriesApiController;

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

Route::group(['middleware' => 'api'], function ($router) {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::group(['middleware' => 'auth:api'], function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user-profile', [AuthController::class, 'userProfile']);
    });

    // Email Verification Routes
    Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])->name('verification.verify');
    Route::post('/email/resend', [AuthController::class, 'resendVerificationEmail'])->middleware(['auth:api']);

    

});

//binatu api rooutes
Route::get('/laundries',[LaundriesApiController::class,'index']);
Route::post('/laundries', [LaundriesApiController::class,'store']);
Route::get('/laundries/{id}', [LaundriesApiController::class, 'show']);
Route::put('/laundries/{id}',[LaundriesApiController::class, 'update']);
Route::delete('/laundries/{id}',[LaundriesApiController::class, 'destroy']);