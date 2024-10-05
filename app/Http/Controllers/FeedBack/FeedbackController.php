<?php

namespace App\Http\Controllers\FeedBack;

use App\Http\Controllers\Controller;
use App\Http\Requests\FeedbackRequest;
use App\Services\Feedback\DeleteFeedbackService;
use App\services\Feedback\feedbackService;
use App\Services\Feedback\GetFeedbackService;
use App\Services\Feedback\StoreFeedbackService;
use Exception;
use Illuminate\Http\Request;
class FeedbackController extends Controller
{
    public function store(FeedbackRequest $request, StoreFeedbackService $feedbackService)
    {
        try
        {

            $feedbackService->storeFeedback($request);
        }
        catch(Exception $e)
        {
            return response([
                'status' => 'fail',
                'message' => $e->getMessage()
            ]);
        }

        return response([
            'status' => 'success',
            'message' => 'Feedback submitted successfully',
        ]);
    }

    public function get_all_feedbacks(Request $request,GetFeedbackService $feedback)
    {
        try
        {
            $feedbacks = $feedback->all($request->input('per_page')?$request->input('per_page'):10);
        }
        catch(Exception $e)
        {
            return response(['status' => 'fail','message' => $e->getMessage()]);
        }

        return response(['status' => 'success','data' =>  $feedbacks]);
    }

    public function mark_as_read(DeleteFeedbackService $feedback,$feedback_id)
    {
        try
        {
            $feedbacks = $feedback->delete($feedback_id);
        }
        catch(Exception $e)
        {
            return response(['status' => 'fail','message' => $e->getMessage()]);
        }

        return response(['status' => 'success','message' => 'the feedback is deleted successfuy']);
    }

}
