<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function index()
    {
        $locations = Location::withCount('staff')->orderBy('name')->get();
        return view('locations.index', compact('locations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:200',
            'state' => 'nullable|string|max:100',
        ]);

        Location::create($request->only('name', 'state'));

        return redirect()->back()->with('success', 'Sede registrada correctamente.');
    }

    public function update(Request $request, Location $location)
    {
        $request->validate([
            'name'  => 'required|string|max:200',
            'state' => 'nullable|string|max:100',
        ]);

        $location->update($request->only('name', 'state'));

        return redirect()->back()->with('success', 'Sede actualizada correctamente.');
    }

    public function destroy(Location $location)
    {
        $location->delete();
        return redirect()->back()->with('success', 'Sede eliminada.');
    }
}
