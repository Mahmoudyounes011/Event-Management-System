<?php

namespace App\Services\Time;

use App\Models\Section;
use App\Models\Venue;
use App\Services\Event\GetEventService;
use App\Services\Section\GetSectionService;
use App\Services\Venue\GetTimesVenueService;
use DateTime;
use Exception;

class GetFreeTimesService
{
    public function get($request,$section_id)
    {
        $section = GetSectionService::find($section_id);

        $date = ($request->validated())['date'];

        $day = CheckDateService::get_day($date);

        $venue = $section->venue;

        $isToday = CheckDateService::is_today($date);

        $open_times = GetTimesVenueService::times_of_day($venue,$day,$isToday);

        $events = GetEventService::placed_events($section,$date);


        $number_of_events = count($events);
        $key = 0;
        $event = isset($events[0]) ? $events[$key] : null;
        $free_times = [];

        foreach($open_times as $time)
        {
            $sub_start_time = strtotime($time->pivot->start_time);
            $sub_end_time = strtotime($time->pivot->end_time);
            for($i = $sub_start_time;$i<$sub_end_time;)
            {
                if(!isset($events[0]))
                {
                    $free_times[] = date('H:i',$i);
                    $i = strtotime(date('H:i',$i).'+ 1 hour');
                    continue;
                }

                if($i != strtotime($event->pivot->start_time))
                {
                    $free_times[] = date('H:i',$i);
                    $i = strtotime(date('H:i',$i).'+ 1 hour');
                }
                else
                {
                    $i = strtotime(date('H:i',$i).'+'.$event->pivot->period.' hour');

                    if($key+1<$number_of_events)
                        $event = $events[++$key];
                }
            }
        }

        return $free_times;

    }


}
