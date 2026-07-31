<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\Location;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    public function index()
    {
        $staff     = Staff::with('location')->orderBy('full_name')->get();
        $locations = Location::orderBy('name')->get();
        return view('staff.index', compact('staff', 'locations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'full_name'   => 'required|string|max:200',
            'role'        => 'required|string|max:100',
            'location_id' => 'nullable|exists:locations,id',
        ]);

        Staff::create($request->only('full_name', 'role', 'location_id'));

        return redirect()->back()->with('success', 'Personal registrado correctamente.');
    }

    public function update(Request $request, Staff $staff)
    {
        $request->validate([
            'full_name'   => 'required|string|max:200',
            'role'        => 'required|string|max:100',
            'location_id' => 'nullable|exists:locations,id',
        ]);

        $staff->update($request->only('full_name', 'role', 'location_id'));

        return redirect()->back()->with('success', 'Personal actualizado correctamente.');
    }

    public function destroy(Staff $staff)
    {
        $staff->delete();
        return redirect()->back()->with('success', 'Personal eliminado.');
    }
}
