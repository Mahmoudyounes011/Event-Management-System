<?php
namespace App\Services\Notification;

use App\Models\Role;
use App\Services\User\GetUserService;
use Exception;

class EditNotificationService
{
    public static function mark_as_read($id)
    {
        $user = GetUserService::find();

        $notification = $user?->unreadNotifications?->find($id);

        $notification?->markAsRead();

        if(!(isset($notification)))
            throw new Exception('There is no notification has this id');

        return $notification;
    }

    public static function alert_admins($data)
    {
        $admins = Role::find(1)->with('users')->first();

        foreach($admins['users'] as $admin)
        {
            $admin->unreadNotifications?->where('data',$data)?->markAsRead();
        }

    }
}
