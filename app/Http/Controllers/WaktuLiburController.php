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
        $waktuLiburs = WaktuLibur::all();
        // return response()->json($waktuLibur);
        return view('Managment.WaktuLibur.waktuLiburs', compact('waktuLiburs'));
    }
    
    public function create()
    {
        // return response()->json(['message' => 'Create Waktu Libur']);
        return view('Managment.WaktuLibur.create');
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
            // return response()->json($validated->errors(), 422);
            return back()->withErrors($validated)->withInput();
        }
        $validated = $validated->validated();
        // Check if the waktu libur already exists
        $existingWaktuLibur = WaktuLibur::where('nama_libur', $validated['nama_libur'])
            ->where('tanggal_mulai', $validated['tanggal_mulai'])
            ->where('tanggal_akhir', $validated['tanggal_akhir'])
            ->first();
        if ($existingWaktuLibur) {
            // return response()->json(['message' => 'WaktuLibur already exists'], 422);
            return back()->withErrors(['message' => 'WaktuLibur already exists'])->withInput();
        }

        $waktuLibur = WaktuLibur::create($validated);

        // return response()->json($waktuLibur, 201);
        return redirect()->route('waktuLibur.index')->with('success', 'Waktu Libur created successfully');
    }

    // Show a single waktu libur
    public function show($id)
    {
        $waktuLibur = WaktuLibur::find($id);
        if (!$waktuLibur) {
            // return response()->json(['message' => 'WaktuLibur not found'], 404);
            return redirect()->route('waktuLibur.index')->withErrors(['message' => 'WaktuLibur not found']);
        }
        // return response()->json($waktuLibur);
        return view('Managment.WaktuLibur.show', compact('waktuLibur'));
    }

    public function edit($id)
    {
        $waktuLibur = WaktuLibur::find($id);
        if (!$waktuLibur) {
            // return response()->json(['message' => 'WaktuLibur not found'], 404);
            return redirect()->route('waktuLibur.index')->withErrors(['message' => 'WaktuLibur not found']);
        }
        // return response()->json($waktuLibur);
        return view('Managment.WaktuLibur.edit', compact('waktuLibur'));
    }

    // Update a waktu libur
    public function update(Request $request, $id)
    {
        $waktuLibur = WaktuLibur::find($id);
        if (!$waktuLibur) {
            // return response()->json(['message' => 'WaktuLibur not found'], 404);
            return redirect()->route('waktuLibur.index')->withErrors(['message' => 'WaktuLibur not found']);
        }

        $validated = Validator::make($request->all(), [
            'nama_libur' => 'sometimes|required|string|max:255',
            'tanggal_mulai' => 'sometimes|required|date',
            'tanggal_akhir' => 'sometimes|required|date|after_or_equal:tanggal_mulai',
        ]);
        if ($validated->fails()) {
            // return response()->json($validated->errors(), 422);
            return back()->withErrors($validated)->withInput();
        }
        $validated = $validated->validated();
        // Check if the waktu libur already exists with different ID
        $existingWaktuLibur = WaktuLibur::where('nama_libur', $validated['nama_libur'])
            ->where('tanggal_mulai', $validated['tanggal_mulai'])
            ->where('tanggal_akhir', $validated['tanggal_akhir'])
            ->where('id', '!=', $id)
            ->first();
        if ($existingWaktuLibur) {
            // return response()->json(['message' => 'WaktuLibur already exists'], 422);
            return back()->withErrors(['message' => 'WaktuLibur already exists'])->withInput();
        }

        $waktuLibur->update($validated);

        // return response()->json($waktuLibur);
        return redirect()->route('waktuLibur.index')->with('success', 'Waktu Libur updated successfully');
    }

    // Delete a waktu libur
    public function destroy($id)
    {
        $waktuLibur = WaktuLibur::find($id);
        if (!$waktuLibur) {
            // return response()->json(['message' => 'WaktuLibur not found'], 404);
            return redirect()->route('waktuLibur.index')->withErrors(['message' => 'WaktuLibur not found']);
        }
        $waktuLibur->delete();

        // return response()->json(['message' => 'WaktuLibur deleted']);
        return redirect()->route('waktuLibur.index')->with('success', 'Waktu Libur deleted successfully');
    }
}
