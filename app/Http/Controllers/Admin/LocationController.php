<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function index()
    {
        $locations = \App\Models\Location::orderBy('name')->get();
        return view('admin.locations', compact('locations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:locations,name',
            'description' => 'nullable|string',
        ]);

        \App\Models\Location::create($request->all());

        return redirect()->back()->with('success', 'Location created successfully!');
    }

    public function update(Request $request, $id)
    {
        $location = \App\Models\Location::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:locations,name,' . $location->id,
            'description' => 'nullable|string',
        ]);

        $location->update($request->all());

        return redirect()->back()->with('success', 'Location updated successfully!');
    }

    public function destroy($id)
    {
        $location = \App\Models\Location::findOrFail($id);
        
        if ($location->users()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete location that is assigned to users.'
            ]);
        }

        $location->delete();

        return response()->json([
            'success' => true,
            'message' => 'Location deleted successfully!'
        ]);
    }
    
    public function show($id)
    {
        $location = \App\Models\Location::findOrFail($id);
        return response()->json($location);
    }
}
