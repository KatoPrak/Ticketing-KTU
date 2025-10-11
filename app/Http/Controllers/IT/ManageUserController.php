<?php

namespace App\Http\Controllers\IT;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

// Nama class diubah agar lebih sesuai
class ManageUserController extends Controller
{
    /**
     * Menampilkan daftar user
     */
    public function index(Request $request)
{
    $search = $request->input('search');

    $query = User::where('role', 'user');

    if ($search) {
        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('id_staff', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('department', 'like', "%{$search}%");
        });
    }

    // Kalau permintaan AJAX, kirim JSON saja
    if ($request->ajax()) {
        return response()->json($query->get());
    }

    $users = $query->get();
    return view('it.manage-user', compact('users'));
}


    /**
     * Menyimpan user baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'id_staff' => 'required|string|unique:users,id_staff|max:50',
            'email' => 'required|email|unique:users,email',
            // Department disesuaikan dengan data yang ada di database Anda
            'department' => 'required|in:hse,hr,finance,production,project,security,IT,Engineering' 
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'id_staff' => $validated['id_staff'],
            'email' => $validated['email'],
            'department' => $validated['department'],
            'role' => 'user', // Role sudah benar sesuai permintaan
            'password' => Hash::make('STAFFKTU123') // Password default diubah agar konsisten
        ]);

        // Mengembalikan data user yang baru dibuat
        return response()->json($user);
    }

    /**
     * Mengambil data satu user untuk diedit
     */
    public function show($id)
    {
        $user = User::findOrFail($id);
        
        return response()->json($user);
    }

    /**
     * Mengupdate data user
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'id_staff' => ['required', 'string', 'max:50', Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            // Department disesuaikan dengan data yang ada di database Anda
            'department' => 'required|in:hse,hr,finance,production,project,security,IT,Engineering'
        ]);

        $user->update($validated);
        
        // Mengembalikan data user yang baru diupdate
        return response()->json($user);
    }

    /**
     * Menghapus user
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully'
        ]);
    }
}