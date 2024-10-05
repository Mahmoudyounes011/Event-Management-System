<?php

namespace App\Services\Time;

use App\Models\FreeTime;
use App\Notifications\UserNotification;
use App\Services\Notification\SendNotificationService;
use App\Services\Store\GetStoreService;
use App\Services\User\UserVerificationService;
use App\Services\Venue\GetVenueService;
use DateTime;
use Exception;

class EditTimeScheduleService
{
        public static function edit($request,$type,$time_id)
    {
        $data = $request->validated();

        $start_time = $data['start_time'];
        $end_time = $data['end_time'];

        if($type=='venue')
        {
            $checked_start_time = DateTime::createFromFormat('H:i', $start_time);
            $checked_end_time = DateTime::createFromFormat('H:i', $end_time);

            if($checked_start_time->format('i') != $checked_end_time->format('i'))
                throw new Exception("The start and the end of each period should have the same minutes");
        }

        $time = FreeTime::with('relatedTo')->find($time_id);

        if(!isset($time))
            throw new Exception('Time not found');

        UserVerificationService::verify($time['schedulable']['user_id']);


        $exist_times = $time['schedulable']->times_of_day($time['day_id']-1)->wherePivot('id','!=',$time['id'])->get();

        if(count($exist_times) == 1)
        {
            $time->start_time = $start_time;
            $time->end_time = $end_time;
        }
        else
        {
            foreach($exist_times as $exist_time)
                if((date('H:i:s',strtotime($start_time)) >= $exist_time['pivot']['start_time'] && date('H:i:s',strtotime($start_time)) < $exist_time['pivot']['end_time']) || (date('H:i:s',strtotime($start_time)) < $exist_time['pivot']['start_time'] && date('H:i:s',strtotime($end_time)) > $exist_time['pivot']['start_time']))
                    throw new Exception('Nested times');

            $time->start_time = $start_time;
            $time->end_time = $end_time;
        }

        $time->save();

    }

}
