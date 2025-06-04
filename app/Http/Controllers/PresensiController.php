<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Presensi;
use App\Models\Shift;
use App\Models\Participant;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PresensiGroupExport;
use App\Models\Device;
use App\Models\WaktuLibur;
use Illuminate\Support\Facades\Crypt;

class PresensiController extends Controller
{
    // Show all presensi records
    public function index(Request $request)
    {
        // $presensis = Presensi::with('participant')->orderBy('created_at', 'desc')->get();
        $groups = Group::all();
        if ($request->group){
            $groupId = $request->input('group');
            $presensis = Presensi::with('participant')
                ->whereHas('participant.groupParticipants', function ($query) use ($groupId) {
                    $query->where('id_group', $groupId);
                })
                ->paginate(10);
        } else if($request->date_filter){
            $date = Carbon::parse($request->date_filter);
            $presensis = Presensi::with('participant')
                ->whereDate('waktu_masuk', $date)
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        } else if ($request->group && $request->date_filter) {
            $groupId = $request->input('group');
            $date = Carbon::parse($request->date_filter);
            $presensis = Presensi::with('participant')
                ->whereHas('participant.groupParticipants', function ($query) use ($groupId) {
                    $query->where('id_group', $groupId);
                })
                ->whereDate('waktu_masuk', $date)
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        } else {
            $presensis = Presensi::with('participant')->orderBy('created_at', 'desc')->paginate(10);
        }
        return view('managment.presensi.presensis', compact('presensis', 'groups'));
    }

    // Show a single presensi record
    public function show($id)
    {
        $presensi = Presensi::with('participant')->findOrFail($id);
        return view('Managment.Presensi.show', compact('presensi'));
    }

    public function store(Request $request)
    {

        $device = Device::where('device_id', $request->id_device)->first();
        if(!$device || $device->api_key != $request->api_key) {
            return response()->json(['message' => 'Device not found']);
        }

        if($device->status === 'inactive') {
            return response()->json(['message' => 'Device is inactive, please call admin to fix it']);
        }

        $participant = Participant::where('id_kartu', $request->id_kartu)->with('jadwalParticipant', 'groupParticipants')->first();

        if (!$participant) {
            return response()->json(['message' => 'Participant not found'], 404);
        }

                // Get the current time and day
        Carbon::setLocale('id');
        $currentTime = Carbon::now();
        $currentDate = $currentTime->translatedFormat(format: 'Y-m-d');
        $currentDay = strtolower($currentTime->translatedFormat('l')); // This will return the day in Bahasa
        $currentHour = $currentTime->translatedFormat(format: 'H:i:s');


        $groupParticipants = $participant->groupParticipants;
        foreach ($groupParticipants as $data) {
            $group = Group::with('groupLiburs')->find($data->id_group);

            foreach ($group->groupLiburs as $dataLibur) {
                $waktuLibur = WaktuLibur::find($dataLibur->id_waktu_libur);

                if ($currentDate >= $waktuLibur->tanggal_mulai && $currentDate <= $waktuLibur->tanggal_akhir) {
                    return response()->json([
                        'message' => 'Hari ini adalah hari libur: ' . $waktuLibur->nama_libur . ', tidak bisa presensi'
                    ], 403);
                }
            }
        }

        

        $jadwalParticipant = $participant['jadwalParticipant'];
        if (!$jadwalParticipant) {
            return response()->json(['message' => 'Jadwal Participant not found'], 404);
        }

        $shift = $jadwalParticipant->shift;
        if (!$shift) {
            return response()->json(['message' => 'Shift not found'], 404);
        }

        $jamKerja = $shift->jamKerja;
        if (!$jamKerja) {
            return response()->json(['message' => 'Jam Kerja not found'], 404);
        }



        // Check if the participant has already checked in today
        $existingAttendance = $participant->presensi()
            ->whereDate('created_at', Carbon::today())
            ->first();
        // return response()->json($currentHour);
        if ($existingAttendance != null) {
            if($currentHour < $jamKerja->jam_mulai_scan_keluar) {
                return response()->json(['message' => 'Belum masuk jam scan keluar'], 422);
            }

            $participant->presensi()->update([
                'waktu_keluar' => $currentTime,
                'status_check_out' => true,
                'updated_at' => $currentTime, // Set to current time for check-out
            ]);
            return response()->json([
                'message' => 'Presensi Check Out Hari Ini',
                'waktu_keluar' => $currentHour,
                'participant' => $participant->nama,
                'shift' => $jadwalParticipant->shift->nama,
                'updated_at' => $currentTime
            ], 200);
        }else{
            // Check if the current day is in the jadwalParticipant
            $isDayInJadwal = $jadwalParticipant->shift->detailShifts->contains(function ($detailShift) use ($currentDay) {
                return strtolower($detailShift->hari) == $currentDay;
            });
            if (!$isDayInJadwal) {
                return response()->json(['message' => 'Tidak ada jadwal untuk hari ini'], 422);
            }

            if($currentHour < $jamKerja->jam_mulai_scan_masuk) {
                return response()->json(['message' => 'Tidak dalam jam scan'], 422);
            }

            $toleransiMasuk = Carbon::parse($jamKerja->jam_masuk)->addMinutes($jamKerja->toleransi_terlambat)->translatedFormat('H:i:s');

            if ($currentHour > $toleransiMasuk){
                $participant->presensi()->create([
                    'id_participant' => $participant->id,
                    'waktu_masuk' => $currentTime,
                    'waktu_keluar' => $currentTime->addHours($jamKerja->toleransi_check_out),
                    'id_device' => $device->id, 
                    'id_shift' => $jadwalParticipant->shift->id,
                    'status_terlambat' => true,
                    'status_check_out' => false
                ]);
            } else {
                $participant->presensi()->create([
                    'id_participant' => $participant->id,
                    'waktu_masuk' => $currentTime,
                    'waktu_keluar' => $currentTime->addHours($jamKerja->toleransi_check_out),
                    'id_device' => $device->id, 
                    'id_shift' => $jadwalParticipant->shift->id,
                    'status_terlambat' => false,
                    'status_check_out' => false
                ]);
            }
            
            return response()->json([
                'message' => 'Presensi Presensi Hari Ini',
                'waktu_keluar' => $currentHour,
                'participant' => $participant->nama,
                'shift' => $jadwalParticipant->shift->nama,
                'updated_at' => $currentTime
            ], 200);
            
        }
    }

