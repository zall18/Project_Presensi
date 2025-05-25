<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JadwalParticipant;
use Illuminate\Support\Facades\Validator;

class JadwalParticipantController extends Controller
{
    // List all jadwal participants
    public function index()
    {
        $jadwalParticipants = JadwalParticipant::with(['shift', 'participant'])->get();
        return response()->json($jadwalParticipants);
    }

    // Store a new jadwal participant
    public function store(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'id_shift' => 'required|exists:shifts,id',
            'id_participant' => 'required|exists:participants,id',
        ]);
        if ($validated->fails()) {
            return response()->json($validated->errors(), 422);
        }
        $validated = $validated->validated();
        // Check if the jadwal participant already exists
        $existingJadwalParticipant = JadwalParticipant::where('id_shift', $validated['id_shift'])
            ->where('id_participant', $validated['id_participant'])
            ->first();
        if ($existingJadwalParticipant) {
            return response()->json(['message' => 'JadwalParticipant already exists'], 422);
        }

        $jadwalParticipant = JadwalParticipant::create($validated);

        return response()->json($jadwalParticipant->load(['shift', 'participant']), 201);
    }

    // Show a single jadwal participant
    public function show($id)
    {
        $jadwalParticipant = JadwalParticipant::with(['shift', 'participant'])->find($id);
        if (!$jadwalParticipant) {
            return response()->json(['message' => 'JadwalParticipant not found'], 404);
        }
        return response()->json($jadwalParticipant);
    }

    // Update a jadwal participant
    public function update(Request $request, $id)
    {
        $jadwalParticipant = JadwalParticipant::findOrFail($id);

        $validated = $request->validate([
            'id_shift' => 'sometimes|required|exists:shifts,id',
        ]);
        if (isset($validated['id_shift'])) {
            // Check if the jadwal participant already exists
            $existingJadwalParticipant = JadwalParticipant::where('id_shift', $validated['id_shift'])
                ->where('id_participant', $jadwalParticipant->id_participant)
                ->first();
            if ($existingJadwalParticipant) {
                return response()->json(['message' => 'JadwalParticipant already exists'], 422);
            }
        }

        $jadwalParticipant->update($validated);

        return response()->json($jadwalParticipant->load(['shift', 'participant']));
    }

    // Delete a jadwal participant
    public function destroy($id)
    {
        $jadwalParticipant = JadwalParticipant::find($id);
        if (!$jadwalParticipant) {
            return response()->json(['message' => 'JadwalParticipant not found'], 404);
        }
        
        $jadwalParticipant->delete();

        return response()->json(['message' => 'JadwalParticipant deleted']);
    }
}
