<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WaktuLibur;
use Illuminate\Support\Facades\Validator;

class WaktuLiburController extends Controller
{
    // List all waktu libur
    public function index()
    {
        $waktuLibur = WaktuLibur::all();
        return response()->json($waktuLibur);
    }

    // Store a new waktu libur
    public function store(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'nama_libur' => 'required|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_akhir' => 'required|date|after_or_equal:tanggal_mulai',
        ]);
        if ($validated->fails()) {
            return response()->json($validated->errors(), 422);
        }
        $validated = $validated->validated();
        // Check if the waktu libur already exists
        $existingWaktuLibur = WaktuLibur::where('nama_libur', $validated['nama_libur'])
            ->where('tanggal_mulai', $validated['tanggal_mulai'])
            ->where('tanggal_akhir', $validated['tanggal_akhir'])
            ->first();
        if ($existingWaktuLibur) {
            return response()->json(['message' => 'WaktuLibur already exists'], 422);
        }

        $waktuLibur = WaktuLibur::create($validated);

        return response()->json($waktuLibur, 201);
    }

    // Show a single waktu libur
    public function show($id)
    {
        $waktuLibur = WaktuLibur::find($id);
        if (!$waktuLibur) {
            return response()->json(['message' => 'WaktuLibur not found'], 404);
        }
        return response()->json($waktuLibur);
    }

    // Update a waktu libur
    public function update(Request $request, $id)
    {
        $waktuLibur = WaktuLibur::find($id);
        if (!$waktuLibur) {
            return response()->json(['message' => 'WaktuLibur not found'], 404);
        }

        $validated = Validator::make($request->all(), [
            'nama_libur' => 'sometimes|required|string|max:255',
            'tanggal_mulai' => 'sometimes|required|date',
            'tanggal_akhir' => 'sometimes|required|date|after_or_equal:tanggal_mulai',
        ]);
        if ($validated->fails()) {
            return response()->json($validated->errors(), 422);
        }
        $validated = $validated->validated();
        // Check if the waktu libur already exists with different ID
        $existingWaktuLibur = WaktuLibur::where('nama_libur', $validated['nama_libur'])
            ->where('tanggal_mulai', $validated['tanggal_mulai'])
            ->where('tanggal_akhir', $validated['tanggal_akhir'])
            ->where('id', '!=', $id)
            ->first();
        if ($existingWaktuLibur) {
            return response()->json(['message' => 'WaktuLibur already exists'], 422);
        }

        $waktuLibur->update($validated);

        return response()->json($waktuLibur);
    }

    // Delete a waktu libur
    public function destroy($id)
    {
        $waktuLibur = WaktuLibur::find($id);
        if (!$waktuLibur) {
            return response()->json(['message' => 'WaktuLibur not found'], 404);
        }
        $waktuLibur->delete();

        return response()->json(['message' => 'WaktuLibur deleted']);
    }
}
