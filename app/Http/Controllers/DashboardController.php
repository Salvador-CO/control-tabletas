<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Assignment;
use App\Models\AssignmentItem;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalDevices = Device::count();
        $availableDevices = Device::where('status', 'disponible')->count();
        $inUseDevices = Device::whereIn('status', ['en_resguardo', 'en_uso'])->count();
        
        $pendingReturns = AssignmentItem::where('is_returned', false)->count();
        $recentAssignments = Assignment::with(['location', 'coordinator', 'items.device'])->latest()->take(10)->get();

        return view('dashboard', compact(
            'totalDevices',
            'availableDevices',
            'inUseDevices',
            'pendingReturns',
            'recentAssignments'
        ));
    }
}