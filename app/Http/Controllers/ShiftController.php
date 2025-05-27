<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ShiftController extends Controller
{
    // List all shifts
    public function index()
    {
        $shifts = Shift::with('jamKerja', 'detailShifts')->get();
        // return response()->json($shifts);
        return view('Managment.Shift.shifts', compact('shifts'));
    }

    // Store a new shift
    public function store(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'nama' => 'required|string|max:100',
            'tanggal_mulai' => 'required|date',
            'id_jam_kerja' => 'required|exists:jam_kerjas,id',
        ]);
        if ($validated->fails()) {
            // return response()->json($validated->errors(), 422);
            return back()->withErrors($validated)->withInput();
        }
        $validated = $validated->validated();
        // Check if the shift already exists
        $existingShift = Shift::where('nama', $validated['nama'])
            ->where('tanggal_mulai', $validated['tanggal_mulai'])
            ->where('id_jam_kerja', $validated['id_jam_kerja'])
            ->first();
        if ($existingShift) {
            return response()->json(['message' => 'Shift already exists'], 422);
        }

        $shift = Shift::create($validated);

        // return response()->json($shift->load('jamKerja'), 201);
        return redirect()->route('shift.index')->with('success', 'Shift created successfully');
    }

    // Show a single shift
    public function show($id)
    {
        $shift = Shift::with('jamKerja')->find($id);
        if (!$shift) {
            return response()->json(['message' => 'Shift not found'], 404);
        }
        // return response()->json($shift);
        return view('Managment.Shift.show', compact('shift'));
    }

    // Update a shift
    public function update(Request $request, $id)
    {
        $shift = Shift::find($id);
        if (!$shift) {
            return response()->json(['message' => 'Shift not found'], 404);
        }
        $validated = Validator::make($request->all(), [
            'nama' => 'sometimes|required|string|max:100',
            'tanggal_mulai' => 'sometimes|required|date',
            'id_jam_kerja' => 'sometimes|required|exists:jam_kerjas,id',
        ]);
        if ($validated->fails()) {
            return response()->json($validated->errors(), 422);
        }
        $validated = $validated->validated();
        // Check if the shift already exists
        $existingShift = Shift::where('nama', $validated['nama'])
            ->where('tanggal_mulai', $validated['tanggal_mulai'])
            ->where('id_jam_kerja', $validated['id_jam_kerja'])
            ->where('id', '!=', $id)
            ->first();
        if ($existingShift) {
            return response()->json(['message' => 'Shift already exists'], 422);
        }

        $shift->update($validated);

        // return response()->json($shift->load('jamKerja'));
        return redirect()->route('shift.index')->with('success', 'Shift updated successfully');
    }

    // Delete a shift
    public function destroy($id)
    {
        $shift = Shift::find($id);
        if (!$shift) {
            return response()->json(['message' => 'Shift not found'], 404);
        }

        // Check if the shift has jadwal_participants
        if ($shift->jadwal_participant()->exists()) {
            return response()->json(['message' => 'Shift cannot be deleted because it has jadwal participants'], 422);
        }

        $shift->delete();

        // return response()->json(['message' => 'Shift deleted']);
        return redirect()->route('shift.index')->with('success', 'Shift deleted successfully');
    }
}
