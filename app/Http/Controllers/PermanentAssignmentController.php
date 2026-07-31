<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\PermanentAssignment;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PermanentAssignmentController extends Controller
{
    public function index()
    {
        $active   = PermanentAssignment::with(['device.category', 'staff'])
                        ->active()
                        ->orderBy('assigned_date', 'desc')
                        ->get();

        $released = PermanentAssignment::with(['device.category', 'staff'])
                        ->released()
                        ->orderBy('released_date', 'desc')
                        ->take(20)
                        ->get();

        return view('permanent.index', compact('active', 'released'));
    }

    public function create()
    {
        // Dispositivos disponibles para asignación permanente:
        // estado "disponible" o "asignado_fijo" SIN asignación permanente activa
        $availableDevices = Device::with('category')
            ->whereIn('status', ['disponible', 'asignado_fijo'])
            ->whereDoesntHave('permanentAssignments', fn($q) => $q->whereNull('released_date'))
            ->orderBy('brand')
            ->get();

        $staff = Staff::orderBy('full_name')->get();

        return view('permanent.create', compact('availableDevices', 'staff'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'device_id'     => 'required|exists:devices,id',
            'staff_id'      => 'required|exists:staff,id',
            'role'          => 'required|string|max:200',
            'assigned_date' => 'required|date',
            'notes'         => 'nullable|string',
        ]);

        // Verificar que el dispositivo no tenga ya una asignación permanente activa
        $existing = PermanentAssignment::where('device_id', $request->device_id)
                        ->whereNull('released_date')
                        ->first();

        if ($existing) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['device_id' => 'Este dispositivo ya tiene una asignación permanente activa.']);
        }

        DB::transaction(function () use ($request) {
            PermanentAssignment::create([
                'device_id'     => $request->device_id,
                'staff_id'      => $request->staff_id,
                'role'          => $request->role,
                'assigned_date' => $request->assigned_date,
                'notes'         => $request->notes,
            ]);

            // Cambiar estado del dispositivo a "asignado_fijo"
            Device::where('id', $request->device_id)
                  ->update(['status' => 'asignado_fijo']);
        });

        return redirect()->route('permanent.index')
                         ->with('success', 'Asignación permanente registrada correctamente.');
    }

    public function show(PermanentAssignment $permanent)
    {
        $permanent->load(['device.category', 'staff']);
        return view('permanent.show', compact('permanent'));
    }

    /**
     * Liberar un dispositivo de su asignación permanente
     * (cuando sale la persona o cambia de puesto)
     */
    public function release(Request $request, PermanentAssignment $permanent)
    {
        $request->validate([
            'released_date'   => 'required|date',
            'released_reason' => 'required|string|max:500',
        ]);

        DB::transaction(function () use ($request, $permanent) {
            $permanent->update([
                'released_date'   => $request->released_date,
                'released_reason' => $request->released_reason,
            ]);

            // El dispositivo vuelve a estar disponible
            $permanent->device->update(['status' => 'disponible']);
        });

        return redirect()->route('permanent.index')
                         ->with('success', 'Dispositivo liberado y disponible nuevamente.');
    }

    public function pdf(PermanentAssignment $permanent)
    {
        $permanent->load(['device.category', 'staff.location']);
        return view('permanent.pdf', compact('permanent'));
    }
}
