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
        // return response()->json($detailShifts);
        return view('Managment.Shift.shifts');
    }

    // Store a new detail shift
    public function store(Request $request)
    {
        // dd($request->all());
        $validated = Validator::make($request->all(), [
            'hari' => 'required|string|max:20',
        ]);
        if ($validated->fails()) {
            // return response()->json($validated->errors(), 422);
            return back()->withErrors($validated)->withInput();
        }
        $validated = $validated->validated();
        // Check if the detail shift already exists
        $existingDetailShift = DetailShift::where('id_shift', $request->shift_id)
            ->where('hari', $validated['hari'])
            ->first();
        if ($existingDetailShift) {
            return back()->withErrors(['message' => 'DetailShift already exists'])->withInput();
        }

        // Validate shift exists
        $shift = Shift::find($request->shift_id);
        if (!$shift) {
            // return response()->json(['message' => 'Shift not found'], 404);
            return back()->withErrors(['message' => 'Shift not found'])->withInput();
        }
        $validated['id_shift'] = $request->shift_id;

        // validate hari
        $hari = ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu'];
        if (!in_array(strtolower($validated['hari']), $hari)) {
            // return response()->json(['message' => 'Hari tidak valid'], 422);
            return back()->withErrors(['message' => 'Hari tidak valid'])->withInput();
        }

        $detailShift = DetailShift::create($validated);

        // return response()->json($detailShift->load('shift'), 201);
        return redirect()->route('shift.show', $request->shift_id)->with('success', 'DetailShift created successfully');
    }

    // Show a single detail shift
    public function show($id)
    {
        $detailShift = DetailShift::with('shift')->find($id);
        if (!$detailShift) {
            // return response()->json(['message' => 'DetailShift not found'], 404);
            return response()->json(['message' => 'DetailShift not found'], 404);
        }
        // return response()->json($detailShift);
        // return view('Managment.Shift.showDetailShift', compact('detailShift'));
    }

    // Update a detail shift
    public function update(Request $request, $id)
    {
        $detailShift = DetailShift::find($id);
        if (!$detailShift) {
            // return response()->json(['message' => 'DetailShift not found'], 404);
            return back()->with('error', 'DetailShift not found');
        }


        $validated = Validator::make($request->all(), [
            'hari' => 'sometimes|required|string|max:20',
        ]);
        if ($validated->fails()) {
            // return response()->json($validated->errors(), 422);
            return back()->withErrors($validated)->withInput();
        }
        $validated = $validated->validated();
        // Check if the detail shift already exists
        $existingDetailShift = DetailShift::where('id_shift', $detailShift->id_shift)
            ->where('hari', $validated['hari'])
            ->where('id', '!=', $id)
            ->first();
        if ($existingDetailShift) {
            // return response()->json(['message' => 'DetailShift already exists'], 422);
            return back()->withErrors(['message' => 'DetailShift already exists'])->withInput();
        }

        // validate hari
        $hari = ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu'];
        if (!in_array(strtolower($validated['hari']), $hari)) {
            // return response()->json(['message' => 'Hari tidak valid'], 422);
            return back()->withErrors(['message' => 'Hari tidak valid'])->withInput();
        }

        $detailShift->update($validated);

        // return response()->json($detailShift->load('shift'));
        return redirect()->route('shift.show', $detailShift->id_shift)->with('success', 'DetailShift updated successfully');
    }

    // Delete a detail shift
    public function destroy($id)
    {
        $detailShift = DetailShift::find($id);
        if (!$detailShift) {
            // return response()->json(['message' => 'DetailShift not found'], 404);
            return response()->json(['message' => 'DetailShift not found'], 404);
        }

        $detailShift->delete();

        // return response()->json(['message' => 'DetailShift deleted']);
        return redirect()->route('shift.show', $detailShift->id_shift)->with('success', 'DetailShift deleted successfully');
    }
}
