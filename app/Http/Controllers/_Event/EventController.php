<?php

namespace App\Http\Controllers\_Event;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateEventRequest;
use App\Http\Requests\CreatePublicPlaceEventRequest;
use App\Services\Event\Attender\GetAttenderService;
use App\Services\Event\AddEventService;
use App\Services\Event\Attender\DeleteAttenderService;
use App\Services\Event\Attender\RegisterEventService;
use App\Services\Event\DeleteEventService;
use App\Services\Event\EditEventService;
use App\Services\Event\GetEventService;
use App\Services\Rating\GetRatingService;
use App\Services\Section\GetSectionService;
use Exception;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function home(Request $request,GetEventService $event)
    {

        try
        {
            $events = $event->all($request->input('per_page')?$request->input('per_page'):10);
        }
        catch(Exception $e)
        {
            return response([
                'status' => 'fail',
                'message' => $e->getMessage()
            ]);
        }

        return response([
            'status' => 'success',
            'data' => $events
        ]);
    }

    public function get(GetEventService $event,$type,$event_id)
    {

        try
        {
            $select = $type=='placed'?['id','user_id','section_id','name','description','capacity','date','start_time','end_time','privacy']:['name','description','user_id','id','date','start_time','end_time','privacy'];

            $with = $type=='unplaced'?['user','capacity','photos','ticket']:['user','photos','ticket','section'];

            $event = $event->find($event_id,$type,$select,$with,'accepted');

            if($event['privacy'] != 'public')
                throw new Exception('Not public event');

            if($type == 'placed')
            {
                $event['section']['venue']['rate'] = (new GetRatingService)->get($event['section']['venue']);
                unset($event['section']['venue']['ratings']);
            }
        }
        catch(Exception $e)
        {
            return response([
                'status' => 'fail',
                'message' => $e->getMessage()
            ]);
        }

        return response([
            'status' => 'success',
            'data' => $event
        ]);
    }

    public function suggestions(Request $request,GetEventService $event)
    {

        try
        {
            $events = $event->suggestions($request->input('per_page')?$request->input('per_page'):10);
        }
        catch(Exception $e)
        {
            return response([
                'status' => 'fail',
                'message' => $e->getMessage()
            ]);
        }

        return response([
            'status' => 'success',
            'data' => $events
        ]);
    }

    public function search(Request $request,GetEventService $event)
    {

        try
        {
            $events = $event->search($request);
        }
        catch(Exception $e)
        {
            return response([
                'status' => 'fail',
                'message' => $e->getMessage()
            ]);
        }

        return response([
            'status' => 'success',
            'data' => $events
        ]);
    }

    public function user_events(Request $request,GetEventService $event)
    {
        try
        {
            $events = $event->user_events($request->input('per_page')?$request->input('per_page'):10);
        }
        catch(Exception $e)
        {
            return response([
                'status' => 'fail',
                'message' => $e->getMessage()
            ]);
        }

        return response([
            'status' => 'success',
            'data' => $events
        ]);
    }

    public function venue_requests($venue_id,Request $request,GetEventService $event)
    {
        try
        {
            $events = $event->venue_requests($venue_id,$request->input('per_page')?$request->input('per_page'):2);
        }
        catch(Exception $e)
        {
            return response([
                'status' => 'fail',
                'message' => $e->getMessage()
            ]);
        }

        return response([
            'status' => 'success',
            'data' => $events
        ]);
    }

    public function venue_events($venue_id,Request $request,GetEventService $event)
    {
        try
        {
            $events = $event->venue_events($venue_id,$request->input('per_page')?$request->input('per_page'):2);
        }
        catch(Exception $e)
        {
            return response([
                'status' => 'fail',
                'message' => $e->getMessage()
            ]);
        }

        return response([
            'status' => 'success',
            'data' => $events
        ]);
    }
    public function venue_rejects($venue_id,Request $request,GetEventService $event)
    {
        try
        {
            $events = $event->venue_rejects($venue_id,$request->input('per_page')?$request->input('per_page'):10);
        }
        catch(Exception $e)
        {
            return response([
                'status' => 'fail',
                'message' => $e->getMessage()
            ]);
        }

        return response([
            'status' => 'success',
            'data' => $events
        ]);
    }

    public function venue_reply($event_id,$result,Request $request,EditEventService $event)
    {
        try
        {
            $event->edit($request,$event_id,$result);
        }
        catch(Exception $e)
        {
            return response([
                'status' => 'fail',
                'message' => $e->getMessage()
            ]);
        }

        return response([
            'status' => 'success',
            'message' => 'The reply is sent successfully'
        ]);
    }

    public function craete_placed(CreateEventRequest $request,$section_id,AddEventService $event)
    {
        try
        {
            $event->add_placed($request,$section_id);
        }
        catch(Exception $e)
        {
            return response([
                'status' => 'fail',
                'message' => $e->getMessage()
            ]);
        }

        return response([
            'status' => 'success',
            'message' => 'The event is queued to owner\'s requests'
        ]);
    }

    public function craete_unplaced(CreatePublicPlaceEventRequest $request,AddEventService $event)
    {
        try
        {
            $event->add_unplaced($request);
        }
        catch(Exception $e)
        {
            return response([
                'status' => 'fail',
                'message' => $e->getMessage()
            ]);
        }

        return response([
            'status' => 'success',
            'message' => 'The event is created successfully'
        ]);
    }

    public function delete_unplaced(Request $request,$event_id,DeleteEventService $event)
    {
        try
        {
            $event->delete($request,$event_id);
        }
        catch(Exception $e)
        {
            return response([
                'status' => 'fail',
                'message' => $e->getMessage()
            ]);
        }

        return response([
            'status' => 'success',
            'message' => 'The event is deleted successfully'
        ]);
    }

    public function register(RegisterEventService $event,Request $request,$type,$event_id)
    {
        try
        {
            $event->store($request,$type,$event_id);
        }
        catch(Exception $e)
        {
            return response([
                'status' => 'fail',
                'message' => $e->getMessage()
            ]);
        }

        return response([
            'status' => 'success',
            'message' => 'The registerization is done successfully'
        ]);
    }

    public function attender_events(Request $request,GetEventService $event)
    {
        try
        {
            $attenders = $event->attender_events($request->input('per_page')?$request->input('per_page'):10);
        }
        catch(Exception $e)
        {
            return response([
                'status' => 'fail',
                'message' => $e->getMessage()
            ]);
        }

        return response([
            'status' => 'success',
            'data' => $attenders
        ]);
    }

    public function attenders(Request $request,GetAttenderService $event,$type,$event_id)
    {
        try
        {
            $attenders = $event->all($type,$event_id,$request->input('per_page')?$request->input('per_page'):20);
        }
        catch(Exception $e)
        {
            return response([
                'status' => 'fail',
                'message' => $e->getMessage()
            ]);
        }

        return response([
            'status' => 'success',
            'data' => $attenders
        ]);
    }

    public function deleteAttender(DeleteAttenderService $event,Request $request,$type,$event_id,$user_id)
    {
        try
        {
            $attenders = $event->delete($request,$type,$event_id,$user_id);
        }
        catch(Exception $e)
        {
            return response([
                'status' => 'fail',
                'message' => $e->getMessage()
            ]);
        }

        return response([
            'status' => 'success',
            'data' => 'The attender is deleted successfully'
        ]);
    }

    public function invite(GetEventService $event,$type,$event_id,$user_id)
    {
        try
        {
            $event->invite($type,$event_id,$user_id);
        }
        catch(Exception $e)
        {
            return response([
                'status' => 'fail',
                'message' => $e->getMessage()
            ]);
        }

        return response([
            'status' => 'success',
            'message' => 'The invitation is done successfully'
        ]);
    }

    public function event_verification(GetEventService $event,$type,$event_id)
    {
        try
        {
            $event->event_verification($type,$event_id);
        }
        catch(Exception $e)
        {
            return response([
                'status' => 'fail',
                'data' => 0
            ]);
        }

        return response([
            'status' => 'success',
            'data' => 1
        ]);
    }

    public function invitation_link_reply(GetEventService $event,Request $request,$type,$event_id,$result)
    {
        try
        {
            $event->invitation_link_reply($request,$type,$event_id,$result);
        }
        catch(Exception $e)
        {
            return response([
                'status' => 'fail',
                'message' => $e->getMessage()
            ]);
        }

        return response([
            'status' => 'success',
            'message' => 'The registerization is done successfully'
        ]);
    }
}
