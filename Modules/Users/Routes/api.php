<?php

use Illuminate\Support\Facades\Route;
use Modules\Users\Http\Controllers\Api\AuthController;

Route::group([

    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {

    Route::post('login', [AuthController::class,'login']);
    Route::post('register', [AuthController::class,'register']);
    Route::post('logout', [AuthController::class,'logout']);
    Route::post('me', [AuthController::class,'me']);
    Route::post('refresh', [AuthController::class,'refresh']);
    Route::post('send/code', [AuthController::class,'ResendActiveCode']);
    Route::post('active/code', [AuthController::class,'activeCode']);


});
