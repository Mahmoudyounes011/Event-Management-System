<?php

namespace App\Services\Event\Attender;

use App\Models\Attender;
use App\Services\Event\GetEventService;
use App\Services\Payment\UpdateBalanceService;
use App\Services\User\GetUserService;
use App\Services\User\UserVerificationService;
use Exception;
use Illuminate\Support\Facades\Hash;

class DeleteAttenderService
{
    public static function delete($request,$type,$event_id,$user_id)
    {
        $event = GetEventService::find($event_id,$type,['id','user_id','privacy'],['user','ticket'],'accepted');

        if($event['privacy'] == 'private')
            throw new Exception('You can not browse attenders of a private event');

        UserVerificationService::verify($event['user']['id']);

        if(isset($event['ticket']))
        {
            if($request->password==null)
                throw new Exception('You must enter your password');

            if($request->password!=null && !Hash::check($request->password,$event['user']['password']))
                throw new Exception('Wrong password');
        }

        $attender = $event->attenderPivot()->where('user_id',$user_id)->get();

        if(!isset($attender[0]))
            throw new Exception('User not found');

        if(isset($event['ticket']))
            (new UpdateBalanceService)->addAfterReject($event['ticket']['price'],GetUserService::find($attender[0]['user_id']),$event['user']);

        $attender[0]->delete();
    }
}
