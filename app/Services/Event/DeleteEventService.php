<?php

namespace App\Services\Event;

use App\Jobs\NotifyAttendersJob;
use App\Jobs\RestoreMoneyJob;
use App\Notifications\UserNotification;
use App\Services\Notification\SendNotificationService;
use App\Services\Payment\UpdateBalanceService;
use App\Services\User\UserVerificationService;
use Exception;

class DeleteEventService
{

    public static function delete($request,$event_id)
    {
        $reason = $request->validate(['reason' => 'required'])['reason'];

        $event = GetEventService::find($event_id,'unplaced',['id','user_id','name','description'],['user','ticket','attenders']);

        $user = $event['user'];

        $attenders = $event['attenders'];

        $message = null;

        if(count($attenders) != 0)
            $message = 'Sorry , Event '.$event['name'].' which has description : '.$event['description'].' was deleted.';

        if(isset($message))
        {
            $message2 = '';

            if(isset($event['ticket']))
            {
                $message2 = 'Your money will be restored now';
                dispatch(new RestoreMoneyJob($event));
            }

            dispatch(new NotifyAttendersJob($message.' '.$message2,$event))->delay(10);
        }

        // $event->delete();

        $message = $message.' '.$reason;

        (new SendNotificationService)->sendNotify($user,new UserNotification($user['id'],$message));

    }
}
