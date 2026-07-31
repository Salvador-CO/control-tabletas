<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\AssignmentItem;
use App\Models\Device;
use App\Models\Event;
use App\Models\Location;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AssignmentController extends Controller
{
    public function index()
    {
        $assignments = Assignment::with(['location', 'coordinator', 'items.device', 'items.staff'])->latest()->get();
        return view('assignments.index', compact('assignments'));
    }

    public function create()
    {
        $events           = Event::all();
        $locations        = Location::all();
        $staff            = Staff::all();
        $availableDevices = Device::available()->with('category')->get();

        return view('assignments.create', compact('events', 'locations', 'staff', 'availableDevices'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'event_id'           => 'nullable|exists:events,id',
            'location_id'        => 'required|exists:locations,id',
            'coordinator_id'     => 'required|exists:staff,id',
            'chargers_count'     => 'required|integer|min:0',
            'start_date'         => 'required|date',
            'end_date'           => 'required|date|after_or_equal:start_date',
            'devices'            => 'required|array|min:1',
            'devices.*.id'       => 'required|exists:devices,id',
            'devices.*.staff_id' => 'nullable|exists:staff,id',
        ]);

        DB::transaction(function () use ($request) {
            $assignment = Assignment::create([
                'event_id'             => $request->event_id,
                'location_id'          => $request->location_id,
                'coordinator_id'       => $request->coordinator_id,
                'delivery_person_name' => $request->delivery_person_name ?? 'MARCELA PEÑA ORDOÑEZ',
                'chargers_count'       => $request->chargers_count,
                'start_date'           => $request->start_date,
                'end_date'             => $request->end_date,
                'status'               => 'activo',
                'observations'         => $request->observations,
            ]);

            foreach ($request->devices as $deviceData) {
                AssignmentItem::create([
                    'assignment_id'  => $assignment->id,
                    'device_id'      => $deviceData['id'],
                    'staff_id'       => $deviceData['staff_id'] ?? null,
                    'has_case_strap' => true,
                    'is_returned'    => false,
                ]);

                Device::where('id', $deviceData['id'])->update(['status' => 'en_resguardo']);
            }
        });

        return redirect()->route('assignments.index')->with('success', 'Vale de Resguardo generado correctamente.');
    }

    public function show(Assignment $assignment)
    {
        $assignment->load(['event', 'location', 'coordinator', 'items.device', 'items.staff']);
        return view('assignments.show', compact('assignment'));
    }

    public function toggleLiberation(AssignmentItem $item)
    {
        DB::transaction(function () use ($item) {
            $newStatus = !$item->is_returned;
            $item->update([
                'is_returned' => $newStatus,
                'returned_at' => $newStatus ? now() : null,
            ]);

            $deviceStatus = $newStatus ? 'disponible' : 'en_resguardo';
            $item->device->update(['status' => $deviceStatus]);

            $assignment  = $item->assignment;
            $allReturned = $assignment->items()->where('is_returned', false)->count() === 0;
            $assignment->update(['status' => $allReturned ? 'completado' : 'activo']);
        });

        return response()->json([
            'success'     => true,
            'is_returned' => $item->is_returned,
            'message'     => $item->is_returned ? 'Tableta liberada.' : 'Tableta vuelta a resguardo.',
        ]);
    }

    public function downloadPdf(Assignment $assignment)
    {
        $assignment->load(['event', 'location', 'coordinator', 'items.device', 'items.staff']);

        if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.vale_resguardo', compact('assignment'));
            return $pdf->stream("Vale_Resguardo_{$assignment->location->name}.pdf");
        }

        // Fallback: render the view directly for browser printing
        return view('pdf.vale_resguardo', compact('assignment'));
    }
}