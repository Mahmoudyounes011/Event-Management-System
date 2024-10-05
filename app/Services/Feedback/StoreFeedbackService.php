<?php
namespace  App\services\Feedback;

use App\Models\Role;
use App\Notifications\AdminNotification;
use App\Services\Notification\SendNotificationService;
use App\Services\User\GetUserService;

class StoreFeedbackService
{
    public function storeFeedback($request)
    {

        $content = $request->validated();

        $user = GetUserService::find();

        $user->feedbacks()->create($content);
    }

}

