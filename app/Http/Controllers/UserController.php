<?php

namespace App\Http\Controllers;

use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    // Display a listing of users
    public function index(Request $request)
    {
        // $users = User::all();
        $users = User::paginate(10); // Default pagination


        if ($request->level){
            $users = User::where('level', $request->level)->paginate(10);
        } elseif ($request->search) {
            $users = User::where('name', 'like', '%' . $request->search . '%')
                ->orWhere('email', 'like', '%' . $request->search . '%')
                ->paginate(10);
        } elseif ($request->sort && $request->direction) {
            $users = User::orderBy($request->sort, $request->direction)->paginate(10);
        } else {
            $users = User::paginate(10);
        }


        // return response()->json($users);
        return view('Managment.User.users', compact('users'));
    }

    public function create()
    {
        // Assuming you want to return a view for creating a new user
        return view('Managment.User.create');
    }

    // Store a newly created user
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
            'level' => 'required|in:admin,operator',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $validated = $validator->validated();
        $validated['password'] = Hash::make($validated['password']);
        $user = User::create($validated);
        // return response()->json($user, 201);
        return redirect()->route('user.index')->with('success', 'User created successfully');
    }

    // Display the specified user
    public function show($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        return response()->json($user);
    }

    public function edit($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        // Assuming you want to return a view for editing
        return view('Managment.User.edit', compact('user'));
    }

    // Update the specified user
    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email',
            'password' => 'sometimes|required|string|min:6',
            'level' => 'sometimes|required|in:admin,operator',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $validated = $validator->validate();
        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        return response()->json($user);
    }

    // Remove the specified user
    public function destroy($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        $user->delete();

        return back()->with('success', 'User deleted successfully');
    }
}
