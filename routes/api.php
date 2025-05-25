<?php

use App\Http\Controllers\DetailShiftController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\GroupLiburController;
use App\Http\Controllers\GroupParticipantController;
use App\Http\Controllers\GroupsController;
use App\Http\Controllers\JadwalParticipantController;
use App\Http\Controllers\JamKerjaController;
use App\Http\Controllers\ParticipantsController;
use App\Http\Controllers\PresensiController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WaktuLiburController;
use App\Models\WaktuLibur;
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

Route::apiResource('shifts', ShiftController::class);

Route::apiResource('detail-shifts', DetailShiftController::class)->except(['create']);
Route::post('detail-shifts/{shift_id}', [DetailShiftController::class, 'store']);

Route::apiResource('jadwal-participants', JadwalParticipantController::class)->except(['create']);

Route::apiResource('devices', DeviceController::class);

Route::apiResource('libur', WaktuLiburController::class);

Route::apiResource('group-libur', GroupLiburController::class);

Route::get('presensi/{id_participant}', [PresensiController::class, 'store']);
