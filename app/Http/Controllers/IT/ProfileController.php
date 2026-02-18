<?php

namespace App\Http\Controllers\IT;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Department;
use App\Models\Location;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $departments = Department::all();
        $regions = Region::all();
        
        // Get locations based on user's region
        $locations = Location::query();
        if ($user->region_id) {
            $locations->where('region_id', $user->region_id);
        }
        $locations = $locations->orderBy('name')->get();
        
        return view('it.profile', compact('user', 'departments', 'locations', 'regions'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'email'            => ['nullable', 'email', Rule::unique('users')->ignore($user->id)],
            'region_id'        => 'nullable|exists:regions,id',
            'current_password' => 'nullable|required_with:new_password',
            'new_password'     => 'nullable|min:6|confirmed',
        ]);

        // Update basic info
        $user->name  = $validated['name'];
        $user->email = $validated['email'] ?? null;

        
        if ($request->filled('region_id')) {
            $user->region_id = $validated['region_id'];
        }

        // Update password if provided
        if ($request->filled('current_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Current password is incorrect']);
            }
            
            $user->password = Hash::make($validated['new_password']);
        }

        $user->save();

        return redirect()->route('it.profile')->with('success', 'Profile updated successfully!');
    }
}
