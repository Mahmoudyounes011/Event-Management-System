<?php

namespace App\Services\Time;

use App\Notifications\UserNotification;
use App\Services\Notification\SendNotificationService;
use App\Services\Store\GetStoreService;
use App\Services\User\UserVerificationService;
use App\Services\Venue\GetTimesVenueService;
use App\Services\Venue\GetVenueService;
use DateTime;
use Exception;

class StoreTimeScheduleService
{
    public static function store($request,$type,$id)
    {

        $data = $request->validated();

        $object = StoreTimeScheduleService::get_object($type,$id);

        $start_times = $data['start_time'];
        $end_times = $data['end_time'];


        ValidTimeService::is_valid($type,$start_times,$end_times);

        $exist_times = $object->times->groupBy('day');

        foreach($start_times as $day => $sub_start_times)
        {

            if(isset($exist_times[$day]))
                $exist_times_of_day = $exist_times[$day];

            foreach($sub_start_times as $key => $start_time)
            {
                if(isset($exist_times_of_day))
                    foreach($exist_times_of_day as $sub_exist_time)
                        if((date('H:i:s',strtotime($start_time)) >= $sub_exist_time['pivot']['start_time'] && date('H:i:s',strtotime($start_time)) < $sub_exist_time['pivot']['end_time']) || (date('H:i:s',strtotime($start_time)) < $sub_exist_time['pivot']['start_time'] && date('H:i:s',strtotime($end_times[$day][$key])) > $sub_exist_time['pivot']['start_time']))
                            throw new Exception('Nested times');

                $object->times()->attach(['day_id' => $day+1] , ['start_time' => $start_time , 'end_time' => $end_times[$day][$key],'created_at' => now()]);
            }

            $exist_times = null;
        }

        if((isset($object['sections']) && isset($object['sections'][0])) || (isset($object['products']) && isset($object['products'][0])))
        {
            $object->available = 1;
            $object->save();

            $message = 'Hello '.$object['owner']['name'].' your '.$type.' '.$object['name'].' became visible to all users now';

            (new SendNotificationService)->sendNotify($object['owner'],new UserNotification($object['owner']['id'],$message,$type,'Available'));
        }


    }

    private static function get_object($type,$id) :mixed
    {
        if($type == 'venue')
        {
            $object  = GetVenueService::find($id,['sections','owner'],true);
        }
        else if($type == 'store')
        {
            $object  = GetStoreService::find($id,['products','owner'],true);
        }
        else
        {
            throw new Exception('Invalid type');
        }

        UserVerificationService::verify($object['user_id']);

        return $object;
    }

}
