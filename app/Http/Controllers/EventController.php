<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::withCount('assignments')->orderBy('start_date', 'desc')->get();
        return view('events.index', compact('events'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:200',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
        ]);

        Event::create($request->only('name', 'start_date', 'end_date'));

        return redirect()->back()->with('success', 'Evento registrado correctamente.');
    }

    public function update(Request $request, Event $event)
    {
        $request->validate([
            'name'       => 'required|string|max:200',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
        ]);

        $event->update($request->only('name', 'start_date', 'end_date'));

        return redirect()->back()->with('success', 'Evento actualizado correctamente.');
    }

    public function destroy(Event $event)
    {
        $event->delete();
        return redirect()->back()->with('success', 'Evento eliminado.');
    }
}
