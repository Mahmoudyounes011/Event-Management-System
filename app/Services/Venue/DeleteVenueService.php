<?php
namespace App\Services\Venue;

use App\Models\User;
use App\Models\Venue;
use App\Notifications\UserNotification;
use App\Services\Notification\SendNotificationService;
use App\Services\User\GetUserService;
use Illuminate\Support\Facades\Notification;
class DeleteVenueService
{
    public function deleteVenueAndSections($venue_id)
    {

        $venue = GetVenueService::find($venue_id);

        $userId = $venue->user_id;

        $user = GetUserService::find($userId);

        $venue->available = 0;

        $venue->save();

        $message = 'Hello '.$user['name'].' your venue '.$venue['name'].' has been deleted succesfully';

        (new SendNotificationService)->sendNotify($user,new UserNotification($user,$message,'Venue','Unavailable'));

    }
}
