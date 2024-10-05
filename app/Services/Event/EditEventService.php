<?php

namespace App\Services\Event;

use App\Notifications\UserNotification;
use App\Services\Notification\SendNotificationService;
use App\Services\Payment\UpdateBalanceService;
use App\Services\User\UserVerificationService;
use Exception;

class EditEventService
{

    public static function edit($request,$event_id,$result)
    {
        if($result != 'accept' && $result != 'reject')
            throw new Exception('Invalid result');

        $event = GetEventService::find($event_id,'placed',['id','user_id','section_id','status','name','description'],['user','section','pivot.level'],'pending');

        $user = $event['user'];

        $owner = $event['section']['venue']['owner'];

        UserVerificationService::verify($owner->id);

        $nowDate = now()->format('Y-m-d');

        if(($event['date'] < $nowDate) || ($event['date'] < $nowDate && $event['start_time'] < now()->subHour()->format('H:i:s')))
            $result = 'reject';


        $reasone = $request->reasone;

        $message = 'Hello '.$user->name.", Your event with name :".$event->name." and description : ".$event->description;

        if($result=='accept')
        {
            if(isset($reasone))
                throw new Exception('You can not send reasone while you are accepted the request');

            $conflict = $event['section']->events()->where('status','accepted')->where('date',$event['date'])->where('start_time','<=',$event['start_time'])
            ->where('end_time','>=',$event['end_time'])->orWhere('date',$event['date'])->where('status','accepted')->where('end_time','>',$event['start_time'])
            ->where('end_time','<=',$event['end_time'])->orWhere('date',$event['date'])->where('status','accepted')->where('start_time','>',$event['start_time'])
            ->where('start_time','<=',$event['end_time'])->get();

            if(!isset($conflict[0]))
            {
                $event->status = 'accepted';

                $message = $message." has been accepted";
            }

        }
        if($result=='reject' || isset($conflict[0]))
        {
            $event->status = 'rejected';

            $message = $message." has been rejected .";

            if(isset($reasone))
                $message = $message."The reasone is : ".$reasone.'.';

            $message = $message."The cost of this event will be added to your card";

            $balance = (isset($event['pivot']) && isset($event['pivot']['level'])) ? $event['section']['price']+$event['pivot']['level']['price'] : $event['section']['price'];

            (new UpdateBalanceService)->addAfterReject($balance,$user,$event['section']['venue'],true);
        }

        $event->save();

        (new SendNotificationService)->sendNotify($user,new UserNotification($user->id,$message,'Event',$result));

        if(isset($conflict[0]))
            throw new Exception('You can not accept more than one event within the same time,the event is rejected');



    }
}
