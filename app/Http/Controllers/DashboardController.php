<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Assignment;
use App\Models\AssignmentItem;
use App\Models\PermanentAssignment;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalDevices        = Device::count();
        $availableDevices    = Device::where('status', 'disponible')->count();
        $inUseDevices        = Device::whereIn('status', ['en_resguardo', 'en_uso'])->count();
        $fixedDevices        = PermanentAssignment::whereNull('released_date')->count();
        $pendingReturns      = AssignmentItem::where('is_returned', false)->count();

        $recentAssignments   = Assignment::with(['location', 'coordinator', 'items.device'])
                                         ->latest()
                                         ->take(10)
                                         ->get();

        // Asignaciones permanentes activas para el panel rápido
        $permanentActive     = PermanentAssignment::with(['device.category', 'staff'])
                                                   ->whereNull('released_date')
                                                   ->latest()
                                                   ->take(5)
                                                   ->get();

        return view('dashboard', compact(
            'totalDevices',
            'availableDevices',
            'inUseDevices',
            'fixedDevices',
            'pendingReturns',
            'recentAssignments',
            'permanentActive'
        ));
    }
}