<?php

use App\Http\Controllers\GroupParticipantController;
use App\Http\Controllers\GroupsController;
use App\Http\Controllers\JamKerjaController;
use App\Http\Controllers\ParticipantsController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::apiResource('users', UserController::class);

Route::apiResource('participants', ParticipantsController::class);
Route::get('participants/id_kartu/{id_kartu}', [ParticipantsController::class, 'showByIdKartu']);

Route::apiResource('groups', GroupsController::class);

Route::apiResource('group-participants', GroupParticipantController::class);

Route::apiResource('jam-kerja', JamKerjaController::class);

Route::apiResource('shifts', \App\Http\Controllers\ShiftController::class);
