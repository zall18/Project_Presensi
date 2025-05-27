<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JadwalParticipant;
use App\Models\Shift;
use Illuminate\Support\Facades\Validator;

class JadwalParticipantController extends Controller
{
    // List all jadwal participants
    public function index()
    {
        $jadwalParticipants = JadwalParticipant::with(['shift', 'participant'])->get();
        $shifts = Shift::all();
        // return response()->json($jadwalParticipants);
        return view('Managment.JadwalParticipant.jadwalParticipants', compact('jadwalParticipants', 'shifts'));
    }

    // Store a new jadwal participant
    public function store(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'id_shift' => 'required|exists:shifts,id',
            'id_participant' => 'required|exists:participants,id',
        ]);
        if ($validated->fails()) {
            // return response()->json($validated->errors(), 422);
            return back()->withErrors($validated)->withInput();
        }
        $validated = $validated->validated();
        // Check if the jadwal participant already exists
        $existingJadwalParticipant = JadwalParticipant::where('id_shift', $validated['id_shift'])
            ->where('id_participant', $validated['id_participant'])
            ->first();
        if ($existingJadwalParticipant) {
            // return response()->json(['message' => 'JadwalParticipant already exists'], 422);
            return back()->withErrors(['message' => 'JadwalParticipant already exists'])->withInput();
        }

        $jadwalParticipant = JadwalParticipant::create($validated);

        // return response()->json($jadwalParticipant->load(['shift', 'participant']), 201);
        return redirect()->route('jadwalParticipant.index')->with('success', 'JadwalParticipant created successfully');
    }

    // Show a single jadwal participant
    public function show($id)
    {
        $jadwalParticipant = JadwalParticipant::with(['shift', 'participant'])->find($id);
        if (!$jadwalParticipant) {
            // return response()->json(['message' => 'JadwalParticipant not found'], 404);
            return redirect()->route('jadwalParticipant.index')->withErrors(['message' => 'JadwalParticipant not found']);
        }
        // return response()->json($jadwalParticipant);
        return view('Managment.JadwalParticipant.show', compact('jadwalParticipant'));
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
                // return response()->json(['message' => 'JadwalParticipant already exists'], 422);
                return back()->withErrors(['message' => 'JadwalParticipant already exists'])->withInput();
            }
        }

        $jadwalParticipant->update($validated);

        // return response()->json($jadwalParticipant->load(['shift', 'participant']));
        return redirect()->route('jadwalParticipant.index')->with('success', 'JadwalParticipant updated successfully');
    }

    // Delete a jadwal participant
    public function destroy($id)
    {
        $jadwalParticipant = JadwalParticipant::find($id);
        if (!$jadwalParticipant) {
            // return response()->json(['message' => 'JadwalParticipant not found'], 404);
            return redirect()->route('jadwalParticipant.index')->withErrors(['message' => 'JadwalParticipant not found']);
        }
        
        $jadwalParticipant->delete();

        // return response()->json(['message' => 'JadwalParticipant deleted']);
        return redirect()->route('jadwalParticipant.index')->with('success', 'JadwalParticipant deleted successfully');
    }
}
