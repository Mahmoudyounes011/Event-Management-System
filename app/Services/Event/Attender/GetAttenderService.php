<?php

namespace App\Services\Event\Attender;

use App\Services\Event\GetEventService;
use App\Services\User\GetUserService;
use App\Services\User\UserVerificationService;
use Exception;

class GetAttenderService
{
    public static function all($type,$event_id,$paginate=20)
    {
        $event = GetEventService::find($event_id,$type,['id','user_id','privacy'],['attenders','user'],'accepted');

        if($event['privacy'] == 'private')
            throw new Exception('You can not browse attenders of a private event');

        UserVerificationService::verify($event['user']['id']);

        $attenders = $event->attenders()->orderByPivot('created_at')->paginate($paginate);

        if(!isset($attenders[0]))
            throw new Exception('There are no attenders');

        return $attenders;
    }
}
