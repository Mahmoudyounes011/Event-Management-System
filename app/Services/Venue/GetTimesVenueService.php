<?php
namespace App\Services\Venue;

use App\Models\Venue;
use Carbon\Carbon;
use Exception;

class GetTimesVenueService
{
    public static function times_of_day(Venue $venue,$day,$isToday)
    {

        $times = $venue->times_of_day($day)->get();

        if(!isset($times[0]))
            throw new Exception('There are no times in this day');

        if($isToday)
        {
            $now = Carbon::now()->format('H:i:s');
            foreach($times as $key => $time)
            {
                $endTime = Carbon::createFromFormat('H:i:s',$time->pivot->end_time);
                if($endTime->lte($now))
                    unset($times[$key]);
                else
                {
                    $startTime = Carbon::createFromFormat('H:i:s',$time->pivot->start_time);
                    $now = Carbon::createFromFormat('H:i:s',$now);
                    if($startTime->lt($now))
                    {
                        $diff = $endTime->diff($now);
                        $endTime->hour -= $diff->h;
                        $time->pivot->start_time = $endTime->format('H:i:s');
                    }
                    else
                        break;

                }

            }
        }

        return $times;
    }


}
