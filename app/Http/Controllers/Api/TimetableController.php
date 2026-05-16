<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Timetable;
use Illuminate\Http\Request;

class TimetableController extends Controller
{
    /**
     * Get all timetable entries for the authenticated student.
     * GET /api/timetable
     */
    public function index(Request $request)
    {
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

        $entries = Timetable::where('student_id', $request->user()->id)
            ->orderByRaw("FIELD(day, 'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday')")
            ->orderBy('start_time')
            ->get();

        // Group by day for easier frontend rendering
        $grouped = [];
        foreach ($days as $day) {
            $grouped[$day] = $entries->where('day', $day)->values();
        }

        return response()->json([
            'timetable' => $grouped,
        ]);
    }

    /**
     * Get timetable for a specific day.
     * GET /api/timetable/{day}
     */
    public function byDay(Request $request, string $day)
    {
        $validDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        $day = ucfirst(strtolower($day));

        if (! in_array($day, $validDays)) {
            return response()->json(['message' => 'Invalid day provided'], 422);
        }

        $entries = Timetable::where('student_id', $request->user()->id)
            ->where('day', $day)
            ->orderBy('start_time')
            ->get();

        return response()->json([
            'day'     => $day,
            'classes' => $entries,
        ]);
    }

    /**
     * Add a new timetable entry (admin/student self-add).
     * POST /api/timetable
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject'    => 'required|string|max:255',
            'lecturer'   => 'required|string|max:255',
            'room'       => 'required|string|max:100',
            'day'        => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday',
            'start_time' => 'required|date_format:H:i',
            'end_time'   => 'required|date_format:H:i|after:start_time',
        ]);

        $entry = Timetable::create([
            ...$validated,
            'student_id' => $request->user()->id,
        ]);

        return response()->json([
            'message' => 'Timetable entry added',
            'entry'   => $entry,
        ], 201);
    }

    /**
     * Update a timetable entry.
     * PUT /api/timetable/{id}
     */
    public function update(Request $request, int $id)
    {
        $entry = Timetable::where('id', $id)
            ->where('student_id', $request->user()->id)
            ->firstOrFail();

        $validated = $request->validate([
            'subject'    => 'sometimes|string|max:255',
            'lecturer'   => 'sometimes|string|max:255',
            'room'       => 'sometimes|string|max:100',
            'day'        => 'sometimes|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday',
            'start_time' => 'sometimes|date_format:H:i',
            'end_time'   => 'sometimes|date_format:H:i',
        ]);

        $entry->update($validated);

        return response()->json([
            'message' => 'Timetable entry updated',
            'entry'   => $entry->fresh(),
        ]);
    }

    /**
     * Delete a timetable entry.
     * DELETE /api/timetable/{id}
     */
    public function destroy(Request $request, int $id)
    {
        $entry = Timetable::where('id', $id)
            ->where('student_id', $request->user()->id)
            ->firstOrFail();

        $entry->delete();

        return response()->json(['message' => 'Timetable entry deleted']);
    }
}
