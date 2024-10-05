<?php

namespace App\Services\Event;

use App\Models\Event;
use App\Models\EventLevel;
use App\Models\Level;
use App\Models\PublicEvent;
use App\Models\Section;
use App\Models\Venue;
use App\Notifications\UserNotification;
use App\Services\Notification\SendNotificationService;
use App\Services\Rating\GetRatingService;
use App\Services\Section\GetSectionService;
use App\Services\User\GetUserService;
use App\Services\User\UserVerificationService;
use App\Services\Venue\GetVenueService;
use Exception;

class GetEventService
{

    public static function all($paginate=10)
    {
        $placed_events = Event::with('user','ticket','section','photos','attenders')->where('status','accepted')->where('privacy','public')->where('date','>',now())
        ->select(['id','user_id','section_id','name','description','capacity','date','start_time','end_time'])->paginate($paginate);

        foreach($placed_events as $key => $event)
        {
            $rest_capacity = $event['capacity']-count($event['attenders']);
            $event['section']['venue']['rate'] = (new GetRatingService)->get($event['section']['venue']);
            $placed_events[$key]['rest_capacity'] = $rest_capacity;
            unset($placed_events[$key]['attenders']);
            unset($event['section']['venue']['ratings']);
        }

        $unplaced_events = PublicEvent::with('user','ticket','capacity','photos','attenders')->where('privacy','public')->where('date','>',now())
        ->select(['id','user_id','latitude','longitude','name','description','date','start_time','end_time'])->paginate($paginate);

        foreach($unplaced_events as $key => $event)
        {
            if(isset($event['capacity']['capacity']))
            {
                $rest_capacity = $event['capacity']['capacity']-count($event['attenders']);
                $unplaced_events[$key]['rest_capacity'] = $rest_capacity;
                unset($unplaced_events[$key]['attenders']);
            }
        }

        if(!isset($placed_events[0]) && !isset($unplaced_events[0]))
            throw new Exception('There are no events');

        $events['placed'] = $placed_events;
        $events['unplaced'] = $unplaced_events;

        return $events;

        // Get the page number from the request (assuming you are using Laravel's request)
        $pageNumber = request()->query('page', 1);

        // Set the number of items per page
        $itemsPerPage = 10;

        // Calculate the offset based on the current page
        // $offset = ($pageNumber - 1) * $itemsPerPage;

        // Get the items for the current page
        $events = array_slice($events,($pageNumber-1)*$itemsPerPage,$itemsPerPage);

        // Get the total count of grouped items
        $totalItems = count($events);

        // Create a LengthAwarePaginator instance to handle pagination
        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
        $events,
        $totalItems,
        $itemsPerPage,
        $pageNumber
        );

        // Set the path for the paginator (current URL)
        $paginator->setPath(request()->url());

