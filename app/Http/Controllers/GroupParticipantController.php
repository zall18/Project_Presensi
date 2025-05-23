<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GroupParticipant;
use Illuminate\Support\Facades\Validator;

class GroupParticipantController extends Controller
{
    // List all group participants
    public function index()
    {
        $groupParticipants = GroupParticipant::with(['participant', 'group'])->get();
        return response()->json($groupParticipants);
    }

    // Store a new group participant
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_participant' => 'required|exists:participants,id',
            'id_group' => 'required|exists:groups,id',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $validated = $validator->validated();

        // Check if the participant is already in the group
        $existingGroupParticipant = GroupParticipant::where('id_participant', $validated['id_participant'])
            ->where('id_group', $validated['id_group'])
            ->first();

        if ($existingGroupParticipant) {
            return response()->json(['message' => 'Participant already in the group'], 422);
        }

        $groupParticipant = GroupParticipant::create($validated);

        return response()->json($groupParticipant->load(['participant', 'group']), 201);
    }

    // Show a single group participant
    public function show($id)
    {
        $groupParticipant = GroupParticipant::find($id)->load(['participant', 'group']);
        if (!$groupParticipant) {
            return response()->json(['message' => 'GroupParticipant not found'], 404);
        }
        return response()->json($groupParticipant);
    }

    // Update a group participant
    public function update(Request $request, $id)
    {
        $groupParticipant = GroupParticipant::find($id);
        if (!$groupParticipant) {
            return response()->json(['message' => 'GroupParticipant not found'], 404);
        }

        $validated = $request->validate([
            'id_participant' => 'required|exists:participants,id',
            'id_group' => 'required|exists:groups,id',
        ]);

        // Check if the participant is already in the group
        $existingGroupParticipant = GroupParticipant::where('id_participant', $validated['id_participant'])
            ->where('id_group', $validated['id_group'])
            ->where('id', '!=', $id)
            ->first();

        if ($existingGroupParticipant) {
            return response()->json(['message' => 'Participant already in the group'], 422);
        }

        $groupParticipant->update($validated);

        return response()->json($groupParticipant->load(['participant', 'group']));
    }

    // Delete a group participant
    public function destroy($id)
    {
        $groupParticipant = GroupParticipant::find($id);

        if (!$groupParticipant) {
            return response()->json(['message' => 'GroupParticipant not found'], 404);
        }
        
        $groupParticipant->delete();

        return response()->json(['message' => 'GroupParticipant deleted']);
    }
}
