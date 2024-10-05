<?php
namespace App\Services\Notification;

use App\Jobs\NotifyAllUsersJob;
use App\Models\Role;
use App\Models\User;
use App\Notifications\AddNotification;
use App\Notifications\AdminNotification;
use App\Notifications\UserNotification;
use App\Services\Event\GetEventService;
use App\Services\Image\ImageService;
use App\Services\Store\AddStoreService;
use App\Services\Store\GetStoreService;
use App\Services\User\GetUserService;
use App\Services\Venue\AddVenueService;
use App\Services\Venue\GetVenueService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class SendNotificationService
{
    public function add($type,$request)
    {
        $data = $request->validated();

        $user_id = GetUserService::find()->id;

        $data['user_id'] = $user_id;

        $images = $request->file('images');

        if(isset($images))
        {
            $paths = (new ImageService)->upload_image($images,$type);
        }

        $data['paths'] = $paths;

        $admins = Role::find(1)->with('users')->first();


        $this->sendNotify($admins['users'],new AddNotification($type,'Add',$data));

    }
    public function update($type,$id,$request)
    {
        $data = $request->validated();

        if(count($data)==0)
            throw new Exception('There is nothing to update');

        $user_id = GetUserService::find()->id;

        if($type=='store')
            $object = GetStoreService::find($id,null,true);
        else
            $object = GetVenueService::find($id,null,true);

        $data['user_id'] = $user_id;

        $data[$type.'_id'] = $object['id'];

        $admins = Role::find(1)->with('users')->first();

        $this->sendNotify($admins['users'],new AddNotification($type,'Update',$data));

    }

    // public function response($notification_id,$result,Request $request)
    // {


    //     $notification = EditNotificationService::mark_as_read($notification_id);

    //     $data = $notification['data']['data'];

    //     EditNotificationService::alert_admins($notification['data']);

    //     $user_id = $data['user_id'];

    //     $destination = $notification['data']['type'];

    //     $user = GetUserService::find($user_id);

    //     if(!isset($user))
    //         throw new Exception('user not found');

    //     if($destination == 'store')
    //         $object = GetStoreService::find($data[$destination.'_id']);
    //     else
    //         $object = GetVenueService::find($data[$destination.'_id']);

    //     if($result == 'accept')
    //     {
    //         if(isset($data['name']))
    //         $object->name = $data['name'];
    //         if(isset($data['description']))
    //         $object->name = $data['description'];
    //         if(isset($data['longitude']))
    //         $object->name = $data['longitude'];
    //         if(isset($data['latitude']))
    //         $object->name = $data['latitude'];

    //         $object->save();

    //         $message = 'Hello '.$user['name'].' ! .Your request about editing '.$object['name'].' '.$destination.' has been approved.Now , your '.$destination.' has the new edited informations';
    //     }
    //     else if($result == 'reject')
    //     {
    //         $message = 'Hello '.$user['name'].' ! .Sorry , your request about editing '.$object['name'].' '.$destination.' has been rejected.' ;

    //         if(isset($request->reason))
    //             $message = $message.' '.$request->reason;
    //     }
    //     else
    //         throw new Exception('Invalid result');

    //         $this->sendNotify($user,new UserNotification($user_id,$message));

    // }

    public function response($notification_id,$result,Request $request)
    {

        $notification = EditNotificationService::mark_as_read($notification_id);

        $data = $notification['data']['data'];

        $user_id = $data['user_id'];

        $user = GetUserService::find($user_id);

        $id = null;

        if(!isset($user))
            throw new Exception('user not found');

        $destination = $notification['data']['type'];

        if($notification['data']['operation']=='Add')
        {
            $phone = $data['phone_number'];

            $exists = DB::table('phone_numbers')->where('phone_number',$phone )->exists();

            if ($exists)
            {
                throw new Exception("The $phone has already been taken.");
            }

            $name =  $data['name'];

            $rejectMessage = 'Hello '.$user['name'].' ! .Sorry , your request about adding '.$name.' '.$destination.' has been rejected.' ;

        }
        else
        {
            if($destination == 'store')
                $object = GetStoreService::find($data[$destination.'_id'],null,true);
            else
                $object = GetVenueService::find($data[$destination.'_id'],null,true);

            $rejectMessage = 'Hello '.$user['name'].' ! .Sorry , your request about editing '.$object['name'].' '.$destination.' has been rejected.' ;

        }

        EditNotificationService::alert_admins($notification['data']);


        if($result == 'accept')
        {
            if($notification['data']['operation']=='Add')
            {
                $message = 'Hello '.$user['name'].' ! .Your request about adding '.$name.' '.$destination.' has been approved.Now , your '.$destination.' does not visible for users , you have to complete your '.$destination.' information to make it visible' ;
                if($destination == 'store')
                    $object =AddStoreService::add($data);
                else
                    $object = AddVenueService::add($data);

                $object->phones()->create(['phone_number' => $phone]);

                $paths = $data['paths'];

                $id = $object->id;

                foreach($paths as $path)
                    $object->photos()->create(['path' => $path]);
            }
            else
            {
                if(isset($data['name']))
                    $object->name = $data['name'];
                if(isset($data['description']))
                    $object->description = $data['description'];
                if(isset($data['longitude']))
                    $object->longitude = $data['longitude'];
                if(isset($data['latitude']))
                    $object->latitude = $data['latitude'];
                if(isset($data['hasDelivery']) && $destination=='store')
                    $object->latitude = $data['hasDelivery'];
                if(isset($data['deliveryCost']) && $destination=='store')
                    $object->latitude = $data['deliveryCost'];

                $object->save();

                $message = 'Hello '.$user['name'].' ! .Your request about editing '.$object['name'].' '.$destination.' has been approved.Now , your '.$destination.' has the new edited informations';
            }
        }
        else if($result == 'reject')
        {
            $message = $rejectMessage;

            if(isset($request->reason))
                $message = $message.' '.$request->reason;
        }
        else
            throw new Exception('Invalid result');

        $this->sendNotify($user,new UserNotification($user_id,$message,$destination,$result,$id));

    }

    public function invitation_reply($notification_id,$result,Request $request)
    {

        $notification = EditNotificationService::mark_as_read($notification_id);

        $data = $notification['data'];

        if($data['name'] != 'placed-Invitation' && $data['name'] != 'unplaced-Invitation')
            throw new Exception('This notification not an invitation reply');

        $user = GetUserService::find();

        $type = explode('-',$data['name'])[0];

        $select = $type=='placed'?['name','description','date','start_time','capacity','user_id','id']:['name','description','date','start_time','user_id','id'];

        $with = $type=='unplaced'?['user','ticket','attenders']:['user','ticket','capacity','attenders'];

        $event = GetEventService::find($data[$data['name'].'_id'],$type,$select,$with,'accepted');

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

        $this->sendNotify($user,new UserNotification($owner['id'],$message,$data['name'],$result,$data[$data['name'].'_id']));
    }

    public function paymentNotify($user,$amount,$type)
    {
        if($type=='pay')
            $message = 'Hello '.$user['name'].' '.$amount.' is cut from your card succesfully , if your request will be rejected , we will charge your crad again';
        else if($type=='add')
            $message = 'Hello '.$user['name'].' '.$amount.' is added to your card succesfully';
        else
            $message = 'Hello '.$user['name'].' '.$amount.' is cut from your card succesfully';



        $this->sendNotify($user,new UserNotification($user->id,$message));

    }

    public function notify_all($request)
    {
        $message = ($request->validated())['message'];

        dispatch(new NotifyAllUsersJob($message));
    }

    public function sendNotify($user,$notification)
    {
        Notification::send($user,$notification);
    }
}
