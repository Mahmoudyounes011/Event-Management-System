<?php
namespace App\Services\Notification;

use App\Services\User\GetUserService;
use Exception;

class GetNotificationService
{

    public static function get_all($paginate=5)
    {
        $user = GetUserService::find();

        $notifications = $user?->unreadNotifications()?->paginate($paginate);

        if(!(isset($notifications)))
            throw new Exception('There are no notifications');

        return $notifications;
    }
}
