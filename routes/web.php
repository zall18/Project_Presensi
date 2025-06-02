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
use App\Models\Group;

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

Route::get('/', [UserController::class, 'dashboard'])->name('dashboard');

Route::resource('user', UserController::class);
Route::get('/participant/import', [ParticipantsController::class, 'importView'])->name('participant.import');
Route::post('/participant/import', [ParticipantsController::class, 'import'])->name('participant.import.data');
Route::resource('participant', ParticipantsController::class);

Route::resource('group', GroupsController::class);
Route::delete('/groupParticipant/destroy', [GroupParticipantController::class, 'destroy'])->name('groupParticipant.destroy');
Route::resource('groupParticipant', GroupParticipantController::class)->except('destroy');

Route::get('group/{id}/addParticipant', [GroupsController::class, 'addParticipant'])->name('group.addParticipant');
Route::get('group/{id}/removeParticipant', [GroupsController::class, 'removeParticipant'])->name('group.removeParticipant');

Route::resource('jamKerja', jamKerjaController::class);
Route::resource('shift', ShiftController::class);

Route::get('shift/{shift_id}/createDetailShift', [ShiftController::class, 'createDetailShift'])->name('shift.createDetailShift');

Route::resource('detailShift', DetailShiftController::class);
Route::resource('jadwalParticipant', JadwalParticipantController::class)->except(['edit', 'destroy', 'update', 'show']);
Route::get('jadwalParticipant/create/{id_shift}', [JadwalParticipantController::class, 'create'])->name('jadwalParticipant.create');
Route::get('jadwalParticipant/remove/{id_shift}', [JadwalParticipantController::class, 'remove'])->name('jadwalParticipant.remove');
Route::get('jadwalParticipant/{id_shift}/show', [JadwalParticipantController::class, 'show'])->name('jadwalParticipant.show');
route::delete('jadwalParticipant/{id_shift}/destroy', [JadwalParticipantController::class, 'destroy'])->name('jadwalParticipant.destroyItem');
Route::resource('presensi', PresensiController::class);
Route::resource('device', DeviceController::class);
Route::resource('waktuLibur', WaktuLiburController::class);
Route::delete('groupLibur/{id_group}/{id_waktu_libur}/destroy', [GroupLiburController::class, 'destroy'])->name('groupLibur.destroyItem');
// Route::resource('groupLibur', GroupLiburController::class);

Route::get('/export-participant', [ParticipantsController::class, 'exportAll'])->name('export.participant');
Route::get('/export-participant/{id}', [ParticipantsController::class, 'exportByGroup'])->name('export.participant.group');

Route::get('/test', function() {
    $test = Group::with('participants')->find(1)->participants->select('no_induk', 'nama', 'id_kartu', 'no_hp', 'alamat')->count();
    return response()->json($test);
});
