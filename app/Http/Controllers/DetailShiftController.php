<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use Illuminate\Http\Request;
use App\Models\DetailShift;
use Illuminate\Support\Facades\Validator;

class DetailShiftController extends Controller
{
    // List all detail shifts
    public function index()
    {
        $detailShifts = DetailShift::with('shift')->get();
        return response()->json($detailShifts);
    }

    // Store a new detail shift
    public function store(Request $request, $shift_id)
    {
        $validated = Validator::make($request->all(), [
            'hari' => 'required|string|max:20',
        ]);
        if ($validated->fails()) {
            return response()->json($validated->errors(), 422);
        }
        $validated = $validated->validated();
        // Check if the detail shift already exists
        $existingDetailShift = DetailShift::where('id_shift', $shift_id)
            ->where('hari', $validated['hari'])
            ->first();
        if ($existingDetailShift) {
            return response()->json(['message' => 'DetailShift already exists'], 422);
        }
        
        // Validate shift exists
        $shift = Shift::find($shift_id);
        if (!$shift) {
            return response()->json(['message' => 'Shift not found'], 404);
        }
        $validated['id_shift'] = $shift_id;

        // validate hari 
        $hari = ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu'];
        if (!in_array(strtolower($validated['hari']), $hari)) {
            return response()->json(['message' => 'Hari tidak valid'], 422);
        }
        
        $detailShift = DetailShift::create($validated);

        return response()->json($detailShift->load('shift'), 201);
    }

    // Show a single detail shift
    public function show($id)
    {
        $detailShift = DetailShift::with('shift')->find($id);
        if (!$detailShift) {
            return response()->json(['message' => 'DetailShift not found'], 404);
        }
        return response()->json($detailShift);
    }

    // Update a detail shift
    public function update(Request $request, $id)
    {
        $detailShift = DetailShift::find($id);
        if (!$detailShift) {
            return response()->json(['message' => 'DetailShift not found'], 404);
        }


        $validated = Validator::make($request->all(), [
            'hari' => 'sometimes|required|string|max:20',
        ]);
        if ($validated->fails()) {
            return response()->json($validated->errors(), 422);
        }
        $validated = $validated->validated();
        // Check if the detail shift already exists
        $existingDetailShift = DetailShift::where('id_shift', $detailShift->id_shift)
            ->where('hari', $validated['hari'])
            ->where('id', '!=', $id)
            ->first();
        if ($existingDetailShift) {
            return response()->json(['message' => 'DetailShift already exists'], 422);
        }

        // validate hari 
        $hari = ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu'];
        if (!in_array(strtolower($validated['hari']), $hari)) {
            return response()->json(['message' => 'Hari tidak valid'], 422);
        }

        $detailShift->update($validated);

        return response()->json($detailShift->load('shift'));
    }

    // Delete a detail shift
    public function destroy($id)
    {
        $detailShift = DetailShift::find($id);
        if (!$detailShift) {
            return response()->json(['message' => 'DetailShift not found'], 404);
        }

        $detailShift->delete();

        return response()->json(['message' => 'DetailShift deleted']);
    }
}
