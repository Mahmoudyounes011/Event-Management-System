<?php

namespace App\Services\Event\Attender;

use App\Models\Event;
use App\Models\PublicEvent;
use App\Models\Section;
use App\Notifications\UserNotification;
use App\Services\Event\GetEventService;
use App\Services\Notification\SendNotificationService;
use App\Services\Payment\UpdateBalanceService;
use App\Services\Rating\GetRatingService;
use App\Services\Section\GetSectionService;
use App\Services\User\GetUserService;
use Exception;
use Illuminate\Support\Facades\Hash;

class RegisterEventService
{

    public static function store($request,$type,$event_id)
    {
        $select = match($type)
        {
            'placed' => ['id','user_id','privacy','capacity'],
            'unplaced' => ['id','user_id','privacy'],
            default => null
        };

        $with = match($type)
        {
            'placed' => ['ticket','attenders','user'],
            'unplaced' => ['ticket','capacity','attenders','user'],
            default => null
        };

        $event = GetEventService::find($event_id,$type,$select,$with,'accepted');

        if($event['privacy'] == 'private')
            throw new Exception('You can not register to a private event');

        if(!isset($event['capacity']))
            throw new Exception('You can not register to a non limited event');

        $capacity = match($type)
        {
            'placed' => $event['capacity'],
            'unplaced' => $event['capacity']['capacity'],
            default => null
        };

        if($request->password==null)
            throw new Exception('You must enter your password');

        $user = GetUserService::find();

        if($request->password!=null && !Hash::check($request->password,$user['password']))
            throw new Exception('Wrong password');



        $check = match($type)
        {
            'placed' => $user->check_if_registered($event_id)->get(),
            'unplaced' => $user->public_check_if_registered($event_id)->get(),
            default => null
        };

        if(isset($check[0]))
            throw new Exception('You can not register more than one time');


        if(count($event['attenders']) == $capacity)
            throw new Exception('Sorry , the number of attenders is full');

        if(isset($event['ticket']))
            (new UpdateBalanceService)->pay($user,$event['ticket']['price'],$event['user']);

        $event->attenders()->attach($user,['created_at' => now()]);

        $message = 'Someone regestered in your '.$event['name'].' event,check your event attenders';

        (new SendNotificationService)->sendNotify($event['user'],new UserNotification($event['user']['id'],$message));

    }

}
