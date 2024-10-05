<?php

namespace App\Http\Controllers\Rating;

use App\Http\Controllers\Controller;
use App\Http\Requests\RatingRequest;
use App\Services\Rating\RatingService;
use Illuminate\Http\Request;
use App\Models\User;
use App\Services\Rating\GetRatingService;
use Exception;
use Illuminate\Support\Facades\Auth;

class RatingController extends Controller
{

    public function store(RatingRequest $request,RatingService $ratingService, $type, $id)
    {
        try
        {
            $ratingService->store($request,$id,$type);
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
            'message' => 'Rating is suuccessfully done'

        ]);
    }

}
