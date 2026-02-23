<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Region;
use Illuminate\Http\Request;

class RegionController extends Controller
{
    public function index()
    {
        $regions = Region::orderBy('name')->get();
        return view('admin.regions', compact('regions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255|unique:regions,name',
        ]);

        Region::create($request->only('name'));

        return redirect()->back()->with('success', 'Region created successfully!');
    }

    public function show($id)
    {
        return response()->json(Region::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $region = Region::findOrFail($id);

        $request->validate([
            'name'  => 'required|string|max:255|unique:regions,name,' . $region->id,
        ]);

        $region->update($request->only('name'));

        return redirect()->back()->with('success', 'Region updated successfully!');
    }

    public function destroy($id)
    {
        $region = Region::findOrFail($id);

        // Check if any locations use this region
        if ($region->locations()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete region that has locations assigned to it.',
            ]);
        }

        $region->delete();

        return response()->json([
            'success' => true,
            'message' => 'Region deleted successfully!',
        ]);
    }
}
