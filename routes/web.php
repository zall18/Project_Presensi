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
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('dashboard');
});

Route::resource('user', UserController::class);
Route::resource('participant', ParticipantsController::class);
Route::resource('group', GroupsController::class);
Route::resource('jamKerja', jamKerjaController::class);
Route::resource('shift', ShiftController::class);
Route::resource('detailShift', DetailShiftController::class);
Route::resource('jadwalParticipant', JadwalParticipantController::class);
Route::resource('presensi', PresensiController::class);
Route::resource('device', DeviceController::class);
Route::resource('waktuLibur', WaktuLiburController::class);