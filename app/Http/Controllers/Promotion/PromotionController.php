<?php

namespace App\Http\Controllers\Promotion;

use App\Http\Controllers\Controller;
use App\Services\Promotion\GetPromotionService;
use App\Services\Promotion\PromoteService;
use Exception;
use Illuminate\Http\Request;

class PromotionController extends Controller
{
    public function get_all(GetPromotionService $all)
    {
        try
        {
            $promotions = $all->all();
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
            'data' => $promotions
        ]);
    }

    public function promote($promotion_id,$event_id,$event_type,PromoteService $promote,Request $request)
    {
        try
        {
            $promote->promote($request,$promotion_id,$event_id,$event_type);
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
            'data' => 'The event is promoted successfully'
        ]);
    }
}
