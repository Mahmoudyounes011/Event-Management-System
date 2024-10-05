<?php
namespace  App\Services\Feedback;

use App\Models\Feedback;
use Exception;

class DeleteFeedbackService
{
    public function delete($feedback_id)
    {
        $feedback = (new GetFeedbackService)->find($feedback_id);

        $feedback->isRead = 1;

        $feedback->save();
    }

}

