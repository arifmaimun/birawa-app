<?php

namespace App\Http\Controllers;

use App\Models\DoctorShift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DoctorShiftController extends Controller
{
    public function index()
    {
        $shifts = DoctorShift::where('doctor_id', Auth::id())
            ->orderByRaw("CASE day_of_week 
                WHEN 'Monday' THEN 1 
                WHEN 'Tuesday' THEN 2 
                WHEN 'Wednesday' THEN 3 
                WHEN 'Thursday' THEN 4 
                WHEN 'Friday' THEN 5 
                WHEN 'Saturday' THEN 6 
                WHEN 'Sunday' THEN 7 
            END")
            ->orderBy('start_time')
            ->get();

        return view('shifts.index', compact('shifts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'day_of_week' => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        DoctorShift::create([
            'doctor_id' => Auth::id(),
            'day_of_week' => $request->day_of_week,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'is_active' => true,
        ]);

        return redirect()->route('shifts.index')->with('success', 'Shift added successfully.');
    }

    public function update(Request $request, DoctorShift $shift)
    {
        if ($shift->doctor_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'is_active' => 'boolean',
        ]);

        $shift->update([
            'is_active' => $request->has('is_active') ? $request->is_active : $shift->is_active,
        ]);

        return back()->with('success', 'Shift updated.');
    }

    public function destroy(DoctorShift $shift)
    {
        if ($shift->doctor_id !== Auth::id()) {
            abort(403);
        }

        $shift->delete();

        return back()->with('success', 'Shift deleted.');
    }
}
