<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Participant;
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

    public function remove(Request $request)
    {
        $shift = Shift::find($request->id_shift);
        if (!$shift) {
            // return response()->json(['message' => 'Shift not found'], 404);
            return redirect()->route('jadwalParticipant.index')->withErrors(['message' => 'Shift not found']);
        }
        $participants =  Participant::whereHas('jadwalParticipant', function ($query) use ($shift) {
            $query->where('id_shift', $shift->id);
        })->get();
        // return response()->json($participants);
        return view('Managment.JadwalParticipant.remove', compact('shift', 'participants'));
    }


    public function create(Request $request)
    {
        $shift = Shift::find($request->id_shift);
        if (!$shift) {
            // return response()->json(['message' => 'Shift not found'], 404);
            return redirect()->route('jadwalParticipant.index')->withErrors(['message' => 'Shift not found']);
        }
        $grups = Group::all();

        if($request->filter_grup === 'not'){
            $jadwalParticipants = JadwalParticipant::all();
            $participants = Participant::whereNotIn('id', $jadwalParticipants->pluck('id'))->get();
        } else if($request->filter_grup && $request->filter_grup != 'all') {
            $participants = Group::find($request->filter_grup)->participants;
        }else{
            $participants = Participant::all();
        }

        if($request->search){
            $participants = $participants->filter(function($participant) use ($request) {
                return str_contains(strtolower($participant->nama), strtolower($request->search));
            });
        }

        $jadwalParticipantIds = JadwalParticipant::where('id_shift', $shift->id)
            ->pluck('id_participant')
            ->toArray();
        
        // return response()->json([
        //     'shift' => $shift,
        //     'participants' => $participants,
        //     'grups' => $grups,
        //     'participantIds' => $jadwalParticipantIds,
        //     'doentHaveJadwalParticipant' => $doentHaveJadwalParticipant,
        // ]);

        return view('Managment.JadwalParticipant.create', compact('shift', 'grups', 'participants', 'jadwalParticipantIds'));
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $validated = Validator::make($request->all(), [
            'id_shift' => 'required|exists:shifts,id',
            // 'id_participant' => 'required|exists:participants,id',
        ]);
        if ($validated->fails()) {
            // return response()->json($validated->errors(), 422);
            return back()->withErrors($validated)->withInput();
        }
        $validated = $validated->validated();

        foreach ($request->participants as $participantId) {
            // Check if the jadwal participant already exists
            $existingJadwalParticipant = JadwalParticipant::where('id_participant', $participantId)
                ->first();
            if ($existingJadwalParticipant) {
                // return response()->json(['message' => 'JadwalParticipant already exists'], 422);
                JadwalParticipant::where('id_participant', $participantId)
                    ->update(['id_shift' => $validated['id_shift']]);
                continue;
            }

            JadwalParticipant::create([
                'id_shift' => $validated['id_shift'],
                'id_participant' => $participantId,
            ]);
        }

        // Check if the jadwal participant already exists
        // $existingJadwalParticipant = JadwalParticipant::where('id_shift', $validated['id_shift'])
        //     ->where('id_participant', $validated['id_participant'])
        //     ->first();
        // if ($existingJadwalParticipant) {
        //     // return response()->json(['message' => 'JadwalParticipant already exists'], 422);
        //     return back()->withErrors(['message' => 'JadwalParticipant already exists'])->withInput();
        // }

        // $jadwalParticipant = JadwalParticipant::create($validated);

        // return response()->json($jadwalParticipant->load(['shift', 'participant']), 201);
        return redirect()->route('jadwalParticipant.index')->with('success', 'JadwalParticipant created successfully');
    }

    // Show a single jadwal participant
    public function show(Request $request)
    {
        // dd($request->all());
        $jadwalParticipant = JadwalParticipant::with(['shift', 'participant'])
            ->where('id_shift', $request->id_shift)
            ->get();
        if ($jadwalParticipant->isEmpty()) {
            // return response()->json(['message' => 'JadwalParticipant not found'], 404);
            return redirect()->route('jadwalParticipant.index')->withErrors(['message' => 'JadwalParticipant not found']);
        }
    
        $shift = Shift::find($request->id_shift);
        if (!$shift) {
            // return response()->json(['message' => 'Shift not found'], 404);
            return redirect()->route('jadwalParticipant.index')->withErrors(['message' => 'Shift not found']);
        }
        $participants =  Participant::whereHas('jadwalParticipant', function ($query) use ($shift) {
            $query->where('id_shift', $shift->id);
        })->get();
        // return response()->json($jadwalParticipant);
        return view('Managment.JadwalParticipant.show', compact('shift', 'participants', 'jadwalParticipant'));
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
    public function destroy(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'id_shift' => 'required|exists:shifts,id',
        ]);
        if ($validator->fails()) {
            // return response()->json($validated->errors(), 422);
            return back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();

        foreach ($request->participants as $participantId) {
            JadwalParticipant::where('id_shift', $validated['id_shift'])
                ->where('id_participant', $participantId)
                ->delete();
        }

        // return response()->json(['message' => 'JadwalParticipant deleted']);
        return redirect()->route('jadwalParticipant.index')->with('success', 'JadwalParticipant deleted successfully');
    }
}
