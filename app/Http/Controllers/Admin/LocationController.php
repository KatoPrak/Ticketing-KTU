<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function index()
    {
        $locations = \App\Models\Location::with('region')->orderBy('name')->get();
        $regions = \App\Models\Region::all();
        return view('admin.locations', compact('locations', 'regions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:locations,name',
            'region_id' => 'required|exists:regions,id',
        ]);

        \App\Models\Location::create($request->only('name', 'region_id'));

        return redirect()->back()->with('success', 'Location created successfully!');
    }

    public function update(Request $request, $id)
    {
        $location = \App\Models\Location::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:locations,name,' . $location->id,
            'region_id' => 'required|exists:regions,id',
        ]);

        $location->update($request->only('name', 'region_id'));

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
