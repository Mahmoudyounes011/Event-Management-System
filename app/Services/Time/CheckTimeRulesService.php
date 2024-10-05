<?php

namespace App\Services\Time;

use DateTime;
use Exception;

class CheckTimeRulesService
{
    public static function check_order(array $times) :void
    {
        foreach($times as $day)
            for($i = 0; $i < count($day) - 1; $i++)
                if(strtotime($day[$i]) >= strtotime($day[$i + 1]))
                    throw new Exception('Times should be inserted in order');
    }

    public static function check_hourly_system(array $start_times, array $end_times) :void
    {
        foreach($start_times as $day => $sub_start_times)
        {
            foreach($sub_start_times as $key => $start_time)
            {
                $checked_start_time = DateTime::createFromFormat('H:i', $start_times[$day][$key]);
                $checked_end_time = DateTime::createFromFormat('H:i', $end_times[$day][$key]);

                if ($checked_start_time->format('i') != $checked_end_time->format('i'))
                    throw new Exception("The start and the end of each period should have the same minutes");
            }
        }
    }
}
