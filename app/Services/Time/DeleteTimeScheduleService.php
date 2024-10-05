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

class DeleteTimeScheduleService
{
    public static function delete($time_id)
    {
        $time = FreeTime::with('relatedTo')->find($time_id);

        if(!isset($time))
            throw new Exception('Time not found');

        UserVerificationService::verify($time['schedulable']['user_id']);

        $exist_times = $time['schedulable']->times()->wherePivot('id','!=',$time['id'])->get();

        if(count($exist_times) == 0)
        {
            $time['schedulable']->available = 0;
            $time['schedulable']->save();
        }

        $time->delete();
    }

}
