<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WaktuLibur;
use App\Models\GroupLibur;
use App\Models\Group;
use Illuminate\Support\Facades\Validator;

class WaktuLiburController extends Controller
{
    // List all waktu libur
    public function index(Request $request)
    {
        if($request->has('search')) {
            $search = $request->input('search');
            $waktuLiburs = WaktuLibur::where('nama_libur', 'like', "%{$search}%")
                ->orWhere('tanggal_mulai', 'like', "%{$search}%")
                ->orWhere('tanggal_akhir', 'like', "%{$search}%")
                ->paginate(10);
        } else if($request->sort && $request->direction) {
            $waktuLiburs = WaktuLibur::orderBy($request->sort, $request->direction)->paginate(10);
        } else {
            $waktuLiburs = WaktuLibur::paginate(10);
        }

        // return response()->json($waktuLibur);
        return view('Managment.WaktuLibur.waktuLiburs', compact('waktuLiburs'));
    }

    public function create()
    {
        // return response()->json(['message' => 'Create Waktu Libur']);
        $groups = Group::all(); // Assuming you have a Group model
        return view('Managment.WaktuLibur.create', compact('groups'));
    }

    // Store a new waktu libur
    public function store(Request $request)
    {
        // dd($request->all());
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

        $waktuLiburId = WaktuLibur::create($validated)->id;
        // Attach groups if provided
        if ($request->has('groups')) {
            foreach ($request->groups as $groupId) {
                GroupLibur::create([
                    'id_waktu_libur' => $waktuLiburId,
                    'id_group' => $groupId,
                ]);
            }
        }

        // return response()->json($waktuLibur, 201);
        return redirect()->route('waktuLibur.index')->with('success', 'Waktu Libur created successfully');
    }

    // Show a single waktu libur
    public function show($id)
    {
        $waktuLibur = WaktuLibur::with('groupLibur')->find($id);
        if (!$waktuLibur) {
            // return response()->json(['message' => 'WaktuLibur not found'], 404);
            return redirect()->route('waktuLibur.index')->withErrors(['message' => 'WaktuLibur not found']);
        }

        $groupLibur = $waktuLibur->groupLibur;// Assuming you have a relationship defined in WaktuLibur model
        $groups = $groupLibur->map(function ($item) {
            return $item->group; // Assuming you have a relationship defined in GroupLibur model
        });
        // return response()->json($groups);
        return view('Managment.WaktuLibur.show', compact('waktuLibur', 'groups'));
    }

    public function edit($id)
    {
        $waktuLibur = WaktuLibur::with('groupLibur')->find($id);
        if (!$waktuLibur) {
            // return response()->json(['message' => 'WaktuLibur not found'], 404);
            return redirect()->route('waktuLibur.index')->withErrors(['message' => 'WaktuLibur not found']);
        }

        $groupLibur = $waktuLibur->groupLibur;// Assuming you have a relationship defined in WaktuLibur model
        $groups = Group::all(); // Get all groups to show in the form

        $selectedGroups = $groupLibur->pluck('id_group')->toArray(); // Get the IDs of the groups associated with this waktu libur

        // return response()->json($selectedGroups);
        return view('Managment.WaktuLibur.edit', compact('waktuLibur', 'groups', 'selectedGroups'));
    }

    // Update a waktu libur
    public function update(Request $request, $id)
    {
        // dd($request->all());
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

        if ($request->has('groups')) {
            foreach ($request->groups as $groupId) {
                GroupLibur::create([
                    'id_waktu_libur' => $id,
                    'id_group' => $groupId,
                ]);
            }
        }

        // return response()->json($waktuLibur);
        return redirect()->route('waktuLibur.index')->with('success', 'Waktu Libur updated successfully');
    }

    // Delete a waktu libur
    public function destroy($id, $waktuLiburId)
    {
        dd($id, $waktuLiburId);
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
