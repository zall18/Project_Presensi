<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GroupLibur;
use Illuminate\Support\Facades\Validator;

class GroupLiburController extends Controller
{
    // List all group libur
    public function index()
    {
        $groupLiburs = GroupLibur::with(['waktuLibur', 'group'])->get();
        return response()->json($groupLiburs);
    }

    // Store a new group libur
    public function store(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'id_waktu_libur' => 'required|exists:waktu_liburs,id',
            'id_group' => 'required|exists:groups,id',
        ]);
        if ($validated->fails()) {
            return response()->json($validated->errors(), 422);
        }
        $validated = $validated->validated();
        // Check if the group libur already exists
        $existingGroupLibur = GroupLibur::where('id_waktu_libur', $validated['id_waktu_libur'])
            ->where('id_group', $validated['id_group'])
            ->first();
        if ($existingGroupLibur) {
            return response()->json(['message' => 'GroupLibur already exists'], 422);
        }

        $groupLibur = GroupLibur::create($validated);

        return response()->json($groupLibur->load(['waktuLibur', 'group']), 201);
    }

    // Show a single group libur
    public function show($id)
    {
        $groupLibur = GroupLibur::with(['waktuLibur', 'group'])->find($id);
        if (!$groupLibur) {
            return response()->json(['message' => 'GroupLibur not found'], 404);
        }
        return response()->json($groupLibur);
    }

    // Update a group libur
    public function update(Request $request, $id)
    {
        $groupLibur = GroupLibur::findOrFail($id);

        $validated = Validator::make($request->all(), [
            'id_waktu_libur' => 'sometimes|required|exists:waktu_liburs,id',
            'id_group' => 'sometimes|required|exists:groups,id',
        ]);
        if ($validated->fails()) {
            return response()->json($validated->errors(), 422);
        }
        $validated = $validated->validated();
        // Check if the group libur already exists
        if (isset($validated['id_waktu_libur']) && isset($validated['id_group'])) {
            $existingGroupLibur = GroupLibur::where('id_waktu_libur', $validated['id_waktu_libur'])
                ->where('id_group', $validated['id_group'])
                ->where('id', '!=', $id)
                ->first();
            if ($existingGroupLibur) {
                return response()->json(['message' => 'GroupLibur already exists'], 422);
            }
        }

        $groupLibur->update($validated);

        return response()->json($groupLibur->load(['waktuLibur', 'group']));
    }

    // Delete a group libur
    public function destroy($id)
    {
        $groupLibur = GroupLibur::find($id);
        if (!$groupLibur) {
            return response()->json(['message' => 'GroupLibur not found'], 404);
        }
        $groupLibur->delete();

        return response()->json(['message' => 'GroupLibur deleted']);
    }
}
