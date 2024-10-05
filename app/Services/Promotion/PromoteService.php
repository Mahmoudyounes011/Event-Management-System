<?php

namespace App\Services\Promotion;

use App\Jobs\PromotionNotifyJob;
use App\Models\Promotion;
use App\Services\Event\GetEventService;
use App\Services\Payment\UpdateBalanceService;
use App\Services\User\GetUserService;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Hash;

class PromoteService
{
    public function promote($request,$promotion_id,$event_id,$event_type)
    {
        if($request->password==null)
            throw new Exception('You must enter your password');

        $user = GetUserService::find();

        if($request->password!=null && !Hash::check($request->password,$user['password']))
            throw new Exception('Wrong password');

        $promotion = (new GetPromotionService)->find($promotion_id);

        $hours = $request->input('hours');

        if((!isset($hours) && $promotion->type=='Suggestion') || (isset($hours) && $promotion->type=='notification'))
            throw new Exception('There is promotion error for input');

        if($event_type=='placed')
        {
            $event = GetEventService::find($event_id,'placed',['id','user_id','name','privacy','date','start_time'],null,'accepted');
        }
        else
        {
            $event = GetEventService::find($event_id,'unplaced',['id','user_id','name','privacy','date','start_time']);
        }

        if($event['user_id'] != $user['id'])
            throw new Exception('You van not promote an event no related to you');


        if($event['privacy'] == 'private')
            throw new Exception('private events can not be promoted');

        $date = Carbon::createFromFormat('Y-m-d H:i:s',$event['date'].' '.$event['start_time']);

        if($date <= now())
            throw new Exception('event can not be promoted at the date of the event');


        if($promotion->type=='Suggestion')
            UpdateBalanceService::pay($user,$hours*$promotion->cost,GetUserService::find(1));
        else
            UpdateBalanceService::pay($user,$promotion->cost,GetUserService::find(1));

        if($promotion->type=='notification')
            dispatch(new PromotionNotifyJob($event));
        else
        {
            $end_at = now()->addHours($hours);

            $event->promotions()->attach($promotion,['end_at' => $end_at,'created_at' => now()]);
        }



    }
}
