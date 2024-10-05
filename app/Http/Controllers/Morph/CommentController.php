<?php

namespace App\Http\Controllers\Morph;

use App\Http\Controllers\Controller;
use App\Http\Requests\CommentRequest;
use App\Services\Comment\GetCommentService;
use App\Services\Comment\StoreCommentService;
use Exception;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function add($type,$event_id,CommentRequest $request)
    {
        try
        {
            StoreCommentService::store($request,$type,$event_id);
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
            'message' => 'The comment is added successfully'
        ]);
    }

    public function get_all($type,$event_id,Request $request)
    {
        try
        {
            $comments = GetCommentService::all($type,$event_id,$request->input('per_page')?$request->input('per_page'):20);
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
            'data' => $comments
        ]);
    }
}
