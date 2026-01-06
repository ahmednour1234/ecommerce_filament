<?php

namespace App\Helpers;

use Carbon\Carbon;

class LeaveHelper
{
    /**
     * Calculate total days between two dates (inclusive)
     */
    public static function calculateTotalDays(Carbon $startDate, Carbon $endDate): int
    {
        if ($endDate->lt($startDate)) {
            throw new \InvalidArgumentException('End date must be after or equal to start date.');
        }
        
        return $startDate->diffInDays($endDate) + 1;
    }

    /**
     * Calculate total days excluding weekends
     */
    public static function calculateWorkingDays(Carbon $startDate, Carbon $endDate): int
    {
        if ($endDate->lt($startDate)) {
            throw new \InvalidArgumentException('End date must be after or equal to start date.');
        }
        
        $totalDays = 0;
        $currentDate = $startDate->copy();
        
        while ($currentDate->lte($endDate)) {
            // Skip weekends (Saturday = 6, Sunday = 0)
            if ($currentDate->dayOfWeek !== Carbon::SATURDAY && $currentDate->dayOfWeek !== Carbon::SUNDAY) {
                $totalDays++;
            }
            $currentDate->addDay();
        }
        
        return $totalDays;
    }

    /**
     * Check if two date ranges overlap
     */
    public static function datesOverlap(
        Carbon $start1,
        Carbon $end1,
        Carbon $start2,
        Carbon $end2
    ): bool {
        return $start1->lte($end2) && $start2->lte($end1);
    }

    /**
     * Format date range for display
     */
    public static function formatDateRange(Carbon $startDate, Carbon $endDate, string $locale = 'en'): string
    {
        if ($startDate->isSameDay($endDate)) {
            return $startDate->format('Y-m-d');
        }
        
        return $startDate->format('Y-m-d') . ' - ' . $endDate->format('Y-m-d');
    }

    /**
     * Get fiscal year for a date
     */
    public static function getFiscalYear(Carbon $date): int
    {
        // Default to calendar year, can be customized based on fiscal year settings
        return $date->year;
    }
}

