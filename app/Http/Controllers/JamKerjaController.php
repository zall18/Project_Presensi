<?php

namespace App\Http\Controllers;

use App\Models\JamKerja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class JamKerjaController extends Controller
{
    // List all jam kerja
    public function index()
    {
        $jamKerjas = JamKerja::all();
        // return response()->json($jamKerjas);
        return view('Managment.JamKerja.jamKerja', compact('jamKerjas'));
    }

    // Store a new jam kerja
    public function store(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'nama' => 'required|string|max:100|unique:jam_kerjas,nama',
            'jam_masuk' => 'required|date_format:H:i:s',
            'jam_pulang' => 'required|date_format:H:i:s',
            'toleransi_terlambat' => 'required|integer|min:0',
            'toleransi_pulang_cepat' => 'required|integer|min:0',
            'jam_mulai_scan_masuk' => 'required|date_format:H:i:s',
            'jam_mulai_scan_keluar' => 'required|date_format:H:i:s',
            'status_check_in' => 'required|in:on-time,late,absent,excused',
            'status_check_out' => 'required|in:on-time,early,absent,excused',
        ]);
        if ($validated->fails()) {
            // return response()->json($validated->errors(), 422);
            return back()->withErrors($validated)->withInput();
        }
        $validated = $validated->validated();

        // Check if jam_masuk is before jam_pulang
        if ($validated['jam_masuk'] >= $validated['jam_pulang']) {
            // return response()->json(['message' => 'Jam masuk harus sebelum jam pulang'], 422);
            return back()->withErrors(['jam_masuk' => 'Jam masuk harus sebelum jam pulang'])->withInput();
        }

        // Check if jam_mulai_scan_masuk is before jam_mulai_scan_keluar
        if ($validated['jam_mulai_scan_masuk'] >= $validated['jam_mulai_scan_keluar']) {
            // return response()->json(['message' => 'Jam mulai scan masuk harus sebelum jam mulai scan keluar'], 422);
            return back()->withErrors(['jam_mulai_scan_masuk' => 'Jam mulai scan masuk harus sebelum jam mulai scan keluar'])->withInput();
        }

        // Check if jam_mulai_scan_masuk is before or equal to jam_masuk
        if ($validated['jam_mulai_scan_masuk'] > $validated['jam_masuk']) {
            // return response()->json(['message' => 'Jam mulai scan masuk harus sebelum atau sama dengan jam masuk'], 422);
            return back()->withErrors(['jam_mulai_scan_masuk' => 'Jam mulai scan masuk harus sebelum atau sama dengan jam masuk'])->withInput();
        }

        // Check if jam_mulai_scan_keluar is before or equal to jam_pulang
        if ($validated['jam_mulai_scan_keluar'] > $validated['jam_pulang']) {
            // return response()->json(['message' => 'Jam mulai scan keluar harus sebelum atau sama dengan jam pulang'], 422);
            return back()->withErrors(['jam_mulai_scan_keluar' => 'Jam mulai scan keluar harus sebelum atau sama dengan jam pulang'])->withInput();
        }

        $jamKerja = JamKerja::create($validated);

        // return response()->json($jamKerja, 201);
        return redirect()->route('jamKerja.index')->with('success', 'Jam Kerja created successfully');
    }

    // Show a single jam kerja
    public function show($id)
    {
        $jamKerja = JamKerja::find($id);
        if (!$jamKerja) {
            return response()->json(['message' => 'JamKerja not found'], 404);
        }
        return response()->json($jamKerja);
    }

    // Update a jam kerja
    public function update(Request $request, $id)
    {
        $jamKerja = JamKerja::findOrFail($id);

        $validated = Validator::make($request->all(), [
            'nama' => 'sometimes|required|string|max:100',
            'jam_masuk' => 'sometimes|required|date_format:H:i:s',
            'jam_pulang' => 'sometimes|required|date_format:H:i:s',
            'toleransi_terlambat' => 'sometimes|required|integer|min:0',
            'toleransi_pulang_cepat' => 'sometimes|required|integer|min:0',
            'jam_mulai_scan_masuk' => 'sometimes|required|date_format:H:i:s',
            'jam_mulai_scan_keluar' => 'sometimes|required|date_format:H:i:s',
            'status_check_in' => 'sometimes|required|in:on-time,late,absent,excused',
            'status_check_out' => 'sometimes|required|in:on-time,early,absent,excused',
        ]);
        if ($validated->fails()) {
            return response()->json($validated->errors(), 422);
        }

        $validated = $validated->validated();

        // Check if the name is unique
        if (JamKerja::where('nama', $validated['nama'])->where('id', '!=', $id)->exists()) {
            return response()->json(['message' => 'Nama jam kerja sudah ada'], 422);
        }

        // Check if jam_masuk is before jam_pulang
        if ($validated['jam_masuk'] >= $validated['jam_pulang']) {
            return response()->json(['message' => 'Jam masuk harus sebelum jam pulang'], 422);
        }

        // Check if jam_mulai_scan_masuk is before jam_mulai_scan_keluar
        if ($validated['jam_mulai_scan_masuk'] >= $validated['jam_mulai_scan_keluar']) {
            return response()->json(['message' => 'Jam mulai scan masuk harus sebelum jam mulai scan keluar'], 422);
        }

        // Check if jam_mulai_scan_masuk is before or equal to jam_masuk
        if ($validated['jam_mulai_scan_masuk'] > $validated['jam_masuk']) {
            return response()->json(['message' => 'Jam mulai scan masuk harus sebelum atau sama dengan jam masuk'], 422);
        }

        // Check if jam_mulai_scan_keluar is before or equal to jam_pulang
        if ($validated['jam_mulai_scan_keluar'] > $validated['jam_pulang']) {
            return response()->json(['message' => 'Jam mulai scan keluar harus sebelum atau sama dengan jam pulang'], 422);
        }

        $jamKerja->update($validated);

        // return response()->json($jamKerja);
        return redirect()->route('jamKerja.index')->with('success', 'Jam Kerja updated successfully');

    }

    // Delete a jam kerja
    public function destroy($id)
    {
        $jamKerja = JamKerja::find($id);
        if (!$jamKerja) {
            return response()->json(['message' => 'JamKerja not found'], 404);
        }
        // Check if the jam kerja is used in any shifts
        if ($jamKerja->shift()->count() > 0) {
            return response()->json(['message' => 'JamKerja cannot be deleted because it is used in shifts'], 422);
        }

        $jamKerja->delete();

        // return response()->json(['message' => 'JamKerja deleted']);
        return back()->with('message', "Success to delete");

    }
}
