<?php

namespace App\Http\Controllers;

use App\Models\Participant;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PresensiController extends Controller
{
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

        // Check if the current day is in the jadwalParticipant
        $isDayInJadwal = $jadwalParticipant->shift->detailShifts->contains(function ($detailShift) use ($currentDay) {
            return strtolower($detailShift->hari) === $currentDay;
        });
        if (!$isDayInJadwal) {
            return response()->json(['message' => 'Tidak ada jadwal untuk hari ini'], 422);
        }

        // Check if the current time is within the working hours
        $isWithinWorkingHours = $jamKerja->where('hari', $currentDay)->contains(function ($jam) use ($currentHour) {
            return $currentHour >= $jam->jam_masuk && $currentHour <= $jam->jam_pulang;
        });
        if (!$isWithinWorkingHours) {
            return response()->json(['message' => 'Tidak dalam jam kerja'], 422);
        }
        // Check if the participant has already checked in today
        $hasCheckedInToday = $participant->presensi()->whereDate('created_at', Carbon::today())->exists();
        if ($hasCheckedInToday) {
            return response()->json(['message' => 'Anda sudah melakukan presensi hari ini'], 422);
        }
        // Create the attendance record
        $participant->presensi()->create([
            'id_kartu' => $id_kartu,
            'jam_masuk' => $currentHour,
            'hari' => $currentDay,
        ]);
        // Return the current hour in JSON format   

        return response()->json($currentHour);


    }
}
