<?php

namespace App\Http\Controllers;

use App\Models\Participant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ParticipantsController extends Controller
{
    // List all participants
    public function index(Request $request)
    {
        // $participants = Participant::all();
        $participants = Participant::paginate(10); // Dpagination

        if ($request->search){
            $participants = Participant::where('nama', 'like', '%' . $request->search .'%')
                ->orWhere('no_induk', 'like', '%' . $request->search . '%')
                ->orWhere('id_kartu', 'like', '%' . $request->search . '%')
                ->paginate(10);
        } elseif ($request->sort && $request->direction) {
            $participants = Participant::orderBy($request->sort, $request->direction)->paginate(10);
        } else {
            $participants = Participant::paginate(10);
        }

        // return response()->json($participants);
        return view('Managment.Participant.participants', compact('participants'));
    }

    public function create()
    {
        // Assuming you want to return a view for creating a new participant
        return view('Managment.Participant.create');
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
            // return response()->json($validated->errors(), 422);
            return back()->withErrors($validated)->withInput();
        }
        $validated = $validated->validated();
        $participant = Participant::create($validated);

        // return response()->json($participant, 201);
        // return back()->with('success', 'Participant created successfully');
        return redirect()->route('participant.index')->with('success', 'Participant created successfully');
    }

    // Show a single participant
    public function show($id)
    {
        $participant = Participant::find($id);
        if (!$participant) {
            return response()->json(['message' => 'Participant not found'], 404);
        }

        // return response()->json($participant);
        return view('Managment.Participant.show', compact('participant'));
    }

    // show a participant by id_kartu
    public function showByIdKartu($id_kartu)
    {
        $participant = Participant::where('id_kartu', $id_kartu)->first();
        if (!$participant) {
            return response()->json(['message' => 'Participant not found'], 404);
        }

        // return response()->json($participant);
        return view('Managment.Participant.show', compact('participant'));
    }

    public function edit($id)
    {
        $participant = Participant::find($id);
        if (!$participant) {
            // return response()->json(['message' => 'Participant not found'], 404);
            return back()->with('error', 'Participant not found');
        }

        // Assuming you want to return a view for editing
        return view('Managment.Participant.edit', compact('participant'));
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

        // return response()->json($participant);
        // return back()->with('success', 'Participant updated successfully');
        return redirect()->route('participant.index')->with('success', 'Participant updated successfully');
    }

    // Delete a participant
    public function destroy($id)
    {
        $participant = Participant::find($id);
        if (!$participant) {
            // return response()->json(['message' => 'Participant not found'], 404);
            return back()->with('error', 'Participant not found');
        }
        $participant->delete();

        // return response()->json(['message' => 'Participant deleted']);
        return back()->with('success', 'Participant deleted successfully');
    }
}
