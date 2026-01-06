<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\HR\Holiday;
use Illuminate\Http\JsonResponse;

class HolidaysCalendarController extends Controller
{
    /**
     * Get holidays as JSON for FullCalendar
     */
    public function getHolidaysJson(): JsonResponse
    {
        $holidays = Holiday::all();

        $events = $holidays->map(function ($holiday) {
            return [
                'id' => $holiday->id,
                'title' => $holiday->name,
                'start' => $holiday->start_date->format('Y-m-d'),
                'end' => $holiday->end_date->copy()->addDay()->format('Y-m-d'), // FullCalendar exclusive end
                'description' => $holiday->description,
                'days_count' => $holiday->days_count,
            ];
        });

        return response()->json($events->values());
    }
}

