<?php

namespace App\Http\Controllers\IT;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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
                  ->orWhere('id_staff', 'like', "%{$search}%")
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
                    'id_staff' => $user->id_staff,
                    'email' => $user->email,
                    'department_id' => $user->department_id,
                    'department_name' => $user->department->name ?? '-',
                ];
            });
            return response()->json($users);
        }

        $departments = Department::all();
        return view('it.manage-user', compact('users', 'departments'));
    }

    // Menyimpan user baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'id_staff' => 'required|string|unique:users,id_staff|max:50',
            'email' => 'required|email|unique:users,email',
            'department_id' => 'required|exists:departments,id',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'id_staff' => $validated['id_staff'],
            'email' => $validated['email'],
            'department_id' => $validated['department_id'],
            'role' => 'user',
            'password' => Hash::make('STAFFKTU123') // ✅ Default password di-hash
        ]);

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'id_staff' => $user->id_staff,
            'email' => $user->email,
            'department_id' => $user->department_id,
            'department_name' => $user->department->name ?? '-',
        ]);
    }

    // Mengambil data user untuk diedit
    public function show($id)
    {
        $user = User::with('department')->findOrFail($id);

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'id_staff' => $user->id_staff,
            'email' => $user->email,
            'department_id' => $user->department_id,
            'department_name' => $user->department->name ?? '-',
        ]);
    }

    // Mengupdate data user
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'id_staff' => ['required', 'string', 'max:50', Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'department_id' => 'required|exists:departments,id',
            'password' => 'nullable|string|min:6', // ✅ Password optional
        ]);

        // ✅ Siapkan data untuk update
        $updateData = [
            'name' => $validated['name'],
            'id_staff' => $validated['id_staff'],
            'email' => $validated['email'],
            'department_id' => $validated['department_id'],
        ];

        // ✅ Hanya update password jika diisi
        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);
        $user->load('department');

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'id_staff' => $user->id_staff,
            'email' => $user->email,
            'department_id' => $user->department_id,
            'department_name' => $user->department->name ?? '-',
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