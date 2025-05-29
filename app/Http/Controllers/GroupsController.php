<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Participant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GroupsController extends Controller
{
    // List all groups
    public function index()
    {
        $groups = Group::all();
        // return response()->json($groups);
        return view('Managment.Group.groups', compact('groups'));
    }

    public function create(){
        return view('Managment.Group.create');
    }

    // Store a new group
    public function store(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'nama' => 'required|string|max:255|unique:groups',
        ]);
        if ($validated->fails()) {
            return response()->json($validated->errors(), 422);
        }
        $validated = $validated->validated();

        $group = Group::create($validated);

        // return response()->json($group, 201);
        return redirect()->route('group.index')->with('success', 'Group created successfully');
    }

    public function addParticipant($id)
    {
        $group = Group::find($id);
        if (!$group) {
            return response()->json(['message' => 'Group not found'], 404);
        }
        $participants = Participant::all();
        $groupParticipantIds = Group::find($id)->participants()->pluck('participants.id')->toArray();
        // return response()->json([
        //     'group' => $group,
        //     'participants' => $participants,
        //     'groupParticipants' => $groupParticipants,
        // ]);

        // return response()->json($participants);
        return view('Managment.Group.groupParticipant.create', compact('group', 'participants', 'groupParticipantIds'));
    }


    // Show a single group
    public function show(Request $request, $id)
    {
        $group = Group::with('participants')->find($id);
        if (!$group) {
            return response()->json(['message' => 'Group not found'], 404);
        }
        $participantsQuery = $group->participants();

        if ($request->search) {
            $participantsQuery->where(function($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->search . '%')
                ->orWhere('no_induk', 'like', '%' . $request->search . '%')
                ->orWhere('id_kartu', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->sort && $request->direction) {
            $participantsQuery->orderBy($request->sort, $request->direction);
        }

        $participants = $participantsQuery->paginate(10);


        return view('Managment.Group.show', compact('group', 'participants'));
    }

    public function edit($id)
    {
        $group = Group::find($id);
        if (!$group) {
            return response()->json(['message' => 'Group not found'], 404);
        }
        // return response()->json($group);
        return view('Managment.Group.edit', compact('group'));
    }

    // Update a group
    public function update(Request $request, $id)
    {
        $group = Group::find($id);
        if (!$group) {
            return response()->json(['message' => 'Group not found'], 404);
        }

        $validated = Validator::make($request->all(), [
            'nama' => 'sometimes|required|string|max:255|unique:groups,nama,' . $group->id,
        ]);
        if ($validated->fails()) {
            return response()->json($validated->errors(), 422);
        }
        $validated = $validated->validated();
        $group->update($validated);

        // return response()->json($group);
        return redirect()->route('group.index')->with('success', 'Group updated successfully');

    }

    // Delete a group
    public function destroy($id)
    {
        $group = Group::find($id);
        if (!$group) {
            return response()->json(['message' => 'Group not found'], 404);
        }
        // Check if the group has participants
        if ($group->participants()->count() > 0) {
            return response()->json(['message' => 'Group cannot be deleted because it has participants'], 422);
        }
        $group->delete();

        // return response()->json(['message' => 'Group deleted']);
        return redirect()->route('group.index')->with('success', 'Group deleted successfully');
    }
}
