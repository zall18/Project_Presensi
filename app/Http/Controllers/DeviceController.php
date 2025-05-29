<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Device;
use Illuminate\Support\Facades\Validator;

class DeviceController extends Controller
{
    // List all devices
    public function index(Request $request)
    {
        // Check if there are any filters or search criteria
        if ($request->has('search')) {
            $search = $request->input('search');
            $devices = Device::where('nama', 'like', "%{$search}%")
                ->orWhere('device_id', 'like', "%{$search}%")
                ->orWhere('lokasi', 'like', "%{$search}%")
                ->paginate(10);
        } elseif ($request->sort && $request->direction) {
            $devices = Device::orderBy($request->sort, $request->direction)->paginate(10);
        } else {
            $devices = Device::paginate(10); // Default pagination
        }

        // return response()->json($devices);
        return view('Managment.Device.devices', compact('devices'));
    }

    public function create()
    {
        // return response()->json(['message' => 'Create Device']);
        return view('Managment.Device.create');
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
            // return response()->json($validated->errors(), 422);
            return back()->withErrors($validated)->withInput();
        }
        $validated = $validated->validated();

        $device = Device::create($validated);

        // return response()->json($device, 201);
        return redirect()->route('device.index')->with('success', 'Device created successfully');
    }

    // Show a single device
    public function show($id)
    {
        $device = Device::find($id);
        if (!$device) {
            // return response()->json(['message' => 'Device not found'], 404);
            return redirect()->route('device.index')->withErrors(['message' => 'Device not found']);
        }
        // return response()->json($device);
        return view('Managment.Device.show', compact('device'));
    }

    public function edit($id)
    {
        $device = Device::find($id);
        if (!$device) {
            // return response()->json(['message' => 'Device not found'], 404);
            return redirect()->route('device.index')->withErrors(['message' => 'Device not found']);
        }
        // return response()->json($device);
        return view('Managment.Device.edit', compact('device'));
    }

    // Update a device
    public function update(Request $request, $id)
    {
        $device = Device::find($id);
        if (!$device) {
            // return response()->json(['message' => 'Device not found'], 404);
            return redirect()->route('device.index')->withErrors(['message' => 'Device not found']);
        }

        $validated = Validator::make($request->all(), [
            'nama' => 'sometimes|required|string|max:100',
            'device_id' => 'sometimes|required|string|max:50|unique:devices,device_id,' . $id,
            'lokasi' => 'nullable|string|max:150',
            'status' => 'sometimes|required|in:active,inactive',
        ]);
        if ($validated->fails()) {
            // return response()->json($validated->errors(), 422);
            return back()->withErrors($validated)->withInput();
        }
        $validated = $validated->validated();
        if (isset($validated['device_id'])) {
            $existingDevice = Device::where('device_id', $validated['device_id'])
                ->where('id', '!=', $id)
                ->first();
            if ($existingDevice) {
                // return response()->json(['message' => 'Device ID already exists'], 422);
                return back()->withErrors(['message' => 'Device ID already exists'])->withInput();
            }
        }

        $device->update($validated);

        // return response()->json($device);
        return redirect()->route('device.index')->with('success', 'Device updated successfully');
    }

    // Delete a device
    public function destroy($id)
    {
        $device = Device::find($id);
        if (!$device) {
            // return response()->json(['message' => 'Device not found'], 404);
            return redirect()->route('device.index')->withErrors(['message' => 'Device not found']);
        }
        // Check if the device is in use
        if ($device->presensi()->count() > 0) {
            // If the device is in use, return an error response
            // return response()->json(['message' => 'Device is in use and cannot be deleted'], 422);
            return redirect()->route('device.index')->withErrors(['message' => 'Device is in use and cannot be deleted']);
        }
        $device->delete();

        // return response()->json(['message' => 'Device deleted']);
        return redirect()->route('device.index')->with('success', 'Device deleted successfully');
    }
}
