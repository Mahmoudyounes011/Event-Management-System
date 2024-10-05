<?php
namespace App\Services\Section;

use App\Http\Requests\DeleteSectionRequest;
use App\Models\Section;
use App\Models\User;
use App\Models\Venue;
use App\Notifications\UserNotification;
use App\Services\Notification\SendNotificationService;
use App\Services\User\GetUserService;
use App\Services\User\UserVerificationService;
use Illuminate\Support\Facades\Notification;
use Exception;

class DeleteSectionService
{

    public function delete($section_id)
    {
        $section = GetSectionService::find($section_id,['venue.sections']);

        $venue = $section['venue'];

        UserVerificationService::verify($venue['user_id']);
        
        $user=GetUserService::find();

        $section->available = 0;

        $section->save();

        $message = 'Hello '.$user['name'].' your section '.$section['description'].'has been deleted succesfully.';

        if(count($venue['sections']) == 0 || (count($venue['sections']) == 1 && $venue['sections'][0]['id'] == $section['id']))
        {
            $venue->available = 0;
            $venue->save();

            $message = $message.' Your venue will be unvisible to our users , please add at least one section to make it visible';
        }

        (new SendNotificationService)->sendNotify($user,new UserNotification($user->id,$message,'Venue','Unavailable'));

    }
    // public function deleteSection($section_id)
    // {
    //     $section = GetSectionService::find($section_id,['venue']);

    //     $venue = $section['venue'];

    //     $userId =$venue->user_id;

    //     $user=GetUserService::find($userId);

    //     $section->available = 0;

    //     $section->save();

    //     $message = 'Hello '.$user['name'].' your section '.$section['description'].'has been deleted succesfully';

    //     Notification::send($user, new UserNotification($userId, $message));

    // }



}
