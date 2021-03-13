<?php

use App\Events\Chat;
use App\Events\Device\SendPayloadEvent;
use App\Http\Controllers\IOT\V1\Auth\LoginController;
use App\Http\Controllers\Iot\V1\AuthController;
use App\Http\Controllers\Iot\V1\DataController;
use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, "login"])->name('login');

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('send', [DataController::class, "send"])->name('data.send');
    Route::post('logout', [AuthController::class, "logout"])->name('logout');
});
