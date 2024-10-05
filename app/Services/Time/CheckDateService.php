<?php

namespace App\Services\Time;

use DateTime;
use Exception;
use Carbon\Carbon;

class CheckDateService
{
    public static function is_today($date)
    {
        return Carbon::parse($date)->isToday();
    }

    public static function get_day($date)
    {

        $dayOfWeek = date('l', strtotime($date));

        $dayNumber = match ($dayOfWeek) {
            'Saturday' => 0,
            'Sunday' => 1,
            'Monday' => 2,
            'Tuesday' => 3,
            'Wednesday' => 4,
            'Thursday' => 5,
            'Friday' => 6,
            default => null,
        };

        if ($dayNumber !== null)
            return $dayNumber;
        else
            throw new Exception("Invalid day name: $dayOfWeek.");


    }
}
