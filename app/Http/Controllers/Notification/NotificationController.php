<?php

namespace App\Http\Controllers\Notification;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddRequest;
use App\Http\Requests\EditRequest;
use App\Http\Requests\SendAllUsersRequest;
use App\Services\Notification\EditNotificationService;
use App\Services\Notification\GetNotificationService;
use App\Services\Notification\SendNotificationService;
use App\Services\Store\AddStoreService;
use App\Services\Venue\AddVenueService;
use Exception;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function get_all(Request $request)
    {
        try
        {
            $notifications = GetNotificationService::get_all($request->input('per_page')?$request->input('per_page'):5);
        }
        catch(Exception $e)
        {
            return response(['status' => 'fail','message' => $e->getMessage()]);
        }

        return response(['status' => 'success','data' => $notifications]);
    }

    public function mark_as_read($id)
    {
        try
        {
            $notifications = EditNotificationService::mark_as_read($id);
        }
        catch(Exception $e)
        {
            return response(['status' => 'fail','message' => $e->getMessage()]);
        }

        return response(['status' => 'success','message' => 'Notification deleted successfully']);
    }

    public function send_if_add($from,AddRequest $request,SendNotificationService $send)
    {
        try
        {
            $send->add($from,$request);
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
            'message' => 'the notification is sent successfully'
        ]);
    }

    public function send_if_update($from,$id,EditRequest $request,SendNotificationService $send)
    {
        try
        {
            $send->update($from,$id,$request);
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
            'message' => 'the notification is sent successfully'
        ]);
    }

    public function notify_all(SendAllUsersRequest $request,SendNotificationService $send)
    {
        try
        {
            $send->notify_all($request);
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
            'message' => 'the notification is sent successfully'
        ]);
    }

    public function reply($notification_id,$result,Request $request,SendNotificationService $send)
    {
        try
        {
            $send->response($notification_id,$result,$request);
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
            'data' => 'The notification is sent successfully    '
        ]);
    }

    public function invitation_reply($notification_id,$result,Request $request,SendNotificationService $send)
    {
        try
        {
            $send->invitation_reply($notification_id,$result,$request);
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
            'data' => 'The notification is sent successfully'
        ]);
    }
}
