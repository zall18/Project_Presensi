<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GroupParticipant;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert;

class GroupParticipantController extends Controller
{
    // List all group participants
    public function index()
    {
        $groupParticipants = GroupParticipant::with(['participant', 'group'])->get();
        return view('Managment.Group.groups', compact('groupParticipants'));
    }

    // Store a new group participant
    public function store(Request $request)
    {
        foreach ($request->participants as $id) {
            // Check if the participant is already in the group
            $existingGroupParticipant = GroupParticipant::where('id_participant', $id)
                ->first();
            if ($existingGroupParticipant) {
                GroupParticipant::where('id_participant', $id)
                    ->update(['id_group' => $request->group_id]);
                continue;
            }
            GroupParticipant::create([
                'id_group' => $request->group_id,
                'id_participant' => $id,
            ]);
        }

        Alert::success('Berhasil!', 'GroupParticipant berhasil ditambahkan 🎉');
        return redirect()->route('group.show', $request->group_id)->with('success', 'GroupParticipant created successfully');
    }

    // Show a single group participant
    public function show($id)
    {
        $groupParticipant = GroupParticipant::find($id)->load(['participant', 'group']);
        if (!$groupParticipant) {
            Alert::error('Gagal!', 'GroupParticipant tidak ditemukan!');
            return redirect()->route('groupParticipant.index')->withErrors(['message' => 'GroupParticipant not found']);
        }
        // return view('Managment.GroupParticipant.show', compact('groupParticipant'));
    }

    // Update a group participant
    public function update(Request $request, $id)
    {
        $groupParticipant = GroupParticipant::find($id);
        if (!$groupParticipant) {
            Alert::error('Gagal!', 'GroupParticipant tidak ditemukan!');
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
            Alert::error('Gagal!', 'Peserta sudah ada di group!');
            return back()->withErrors(['message' => 'Participant already in the group'])->withInput();
        }

        $groupParticipant->update($validated);

        Alert::success('Berhasil!', 'GroupParticipant berhasil diupdate 🎉');
        return redirect()->route('groupParticipant.index')->with('success', 'GroupParticipant updated successfully');
    }

    // Delete a group participant
    public function destroy(Request $request ,$id)
    {
        $groupParticipant = GroupParticipant::where('id_participant', $id)->first();

        if (!$groupParticipant) {
            Alert::error('Gagal!', 'GroupParticipant tidak ditemukan!');
            return redirect()->route('group.show', $request->group_id)->withErrors(['message' => 'GroupParticipant not found']);
        }

        $groupParticipant->delete();

        Alert::success('Berhasil!', 'GroupParticipant berhasil dihapus 🎉');
        return redirect()->route('group.show', $request->group_id)->with('success', 'GroupParticipant deleted successfully');
    }
}