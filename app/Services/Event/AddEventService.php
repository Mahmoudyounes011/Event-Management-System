<?php

namespace App\services\Event;

use App\Models\Event;
use App\Models\Level;
use App\Models\Section;
use App\Models\SectionCategory;
use App\Notifications\UserNotification;
use App\Services\Image\ImageService;
use App\Services\Notification\SendNotificationService;
use App\Services\Payment\UpdateBalanceService;
use App\Services\Section\GetSectionService;
use App\Services\User\GetUserService;
use Exception;
use Illuminate\Support\Facades\Hash;

class AddEventService
{
    public function add_placed($request,$section_id)
    {
        $data = $request->validated();

        if(isset($data['cost']) && $data['privacy']=='private')
            throw new Exception('Private events can not be payed');

        if($request->password==null)
            throw new Exception('You must enter your password');

        $user = GetUserService::find();

        if($request->password!=null && !Hash::check($request->password,$user['password']))
            throw new Exception('Wrong password');

        $section = GetSectionService::find($section_id,['venue.owner','venue.wallet'],['id','venue_id','capacity','price']);

        // if($user['id'] == $section['venue']['owner']['id'])
        //     throw new Exception('You can not book within your venue');

        $this->capacity($section->capacity,$data['capacity']);

        $level_id = $request->input('level_id');

        $level = isset($level_id) ? Level::find($level_id) : null;

        $cost = isset($level) ? $section->price+$level->price : $section->price;

        $data['user_id'] = $user->id;

        $data['end_time'] = date('H:i',strtotime($data['start_time'].$data['period'].' hour'));

        unset($data['period']);

        $event = $section->events()->create($data);

        UpdateBalanceService::pay($user,$cost,$section->venue);

        $images = $request->file('images');

        if(isset($images))
        {
            $paths = (new ImageService)->upload_image($images,'Event');
            foreach($paths as $path)
                $event->photos()->create(['path' => $path]);
        }

        if(isset($level))
            $event->pivot()->create(['level_id'=>$level->id]);
        if(isset($data['cost']))
            $event->ticket()->create(['price'=>$data['cost']]);

        $owner = $section['venue']['owner'];

        $message = 'Someone sent you an request , check your inbox to display the event';

        (new SendNotificationService)->sendNotify($owner,new UserNotification($owner->id,$message,'Event','Pending'));

    }

    public function add_unplaced($request)
    {
        $data = $request->validated();

        if(isset($data['cost']) && $request->password==null)
            throw new Exception('You must enter your password');

        $user = GetUserService::find();

        if($request->password!=null && !Hash::check($request->password,$user['password']))
            throw new Exception('Wrong password');

        $event = $user->public_events()->create($data);

        $images = $request->file('images');

        if(isset($images))
        {
            $paths = (new ImageService)->upload_image($images,'Event');
            foreach($paths as $path)
                $event->photos()->create(['path' => $path]);
        }

        if(isset($data['capacity']))
            $event->capacity()->create(['capacity'=>$data['capacity']]);
        if(isset($data['cost']))
            $event->ticket()->create(['price'=>$data['cost']]);

    }

    private function capacity($section_capacity,$capacity)
    {
        if($capacity>$section_capacity)
            throw new Exception('Overload of '.$section_capacity);
    }
}
