<?php
namespace  App\Services\Feedback;

use App\Models\Feedback;
use App\Models\Role;
use App\Notifications\AdminNotification;
use App\Services\Notification\SendNotificationService;
use App\services\User\GetUserService;
use Exception;

class GetFeedbackService
{
    public function find($feedback_id)
    {
        $feedback = Feedback::find($feedback_id);

        if(!isset($feedback))
            throw new Exception('Feedback not found');

        return $feedback;
    }
    public function all($paginate=10)
    {

        $feedbacks = Feedback::with('user')->where('isRead',0)->select(['id','user_id','content','created_at'])->paginate($paginate);

        if(!isset($feedbacks[0]))
            throw new Exception('There are no feedbacks');

        return $feedbacks;
    }

}