    // Show the edit form
    public function edit($id)
    {
        $presensi = Presensi::findOrFail($id);
        $participants = Participant::all();
        return view('Managment.Presensi.edit', compact('presensi', 'participants'));
    }

    // Update the presensi record
    public function update(Request $request, $id)
    {
        $presensi = Presensi::findOrFail($id);

        $request->validate([
            'participant_id' => 'required|exists:participants,id',
            'tanggal' => 'required|date',
            'jam_masuk' => 'required',
            'jam_pulang' => 'required',
            'status' => 'required'
        ]);

        $presensi->update($request->all());

        return redirect()->route('presensi.index')->with('success', 'Presensi updated successfully.');
    }

    // Delete the presensi record
    public function destroy($id)
    {
        $presensi = Presensi::findOrFail($id);
        $presensi->delete();

        return redirect()->route('presensi.index')->with('success', 'Presensi deleted successfully.');
    }

    public function test(Request $request) {
        $group = Group::find($request->id);

        if(!$group){
            return response()->json('Group not found', 404);
        }

        $totalDay = Presensi::with('shift');
        $presensi = Presensi::with('participant', 'shift')->whereHas('participant.groupParticipants.group', function($query) use ($group) {
            $query->where('id_group', $group->id);
        })->get();
        $shift = $presensi->pluck('shift.tanggal_mulai');
        $today = Carbon::today();
        $totalHari = $shift->map(function ($date) use($today) {
            $tanggalMulai = Carbon::parse($date);
            $totalDay = $tanggalMulai->diffInDays($today) + 1; // +1 jika ingin termasuk hari mulai
            return $totalDay;
        });



        $dataPresensi = $presensi->map(function($data, $index) use($totalHari, $presensi) {
            $totalMasuk = $presensi->where('id_participant', $data->participant->id)->count();
            $totalTelat = $presensi->where('id_participant', $data->participant->id)->where('status_terlambat', true)->count();
            $totalTidakCO = $presensi->where('id_participant', $data->participant->id)->where('status_check_out')->where('status_check_out', false)->count();

            return [
                "participant" => $data->participant->nama,
                "TotalHari" => $totalHari[$index],
                "TotalMasuk" => $totalMasuk,
                "totalTelat" => $totalTelat,
                "totalTidakMasuk" => $totalHari[$index] - $totalMasuk,
                "totalTidakCheckOut" => $totalTidakCO
            ];
        });
        // $startDate = $shift->tanggal_mulai;

        return response()->json($dataPresensi);
    }

    public function presensiExport($id)
    {
        $groupId = Crypt::decrypt($id);
        $group = Group::find($groupId);

        if(!$group) {
            return response()->json(['message' => 'Group not found'], 404);
        }

        return Excel::download(new PresensiGroupExport($group->id, $group->nama), 'report_presensi_group_' . $group->nama . '.xlsx');
    }
}
