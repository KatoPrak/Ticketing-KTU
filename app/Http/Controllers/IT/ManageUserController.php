<?php

namespace App\Http\Controllers\IT;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ManageUserController extends Controller
{
    // Menampilkan daftar user
    public function index(Request $request)
    {
        $search = $request->input('search');
        $query = User::where('role', 'user')->with('department');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->get();

        // Jika AJAX request untuk search
        if ($request->ajax()) {
            $users = $users->map(function($user){
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'nik' => $user->nik,
                    'username' => $user->username,
                    'email' => $user->email,
                    'department_id' => $user->department_id,
                    'department_name' => $user->department->name ?? '-',
                    'location_id' => $user->location_id,
                    'location_name' => $user->location->name ?? '-',
                ];
            });
            return response()->json($users);
        }

        $departments = Department::all();
        
        // ✅ Filter locations by IT user's region
        $currentUser = auth()->user();
        $locations = \App\Models\Location::query();
        
        if ($currentUser->region_id) {
            $locations->where('region_id', $currentUser->region_id);
        }
        
        $locations = $locations->orderBy('name')->get();
        
        return view('it.manage-user', compact('users', 'departments', 'locations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'nik' => 'nullable|string|unique:users,nik|max:50',
            'username' => 'required|string|unique:users,username|max:50',
            'email' => 'required|email|unique:users,email',
            'department_id' => 'required|exists:departments,id',
            'location_id' => 'required|exists:locations,id',
        ]);

        $user = User::create([
            'name'          => $validated['name'],
            'nik'           => $validated['nik'] ? Str::lower($validated['nik']) : null,
            'username'      => isset($validated['username']) ? Str::lower($validated['username']) : null,
            'email'         => $validated['email'],
            'department_id' => $validated['department_id'],
            'location_id'   => $validated['location_id'],
            'role'          => 'user',
            'password'      => Hash::make('STAFFKTU123')
        ]);

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'nik' => $user->nik,
            'username' => $user->username,
            'email' => $user->email,
            'department_id' => $user->department_id,
            'department_name' => $user->department->name ?? '-',
            'location_id' => $user->location_id,
            'location_name' => $user->location->name ?? '-',
        ]);
    }

    // Mengambil data user untuk diedit
    public function show($id)
    {
        $user = User::with('department', 'location')->findOrFail($id);

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'nik' => $user->nik,
            'username' => $user->username,
            'email' => $user->email,
            'department_id' => $user->department_id,
            'department_name' => $user->department->name ?? '-',
            'location_id' => $user->location_id,
            'location_name' => $user->location->name ?? '-',
        ]);
    }

    // Mengupdate data user
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'nik' => ['nullable', 'string', 'max:50', Rule::unique('users')->ignore($user->id)],
            'username' => ['required', 'string', 'max:50', Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'department_id' => 'required|exists:departments,id',
            'location_id' => 'required|exists:locations,id',
            'password' => 'nullable|string|min:6', // ✅ Password optional
        ]);

        // ✅ Siapkan data untuk update
        $updateData = [
            'name'          => $validated['name'],
            'nik'           => $validated['nik'] ? Str::lower($validated['nik']) : null,
            'username'      => isset($validated['username']) ? Str::lower($validated['username']) : null,
            'email'         => $validated['email'],
            'department_id' => $validated['department_id'],
            'location_id'   => $validated['location_id'],
        ];

        // ✅ Hanya update password jika diisi
        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);
        $user->load('department', 'location');

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'nik' => $user->nik,
            'username' => $user->username,
            'email' => $user->email,
            'department_id' => $user->department_id,
            'department_name' => $user->department->name ?? '-',
            'location_id' => $user->location_id,
            'location_name' => $user->location->name ?? '-',
        ]);
    }

    // Menghapus user
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        // ✅ Cek apakah user punya tiket
        if ($user->tickets()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete user with existing tickets.'
            ], 400);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully'
        ]);
    }
}