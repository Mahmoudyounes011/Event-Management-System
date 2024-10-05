<?php

namespace App\Services\Comment;

use App\Models\Category;
use App\Notifications\UserNotification;
use App\Services\Event\GetEventService;
use App\Services\Notification\SendNotificationService;
use App\Services\User\GetUserService;
use Exception;

class StoreCommentService
{
    public static function store($request,$type,$event_id)
    {
        $comment = $request->validated();

        $event = GetEventService::find($event_id,$type,['id','user_id','name','privacy'],['user'],'accepted');

        if($event['privacy'] == 'private')
            throw new Exception('You can not comment on private event');

        $user = GetUserService::find();

        $event->comments()->attach($user,['comment' => $comment['comment'],'created_at' => now()]);

        $message = 'Someone commented on your '.$event['name'].' event,check your event comments';

        (new SendNotificationService)->sendNotify($event['user'],new UserNotification($event['user']['id'],$message));
    }
}
