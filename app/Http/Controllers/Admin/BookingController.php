<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Bar;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Mail;
use App\Mail\BookingStatusChanged; // mailable we'll create

class BookingController extends Controller
{
    // List / filter
    public function index(Request $request)
    {
        $q = $request->get('q');
        $barId = $request->get('bar_id');
        $status = $request->get('status');
        $date = $request->get('date');

        $bookings = Booking::with('bar','user')
            ->when($q, fn($qry) => $qry->where(function($s) use ($q) {
                $s->where('customer_name','like',"%{$q}%")
                  ->orWhere('customer_phone','like',"%{$q}%")
                  ->orWhere('customer_email','like',"%{$q}%");
            }))
            ->when($barId, fn($qry) => $qry->where('bar_id', $barId))
            ->when($status, fn($qry) => $qry->where('status', $status))
            ->when($date, fn($qry) => $qry->where('booking_date', $date))
            ->orderBy('booking_date','desc')
            ->orderBy('booking_time','desc')
            ->paginate(25)
            ->withQueryString();

        $bars = Bar::orderBy('name')->get();

        return view('admin.bookings.index', compact('bookings','bars'))
            ->with(['title'=>'Bookings','catName'=>'bar','subCatName'=>'bookings','scrollspy' => false,
            'simplePage' => false]);
    }

    // show create form
    public function create()
    {
        $bars = Bar::orderBy('name')->get();
        return view('admin.bookings.create', compact('bars'))
            ->with(['title'=>'Create Booking','catName'=>'bar','subCatName'=>'bookings','scrollspy' => false,
            'simplePage' => false]);
    }

    // store booking (admin create)
    public function store(Request $request)
    {
        $data = $request->validate([
            'bar_id' => ['required','exists:bars,id'],
            'user_id' => ['nullable','exists:users,id'],
            'customer_name' => ['required','string','max:191'],
            'customer_email' => ['nullable','email','max:191'],
            'customer_phone' => ['nullable','string','max:30'],
            'people_count' => ['required','integer','min:1','max:100'],
            'booking_date' => ['required','date'],
            'booking_time' => ['required','date_format:H:i'],
            'duration_minutes' => ['nullable','numeric','min:15','max:1440'],
            'table_area' => ['nullable','string','max:191'],
            'special_request' => ['nullable','string'],
            'price' => ['nullable','numeric','min:0'],
            'status' => ['nullable', Rule::in(['pending','confirmed','cancelled','completed'])],
        ]);

        // ---- FIX START ----
        // Force duration to integer + provide safe default
        $duration = (int)($data['duration_minutes'] ?? 120);

        // if user submitted empty string "" → convert to default
        if ($duration < 1) {
            $duration = 120;
        }

        $data['duration_minutes'] = $duration;
        // ---- FIX END ----

        // Create Carbon instance
        $starts = Carbon::createFromFormat(
            'Y-m-d H:i',
            $data['booking_date'].' '.$data['booking_time'],
            config('app.timezone')
        );

        // Calculate ends_at safely
        $data['ends_at'] = $starts->copy()->addMinutes($data['duration_minutes']);

        $data['created_by_admin'] = true;
        $data['status'] = $data['status'] ?? 'pending';

        // Availability check
        if (! $this->isAvailable($data['bar_id'], $starts, $data['ends_at'], $data['table_area'])) {
            return back()->withInput()->withErrors(['booking_time' => 'Selected slot is not available.']);
        }

        $booking = Booking::create($data);

        // Email
        if (!empty($data['customer_email'])) {
            try {
                Mail::to($data['customer_email'])
                    ->send(new BookingStatusChanged($booking, 'created'));
            } catch (\Throwable $e) {}
        }

        return redirect()->route('admin.bookings.index')
            ->with('success', 'Booking created.');
    }



    // ================= EDIT ==================

    public function edit(Booking $booking)
    {
        $bars = Bar::orderBy('name')->get();
        return view('admin.bookings.create', compact('booking','bars'))
            ->with([
                'title' => 'Edit Booking',
                'catName' => 'bar',
                'subCatName' => 'bookings',
                'scrollspy' => false,
                'simplePage' => false,
            ]);
    }