        return $paginator;
    }

    public static function search($request)
    {
        $data = $request->all();

        if(!isset($data['attribute']))
            throw new Exception('There are no events');

        $placed_events = Event::with('user','ticket','section','photos','attenders');
        $placed_events = $placed_events->where('name','like',$data['attribute'].'%')
        ->orWhere('description','like',$data['attribute'].'%')
        ->orWhere('description','like',$data['attribute'].'%')
        ->orWhereHas('user',function($query) use ($data)
        {
            $query->where('name','like',$data['attribute'].'%');
        });
        $placed_events = $placed_events->where('status','accepted')->where('privacy','public')->where('date','>',now())
        ->select(['id','user_id','section_id','name','description','capacity','date','start_time','end_time'])
        ->paginate($request->input('per_page')?$request->input('per_page'):10);

        foreach($placed_events as $key => $event)
        {
            $rest_capacity = $event['capacity']-count($event['attenders']);
            $event['section']['venue']['rate'] = (new GetRatingService)->get($event['section']['venue']);
            $placed_events[$key]['rest_capacity'] = $rest_capacity;
            unset($placed_events[$key]['attenders']);
            unset($event['section']['venue']['ratings']);
        }

        $unplaced_events = PublicEvent::with('user','ticket','capacity','photos','attenders');


        $unplaced_events = $unplaced_events->where('name','like',$data['attribute'].'%')->orWhere('description','like',$data['attribute'].'%')->orWhereHas('user',function($query) use ($data)
        {
            $query->where('name','like',$data['attribute'].'%');
        });

        $unplaced_events = $unplaced_events->where('privacy','public')->where('date','>',now())->select(['id','user_id','latitude','longitude','name','description','date','start_time','end_time'])->paginate($request->input('per_page')?$request->input('per_page'):10);

        foreach($unplaced_events as $key => $event)
        {
            if(isset($event['capacity']['capacity']))
            {
                $rest_capacity = $event['capacity']['capacity']-count($event['attenders']);
                $unplaced_events[$key]['rest_capacity'] = $rest_capacity;
                unset($unplaced_events[$key]['attenders']);
            }
        }

        if(!isset($placed_events[0]) && !isset($unplaced_events[0]))
            throw new Exception('There are no events');

        $events['placed'] = $placed_events;
        $events['unplaced'] = $unplaced_events;

        return $events;

        // Get the page number from the request (assuming you are using Laravel's request)
        $pageNumber = request()->query('page', 1);

        // Set the number of items per page
        $itemsPerPage = 10;

        // Calculate the offset based on the current page
        // $offset = ($pageNumber - 1) * $itemsPerPage;

        // Get the items for the current page
        $events = array_slice($events,($pageNumber-1)*$itemsPerPage,$itemsPerPage);

        // Get the total count of grouped items
        $totalItems = count($events);

        // Create a LengthAwarePaginator instance to handle pagination
        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
        $events,
        $totalItems,
        $itemsPerPage,
        $pageNumber
        );

        // Set the path for the paginator (current URL)
        $paginator->setPath(request()->url());

        return $paginator;
    }

    public static function suggestions($paginate=10)
    {
        $placed_events = Event::with('user','ticket','section','photos','attenders')->whereHas('promotions',function($query)
        {
            $query->where('type','suggestion')->where('event_promotions.end_at','>',now());
        })
        ->select(['id','user_id','section_id','name','description','capacity','date','start_time','end_time'])->paginate($paginate);

        foreach($placed_events as $key => $event)
        {
            $rest_capacity = $event['capacity']-count($event['attenders']);
            $event['section']['venue']['rate'] = (new GetRatingService)->get($event['section']['venue']);
            $placed_events[$key]['rest_capacity'] = $rest_capacity;
            unset($placed_events[$key]['attenders']);
            unset($event['section']['venue']['ratings']);
        }

        $unplaced_events = PublicEvent::with('user','ticket','capacity','photos','attenders')->whereHas('promotions',function($query)
        {
            $query->where('type','suggestion')->where('event_promotions.end_at','>',now());
        })->select(['id','user_id','latitude','longitude','name','description','date','start_time','end_time'])->paginate($paginate);

        foreach($unplaced_events as $key => $event)
        {
            if(isset($event['capacity']['capacity']))
            {
                $rest_capacity = $event['capacity']['capacity']-count($event['attenders']);
                $unplaced_events[$key]['rest_capacity'] = $rest_capacity;
                unset($unplaced_events[$key]['attenders']);
            }
        }

        if(!isset($placed_events[0]) && !isset($unplaced_events[0]))
            throw new Exception('There are no events');

        $events['placed'] = $placed_events;
        $events['unplaced'] = $unplaced_events;

        return $events;


        // Get the page number from the request (assuming you are using Laravel's request)
        $pageNumber = request()->query('page', 1);

        // Set the number of items per page
        $itemsPerPage = 10;

        // Calculate the offset based on the current page
        // $offset = ($pageNumber - 1) * $itemsPerPage;

        // Get the items for the current page
        $events = array_slice($events,($pageNumber-1)*$itemsPerPage,$itemsPerPage);

        // Get the total count of grouped items
        $totalItems = count($events);

        // Create a LengthAwarePaginator instance to handle pagination
        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
        $events,
        $totalItems,
        $itemsPerPage,
        $pageNumber
        );

        // Set the path for the paginator (current URL)
        $paginator->setPath(request()->url());

        return $paginator;
    }

    public static function find($event_id,$type='placed',$select=['id','name','description','capacity','date','start_time','end_time','privacy'],$with=null,$status=null)
    {
        if($type=='placed')
        {
            if(isset($with))
                $event = Event::with($with);
            if(isset($status))
            {
                if(isset($with))
                    $event = $event->where('status',$status);
                else
                    $event = Event::where('status',$status);
            }


            if(isset($event))
                $event = $event->find($event_id);
            else
                $event = Event::find($event_id);
        }
        else
        {
            if(isset($with))
                $event = PublicEvent::with($with)->select($select)->find($event_id);
            else
                $event = PublicEvent::select($select)->find($event_id);
        }

        if(!isset($event))
            throw new Exception('Event not found');

        return $event;

    }
    public static function placed_events(Section $section,$date=null)
    {
        if(isset($date))
            $events = $section->events_of_date($date)->get();
        else
            $events = $section->events;

        return $events;

    }

    public static function user_events($paginate=10)
    {
        $user = GetUserService::find();

        $events = [];
        $events['placed'] = $user->events()->with('pivot.level','ticket','attenders')->paginate($paginate);

        foreach($events['placed'] as $key => $event)
        {
            $rest_capacity = $event['capacity']-count($event['attenders']);
            $event['rest_capacity'] = $rest_capacity;
            unset($events['placed'][$key]['attenders']);
        }

        $events['unplaced'] = $user->public_events()->with('capacity','ticket','attenders')->paginate($paginate);

        foreach($events['unplaced'] as $key => $event)
        {
            if(isset($event['capacity']['capacity']))
            {
                $rest_capacity = $event['capacity']['capacity']-count($event['attenders']);
                $event['rest_capacity'] = $rest_capacity;
                unset($events['unplaced'][$key]['attenders']);

            }
        }

        if(!isset($events['placed'][0]) && !isset($events['unplaced'][0]))
            throw new Exception('You have not events');

        return $events;

        // Get the page number from the request (assuming you are using Laravel's request)
        $pageNumber = request()->query('page', 1);

        // Set the number of items per page
        $itemsPerPage = 10;

        // Calculate the offset based on the current page
        // $offset = ($pageNumber - 1) * $itemsPerPage;

        // Get the items for the current page
        $events = array_slice($events,($pageNumber-1)*$itemsPerPage,$itemsPerPage);

        // Get the total count of grouped items
        $totalItems = count($events);

        // Create a LengthAwarePaginator instance to handle pagination
        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
        $events,
        $totalItems,
        $itemsPerPage,
        $pageNumber
        );

        // Set the path for the paginator (current URL)
        $paginator->setPath(request()->url());

        return $paginator;

    }

    public static function  venue_requests($venue_id,$paginate=2)
    {
        $sections = GetSectionService::all($venue_id,['requests.pivot.level','requests.ticket'],'requests',true,$paginate);

        // if(!isset($sections[0]) || !isset($sections[0]['requests'][0]))
        //     throw new Exception('There are no requests');

        return $sections;
    }

    public static function venue_rejects($venue_id,$paginate=2)
    {
        $sections = GetSectionService::all($venue_id,['rejects.pivot.level','rejects.ticket'],'rejects',true,$paginate);

        // if(!isset($sections[0]) || !isset($sections[0]['rejects'][0]))
        //     throw new Exception('There are no rejected events');

        return $sections;
    }

    public static function venue_events($venue_id,$paginate=2)
    {
        $sections = GetSectionService::all($venue_id,['events.pivot.level','events.ticket','events.user'],'events',true,$paginate);

        // if(!isset($sections[0]) || !isset($sections[0]['events'][0]))
        //     throw new Exception('There are no events');

        return $sections;
    }

    public static function attender_events($paginate=10)
    {
        $user = GetUserService::find();

        $events['placed'] = $user->registers()->with('user','ticket','section','photos','attenders')->paginate($paginate);

        foreach($events['placed'] as $key => $event)
        {
            $rest_capacity = $event['capacity']-count($event['attenders']);
            $event['section']['venue']['rate'] = (new GetRatingService)->get($event['section']['venue']);
            $events['placed'][$key]['rest_capacity'] = $rest_capacity;
            unset($events['placed'][$key]['attenders']);
            unset($event['section']['venue']['ratings']);
        }

        $events['unplaced'] = $user->public_registers()->with('user','ticket','capacity','photos','attenders')->paginate($paginate);

        foreach($events['unplaced'] as $key => $event)
        {
            if(isset($event['capacity']['capacity']))
            {
                $rest_capacity = $event['capacity']['capacity']-count($event['attenders']);
                $events['unplaced'][$key]['rest_capacity'] = $rest_capacity;
                unset($events['unplaced'][$key]['attenders']);
            }
        }

        if(!isset($events['placed']) && !isset($events['unplaced']))
            throw new Exception('There are no events');

        return $events;
    }

    public function invite($type,$event_id,$user_id)
    {
        $event = $this->find($event_id,$type,['name','description','date','user_id','id'],['ticket','user'],'accepted');

        if(!isset($event))
            throw new Exception('There is no event');

        UserVerificationService::verify($event['user']['id']);

        if($event['user']['id'] == $user_id)
            throw new Exception('You can not invite yourself');

        if($event['ticket'])
            throw new Exception('Paid events can not be invitable');

        if(($event['date'] == now()->format('Y-m-d') && $event['start_time'] >= now()->format('H:i')) || $event['date'] < now()->format('Y-m-d'))
            throw new Exception('The event is passed');

        $user = GetUserService::find($user_id);

        $owner = $event['user'];

        $message = 'Hello '.$user['name'].', '.$owner['name'].' invited to register in his '.$event['name'].' event , description : '.$event['description'];

        (new SendNotificationService)->sendNotify($user,new UserNotification($user['id'],$message,$type.'-Invitation','pending',$event_id));
    }

    public function invitation_link_reply($request,$type,$event_id,$result)
    {
        $select = $type=='placed'?['name','description','date','start_time','capacity','user_id','id']:['name','description','date','start_time','user_id','id'];

        $with = $type=='unplaced'?['user','ticket','capacity','attenders']:['user','ticket','attenders'];

        $event = $this->find($event_id,$type,$select,$with,'accepted');

        $user = GetUserService::find();

        if($event['user']['id'] == $user['id'])
            throw new Exception('You can not invite yourself');

        if($event['ticket'])
            throw new Exception('Paid events can not be invitable');

        if(($event['date'] == now()->format('Y-m-d') && $event['start_time'] >= now()->format('H:i')) || $event['date'] < now()->format('Y-m-d'))
            throw new Exception('The event is passed');

        if(isset($event['attenders']) && isset($event['capacity']) && isset($event['capacity']['capacity']) && $event['capacity']['capacity']<=count($event['attenders']))
            throw new Exception('Sorry ! , the event is full');

        if(isset($event['attenders']) && isset($event['capacity']) && !isset($event['capacity']['capacity']) && $event['capacity']<=count($event['attenders']))
            throw new Exception('Sorry ! , the event is full');

        $owner = $event['user'];

        if($result == 'accept')
        {
            $message = 'Hello '.$owner['name'].' , your invitation to '.$user['name'].' is approved now';

            $event->attenders()->attach($user);
        }
        else if($result == 'reject')
        {
            $message = 'Hello '.$owner['name'].' , your invitation to '.$user['name'].' is rejected now .';

            if(isset($request->reason))
                $message = $message.' '.$request->reason;
        }
        else
            throw new Exception('Invalid result');

        (new SendNotificationService)->sendNotify($user,new UserNotification($owner['id'],$message,$type.'-Invitation',$result,$event_id));
    }

    public function event_verification($type,$event_id)
    {
        $event = $this->find($event_id,$type,['user_id','id'],['user'],'accepted');

        if(!isset($event))
            throw new Exception('There is no event');

        UserVerificationService::verify($event['user']['id']);
    }
}
