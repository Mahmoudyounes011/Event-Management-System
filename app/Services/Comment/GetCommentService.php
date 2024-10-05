<?php

namespace App\Services\Comment;

use App\Services\Event\GetEventService;

use Exception;

class GetCommentService
{
    public static function all($type,$event_id,$paginate=20)
    {
        $event = GetEventService::find($event_id,$type,['id','privacy'],['comments'],'accepted');

        if($event['privacy'] == 'private')
            throw new Exception('You can not browse comments of a private event');

        $comments = $event->comments()->orderByPivot('created_at','desc')->paginate($paginate);

        if(!isset($comments[0]))
            throw new Exception('There are no comments');

        return $comments;
    }
}
