<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Setting;

class FinanceCycleService
{
    /**
     * Get the current financial cycle dates and statistics
     */
    public static function getCurrentCycle(int $monthOffset = 0)
    {
        // Get settings or defaults
        $cycleStartDate = (int) self::getSetting('cycle_start_date', 25);
        $cycleEndDate = self::getSetting('cycle_end_date', null);
        $monthlyBudget = (float) self::getSetting('monthly_budget', 5000000);

        $today = Carbon::today();
        
        // Add month offset to today
        if ($monthOffset !== 0) {
            $today->addMonthsNoOverflow($monthOffset);
        }

        $currentDay = $today->day;

        // Determine start date base month
        if ($currentDay >= $cycleStartDate) {
            // We are in the current month's cycle
            $startDate = Carbon::create($today->year, $today->month, $cycleStartDate)->startOfDay();
        } else {
            // We are in the previous month's cycle
            $startDate = Carbon::create($today->year, $today->month, $cycleStartDate)->subMonthNoOverflow()->startOfDay();
        }

        // Determine end date
        if ($cycleEndDate) {
            $cycleEndDate = (int) $cycleEndDate;
            $endDate = Carbon::create($startDate->year, $startDate->month, $cycleEndDate);
            // If end date is less than start date (e.g., Start: 25, End: 20), it crosses to next month
            if ($cycleEndDate < $cycleStartDate) {
                $endDate->addMonthNoOverflow();
            }
            $endDate->endOfDay();
        } else {
            // Default logic: End is 1 day before the next Start Date
            $endDate = $startDate->copy()->addMonthNoOverflow()->subDay()->endOfDay();
        }

        // Calculate days
        $totalDays = $startDate->diffInDays($endDate) + 1;
        $passedDays = $startDate->diffInDays($today); // Not including today if we want "remaining days" to include today
        $remainingDays = $totalDays - $passedDays;
        
        if ($remainingDays <= 0) {
            $remainingDays = 1; // Prevent division by zero
        }

        return [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'total_days' => $totalDays,
            'passed_days' => $passedDays,
            'remaining_days' => $remainingDays,
            'monthly_budget' => $monthlyBudget,
            'cycle_start_date' => $cycleStartDate
        ];
    }

    public static function getSetting($key, $default = null)
    {
        $setting = Setting::where('key', $key)->first();
        
        if ($setting) {
            return $setting->value;
        }

        // Fallback to .env
        if ($key === 'cycle_start_date') {
            return env('CYCLE_START_DATE', $default);
        }
        if ($key === 'monthly_budget') {
            return env('MONTHLY_BUDGET', $default);
        }

        return $default;
    }

    public static function setSetting($key, $value)
    {
        Setting::updateOrCreate(['key' => $key], ['value' => $value]);
    }
}
