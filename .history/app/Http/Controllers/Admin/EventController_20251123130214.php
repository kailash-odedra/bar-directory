<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Bar;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->get('q');
        $events = Event::with('bar')
            ->when($q, fn($b) => $b->where('title','like', "%{$q}%"))
            ->orderBy('start_date','desc')
            ->paginate(25)->withQueryString();

        return view('admin.events.index', compact('events'))->with('catName','bar');
    }

    public function create()
    {
        $bars = Bar::orderBy('name')->get();
        return view('admin.events.create', compact('bars'))->with('catName','bar');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'bar_id' => 'required|exists:bars,id',
            'title' => 'required|string|max:191',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'ticket_url' => 'nullable|url',
        ]);

        Event::create($data);

        return redirect(url('admin/events'))->with('success','Event created.');
    }

    public function edit(Event $event)
    {
        $bars = Bar::orderBy('name')->get();
        return view('admin.events.edit', compact('event','bars'))->with('catName','bar');
    }

    public function update(Request $request, Event $event)
    {
        $data = $request->validate([
            'bar_id' => 'required|exists:bars,id',
            'title' => 'required|string|max:191',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'ticket_url' => 'nullable|url',
        ]);

        $event->update($data);

        return redirect(url('admin/events'))->with('success','Event updated.');
    }

    public function destroy(Event $event)
    {
        $event->delete();
        return redirect(url('admin/events'))->with('success','Event deleted.');
    }
}
