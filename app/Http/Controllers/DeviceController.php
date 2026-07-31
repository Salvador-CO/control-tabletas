<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Category;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    public function index(Request $request)
    {
        $query = Device::with('category');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('serial_number', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status') && $request->status !== 'Todos') {
            $query->where('status', $request->status);
        }

        $devices    = $query->paginate(20);
        $categories = Category::all();

        return view('devices.index', compact('devices', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id'     => 'required|exists:categories,id',
            'brand'           => 'required|string|max:100',
            'model'           => 'required|string|max:100',
            'serial_number'   => 'required|string|unique:devices,serial_number',
            'charger_details' => 'nullable|string|max:255',
            'notes'           => 'nullable|string',
        ]);

        Device::create($validated);

        return redirect()->back()->with('success', 'Dispositivo registrado exitosamente en inventario.');
    }

    public function update(Request $request, Device $device)
    {
        $validated = $request->validate([
            'brand'           => 'required|string|max:100',
            'model'           => 'required|string|max:100',
            'serial_number'   => 'required|string|unique:devices,serial_number,' . $device->id,
            'status'          => 'required|in:disponible,en_resguardo,asignado_fijo,mantenimiento',
            'charger_details' => 'nullable|string|max:255',
            'notes'           => 'nullable|string',
        ]);

        $device->update($validated);

        return redirect()->back()->with('success', 'Dispositivo actualizado correctamente.');
    }
}