    // ================= UPDATE ==================

    public function update(Request $request, Booking $booking)
    {
        $data = $request->validate([
            'bar_id' => ['required','exists:bars,id'],
            'user_id' => ['nullable','exists:users,id'],
            'customer_name' => ['required','string','max:191'],
            'customer_email' => ['nullable','email','max:191'],
            'customer_phone' => ['nullable','string','max:30'],
            'people_count' => ['required','integer','min:1','max:100'],
            'booking_date' => ['required','date'],
            'booking_time' => ['required','date_format:H:i'],
            'duration_minutes' => ['nullable','numeric','min:15','max:1440'],
            'table_area' => ['nullable','string','max:191'],
            'special_request' => ['nullable','string'],
            'price' => ['nullable','numeric','min:0'],
            'status' => ['nullable', Rule::in(['pending','confirmed','cancelled','completed'])],
        ]);

        // ---- FIX START ----
        $duration = (int)($data['duration_minutes'] ?? 120);
        if ($duration < 1) {
            $duration = 120;
        }
        $data['duration_minutes'] = $duration;
        // ---- FIX END ----

        $starts = Carbon::createFromFormat(
            'Y-m-d H:i',
            $data['booking_date'].' '.$data['booking_time'],
            config('app.timezone')
        );

        $data['ends_at'] = $starts->copy()->addMinutes($data['duration_minutes']);

        // Availability check excluding current booking ID
        if (! $this->isAvailable(
            $data['bar_id'],
            $starts,
            $data['ends_at'],
            $data['table_area'],
            $booking->id
        )) {
            return back()->withInput()->withErrors(['booking_time' => 'Selected slot is not available.']);
        }

        $booking->update($data);

        if (!empty($data['customer_email'])) {
            try {
                Mail::to($data['customer_email'])
                    ->send(new BookingStatusChanged($booking, 'updated'));
            } catch (\Throwable $e) {}
        }

        return redirect()->route('admin.bookings.index')
            ->with('success', 'Booking updated.');
    }


    // delete
    public function destroy(Booking $booking)
    {
        $booking->delete();
        return back()->with('success', 'Booking deleted.');
    }

    // quick status change via AJAX
    public function toggleStatus(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);
        $new = $request->get('status'); // expected: pending|confirmed|cancelled|completed
        if (! in_array($new, ['pending','confirmed','cancelled','completed'])) {
            return response()->json(['success'=>false,'message'=>'Invalid status'], 422);
        }
        $booking->status = $new;
        $booking->save();

        // send notification
        if ($booking->customer_email) {
            try { Mail::to($booking->customer_email)->send(new BookingStatusChanged($booking, 'status_changed')); } catch(\Throwable $e){}
        }

        return response()->json(['success'=>true,'status'=>$booking->status]);
    }

    /**
     * Basic availability check.
     * - Checks for overlapping bookings on same bar & same table_area (if provided)
     * - Excludes $excludeBookingId if provided
     */
    protected function isAvailable(int $barId, \Carbon\Carbon $start, \Carbon\Carbon $end, ?string $tableArea = null, ?int $excludeBookingId = null): bool
    {
        $query = Booking::where('bar_id', $barId)
            ->where(function($q) use ($start, $end){
                // overlap logic: (start < existing_end) AND (end > existing_start)
                $q->where(function($s) use ($start, $end){
                    $s->where('booking_date', $start->toDateString())
                      ->whereRaw('? < ends_at', [$start->toDateTimeString()])
                      ->whereRaw('? > CONCAT(booking_date, " ", booking_time)', [$end->toDateTimeString()]);
                });
            });

        if ($tableArea) {
            $query->where('table_area', $tableArea);
        }

        if ($excludeBookingId) {
            $query->where('id', '!=', $excludeBookingId);
        }

        // ignore cancelled bookings
        $query->where('status','!=','cancelled');

        return ! $query->exists();
    }
}
