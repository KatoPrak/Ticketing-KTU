<?php

namespace App\Http\Controllers\IT;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Department;

class DepartmentController extends Controller
{
    // DepartmentController.php
public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|unique:departments,name',
    ]);

    $department = Department::create([
        'name' => $request->name,
    ]);

    return response()->json([
        'id' => $department->id,
        'name' => $department->name
    ]);
}

}
