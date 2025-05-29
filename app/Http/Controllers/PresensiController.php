<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Presensi;
use App\Models\Participant;
use Carbon\Carbon;
use Illuminate\Http\Request;

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

    public function store($id_kartu)
    {
        $participant = Participant::where('id_kartu', $id_kartu)->with('jadwalParticipant')->first();

        if (!$participant) {
            return response()->json(['message' => 'Participant not found'], 404);
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

        // Get the current time and day
        Carbon::setLocale('id');
        $currentTime = Carbon::now();
        $currentDay = strtolower($currentTime->translatedFormat('l')); // This will return the day in Bahasa
        $currentHour = $currentTime->translatedFormat(format: 'H:i:s');


        // Check if the participant has already checked in today
        $existingAttendance = $participant->presensi()
            ->whereDate('created_at', Carbon::today())
            ->first();
        if ($existingAttendance != null) {
            if($currentTime > $jamKerja->jam_mulai_scan_keluar) {
                return response()->json(['message' => 'Belum masuk jam scan keluar'], 422);
            }

            $participant->presensi()->update([
                'waktu_keluar' => $currentTime,
                'updated_at' => $currentTime, // Set to current time for check-out
            ]);
            return response()->json([
                'message' => 'Presensi berhasil',
                'waktu_keluar' => $currentTime->format('H:i:s'),
                'participant' => $participant->nama,
                'shift' => $jadwalParticipant->shift->nama_shift,
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

            if($currentTime < $jamKerja->jam_mulai_scan_masuk) {
                return response()->json(['message' => 'Tidak dalam jam scan'], 422);
            }

            // Check if the current time is within the working hours
            $isWithinWorkingHours = $jamKerja->get()->contains(function ($jam) use ($currentHour) {
                return $currentHour >= $jam->jam_masuk && $currentHour <= $jam->jam_pulang;
            });

            if (!$isWithinWorkingHours) {
                return response()->json(['message' => 'Tidak dalam jam kerja'], 422);
            }

            // Create the attendance record
            $participant->presensi()->create([
                'id_participant' => $participant->id,
                'waktu_masuk' => $currentTime,
                'waktu_keluar' => null, // Set to null for check-in
                'id_device' => 1, // Assuming a default device ID, replace with actual logic if needed
                'id_shift' => $jadwalParticipant->shift->id,
                'updated_at' => null, // Set to null for check-in
            ]);
        }



        // // Return the current hour in JSON format
        // return response()->json([
        //     'message' => 'Presensi berhasil',
        //     'waktu_masuk' => $currentTime->format('H:i:s'),
        //     'participant' => $participant->nama,
        //     'shift' => $jadwalParticipant->shift->nama_shift,
        //     'updated_at' => null
        // ]);
        return response()->json($existingAttendance, 200);



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
}
