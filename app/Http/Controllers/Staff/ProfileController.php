<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Department;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $departments = Department::all();
        
        // Get locations based on user's region
        $locations = Location::query();
        if ($user->region_id) {
            $locations->where('region_id', $user->region_id);
        }
        $locations = $locations->orderBy('name')->get();
        
        return view('staff.profile', compact('user', 'departments', 'locations'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'email'            => ['nullable', 'email'],
            'department_id'    => 'required|exists:departments,id',
            'location_id'      => 'nullable|exists:locations,id',
            'current_password' => 'nullable|required_with:new_password',
            'new_password'     => 'nullable|min:6|confirmed',
        ]);

        // Update basic info
        $user->name          = $validated['name'];
        $user->email         = $validated['email'] ?? null;
        $user->department_id = $validated['department_id'];
        $user->location_id   = $validated['location_id'] ?? null;

        // Update password if provided
        if ($request->filled('current_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Current password is incorrect']);
            }
            
            $user->password = Hash::make($validated['new_password']);
        }

        $user->save();

        return redirect()->route('staff.profile')->with('success', 'Profile updated successfully!');
    }
}
