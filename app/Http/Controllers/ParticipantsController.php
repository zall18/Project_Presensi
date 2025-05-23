<?php

namespace App\Http\Controllers;

use App\Models\Participant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ParticipantsController extends Controller
{
    // List all participants
    public function index()
    {
        $participants = Participant::all();
        return response()->json($participants);
    }

    // Store a new participant
    public function store(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'no_induk' => 'required|string|max:50|unique:participants',
            'nama' => 'required|string|max:100',
            'id_kartu' => 'required|string|max:50|unique:participants',
            'no_hp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
        ]);

        if ($validated->fails()) {
            return response()->json($validated->errors(), 422);
        }
        $validated = $validated->validated();
        $participant = Participant::create($validated);

        return response()->json($participant, 201);
    }

    // Show a single participant
    public function show($id)
    {
        $participant = Participant::find($id);
        if (!$participant) {
            return response()->json(['message' => 'Participant not found'], 404);
        }

        return response()->json($participant);
    }

    // show a participant by id_kartu
    public function showByIdKartu($id_kartu)
    {
        $participant = Participant::where('id_kartu', $id_kartu)->first();
        if (!$participant) {
            return response()->json(['message' => 'Participant not found'], 404);
        }

        return response()->json($participant);
    }

    // Update a participant
    public function update(Request $request, $id)
    {
        $participant = Participant::findOrFail($id);

        $validated = $request->validate([
            'no_induk' => 'sometimes|required|string|max:50|unique:participants,no_induk,' . $id,
            'nama' => 'sometimes|required|string|max:100',
            'id_kartu' => 'sometimes|required|string|max:50|unique:participants,id_kartu,' . $id,
            'no_hp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
        ]);

        $participant->update($validated);

        return response()->json($participant);
    }

    // Delete a participant
    public function destroy($id)
    {
        $participant = Participant::find($id);
        if (!$participant) {
            return response()->json(['message' => 'Participant not found'], 404);
        }
        $participant->delete();

        return response()->json(['message' => 'Participant deleted']);
    }
}
