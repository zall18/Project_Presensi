<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Device;
use Illuminate\Support\Facades\Validator;

class DeviceController extends Controller
{
    // List all devices
    public function index()
    {
        $devices = Device::all();
        return response()->json($devices);
    }

    // Store a new device
    public function store(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'nama' => 'required|string|max:100',
            'device_id' => 'required|string|max:50|unique:devices',
            'lokasi' => 'nullable|string|max:150',
            'status' => 'required|in:active,inactive',
        ]);
        if ($validated->fails()) {
            return response()->json($validated->errors(), 422);
        }
        $validated = $validated->validated();

        $device = Device::create($validated);

        return response()->json($device, 201);
    }

    // Show a single device
    public function show($id)
    {
        $device = Device::find($id);
        if (!$device) {
            return response()->json(['message' => 'Device not found'], 404);
        }
        return response()->json($device);
    }

    // Update a device
    public function update(Request $request, $id)
    {
        $device = Device::find($id);
        if (!$device) {
            return response()->json(['message' => 'Device not found'], 404);
        }

        $validated = Validator::make($request->all(), [
            'nama' => 'sometimes|required|string|max:100',
            'device_id' => 'sometimes|required|string|max:50|unique:devices,device_id,' . $id,
            'lokasi' => 'nullable|string|max:150',
            'status' => 'sometimes|required|in:active,inactive',
        ]);
        if ($validated->fails()) {
            return response()->json($validated->errors(), 422);
        }
        $validated = $validated->validated();
        if (isset($validated['device_id'])) {
            $existingDevice = Device::where('device_id', $validated['device_id'])
                ->where('id', '!=', $id)
                ->first();
            if ($existingDevice) {
                return response()->json(['message' => 'Device ID already exists'], 422);
            }
        }

        $device->update($validated);

        return response()->json($device);
    }

    // Delete a device
    public function destroy($id)
    {
        $device = Device::find($id);
        if (!$device) {
            return response()->json(['message' => 'Device not found'], 404);
        }
        // Check if the device is in use
        if ($device->presensi()->count() > 0) {
            // If the device is in use, return an error response
            return response()->json(['message' => 'Device is in use and cannot be deleted'], 422);
        }
        $device->delete();

        return response()->json(['message' => 'Device deleted']);
    }
}
