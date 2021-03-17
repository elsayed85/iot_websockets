<?php

use App\Http\Controllers\Python\CompilerController;
use App\Http\Controllers\Python\DataController;
use App\python\LaravelPython;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;

Route::get('needleman-wunsch', [CompilerController::class , "NeedlemanWunsch"])->name('needleman_wunsch');
Route::get('nussinov', [CompilerController::class , "Nussinov"])->name('nussinov');
Route::post('upload', [DataController::class , "uploadFile"])->name('data.uploadFile');
