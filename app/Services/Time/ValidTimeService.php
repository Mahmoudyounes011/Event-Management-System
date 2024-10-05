<?php

namespace App\Services\Time;

use Exception;

class ValidTimeService
{
    public static function is_valid($type,array $start_times, array $end_times) :void
    {
        CheckTimeRulesService::check_order($start_times);
        CheckTimeRulesService::check_order($end_times);
        
        if($type === 'venue')
            CheckTimeRulesService::check_hourly_system($start_times,$end_times);

        foreach($start_times as $key => $day)
        {
            $end_time = $end_times[$key];
            for($i = 0; $i < count($day) - 1; $i++)
                if(strtotime($day[$i]) >= strtotime($day[$i + 1]) || strtotime($day[$i + 1]) <= strtotime($end_time[$i]))
                    throw new Exception('Times should be inserted in order');
        }
    }

}
