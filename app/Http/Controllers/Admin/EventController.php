<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Bar;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->get('q');
        $events = Event::with('bar')
            ->when($q, fn($query) => $query->where('title', 'like', "%{$q}%"))
            ->orderBy('start_time', 'desc')
            ->paginate(20)
            ->withQueryString();

        return view('admin.events.index', [
            'events' => $events,
            'title' => 'Events List',
            'catName' => 'bar',
            'subCatName' => 'events',
            'scrollspy' => false,
            'simplePage' => false,
        ]);
    }

    public function create()
    {
        $bars = Bar::orderBy('name')->get();
        return view('admin.events.create', [
            'bars' => $bars,
            'title' => 'Add New Event',
            'catName' => 'bar',
            'subCatName' => 'events',
            'scrollspy' => false,
            'simplePage' => false,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'bar_id' => 'required|exists:bars,id',
            'title' => 'required|string|max:191',
            'type' => 'required|in:event,offer',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'start_time' => 'required|date',
            'end_time' => 'nullable|date|after_or_equal:start_time',
            'ticket_link' => 'nullable|url',
        ]);
        $data['status'] = 1;
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('events', 'public');
        }

        Event::create($data);

        return redirect()->route('admin.events.index')->with('success', 'Event created successfully.');
    }

    public function edit(Event $event)
    {
        $bars = Bar::orderBy('name')->get();
        return view('admin.events.create', [ // reuse create view
            'event' => $event,
            'bars' => $bars,
            'title' => 'Edit Event',
            'catName' => 'bar',
            'subCatName' => 'events',
            'scrollspy' => false,
            'simplePage' => false,
        ]);
    }

    public function update(Request $request, Event $event)
    {
        $data = $request->validate([
            'bar_id' => 'required|exists:bars,id',
            'title' => 'required|string|max:191',
            'type' => 'required|in:event,offer',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'start_time' => 'required|date',
            'end_time' => 'nullable|date|after_or_equal:start_time',
            'ticket_link' => 'nullable|url',
        ]);

        if ($request->hasFile('image')) {
            if ($event->image) {
                Storage::disk('public')->delete($event->image);
            }
            $data['image'] = $request->file('image')->store('events', 'public');
        }

        $event->update($data);

        return redirect()->route('admin.events.index')->with('success', 'Event updated successfully.');
    }
    public function toggleStatus($id)
    {
        $event = Event::findOrFail($id);
        $event->status = $event->status == 1 ? 2 : 1;
        $event->save();
        return response()->json([
            'success' => true,
            'status' => $event->status
        ]);
    }
    public function destroy(Event $event)
    {
        if ($event->image) {
            Storage::disk('public')->delete($event->image);
        }

        $event->delete();
        return back()->with('success', 'Event deleted successfully.');
    }
}

