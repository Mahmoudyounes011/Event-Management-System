<?php
namespace App\Services\Store;

use App\Http\Requests\DeleteSectionRequest;
use App\Models\Section;
use App\Models\Store;
use App\Models\User;
use App\Models\Venue;
use App\Notifications\UserNotification;
use App\Services\Notification\SendNotificationService;
use App\Services\User\GetUserService;
use Illuminate\Support\Facades\Notification;
use Exception;

class DeleteStoreService
{

    public function deleteStore($store_id)
    {
        $store = GetStoreService::find($store_id);

        $userId =$store->user_id;

        $user=GetUserService::find($userId);

        $store->available = 0;

        $store->save();

        $message = 'Hello '.$user['name'].' your store '.$store['name'].'has been deleted succesfully';

        (new SendNotificationService)->sendNotify($user,new UserNotification($userId,$message,'Store','Unavailable'));


    }



}
