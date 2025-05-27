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
        // return response()->json($groupParticipants);
        return view('Managment.Group.groups', compact('groupParticipants'));
    }

    // Store a new group participant
    public function store(Request $request)
    {
        // dd($request->all());
        // $validator = Validator::make($request->all(), [
        //     // 'id_participant' => 'required|exists:participants,id',
        //     'group_id' => 'required|exists:groups,id',
        // ]);
        // if ($validator->fails()) {
        //     // return response()->json($validator->errors(), 422);
        //     return back()->withErrors($validator)->withInput();
        // }
        // $validated = $validator->validated();

        foreach ($request->participants as $id) {

            // Check if the participant is already in the group
            $existingGroupParticipant = GroupParticipant::where('id_participant', $id)
                ->first();
            if ($existingGroupParticipant) {
                GroupParticipant::where('id_participant', $id)
                    ->update(['id_group' => $request->group_id]);
                continue; // Skip to the next participant if already exists
            }
            GroupParticipant::create([
                'id_group' => $request->group_id,
                'id_participant' => $id,
            ]);
        }

        // $groupParticipant = GroupParticipant::create($validated);


        // return response()->json($groupParticipant->load(['participant', 'group']), 201);
        return redirect()->route('group.show', $request->group_id)->with('success', 'GroupParticipant created successfully');
    }

    // Show a single group participant
    public function show($id)
    {
        $groupParticipant = GroupParticipant::find($id)->load(['participant', 'group']);
        if (!$groupParticipant) {
            // return response()->json(['message' => 'GroupParticipant not found'], 404);
            return redirect()->route('groupParticipant.index')->withErrors(['message' => 'GroupParticipant not found']);
        }
        // return response()->json($groupParticipant);
        // return view('Managment.GroupParticipant.show', compact('groupParticipant'));
    }

    // Update a group participant
    public function update(Request $request, $id)
    {
        $groupParticipant = GroupParticipant::find($id);
        if (!$groupParticipant) {
            // return response()->json(['message' => 'GroupParticipant not found'], 404);
            return redirect()->route('groupParticipant.index')->withErrors(['message' => 'GroupParticipant not found']);
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
            // return response()->json(['message' => 'Participant already in the group'], 422);
            return back()->withErrors(['message' => 'Participant already in the group'])->withInput();
        }

        $groupParticipant->update($validated);

        // return response()->json($groupParticipant->load(['participant', 'group']));
        return redirect()->route('groupParticipant.index')->with('success', 'GroupParticipant updated successfully');
    }

    // Delete a group participant
    public function destroy(Request $request ,$id)
    {

        $groupParticipant = GroupParticipant::where('id_participant', $id)->first();

        if (!$groupParticipant) {
            // return response()->json(['message' => 'GroupParticipant not found'], 404);
            return redirect()->route('group.show', $request->group_id)->withErrors(['message' => 'GroupParticipant not found']);
        }

        $groupParticipant->delete();

        // return response()->json(['message' => 'GroupParticipant deleted']);
        return redirect()->route('group.show', $request->group_id)->with('success', 'GroupParticipant deleted successfully');
    }
}
