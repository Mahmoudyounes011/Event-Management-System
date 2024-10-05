<?php

namespace App\Http\Controllers\Morph;

use App\Http\Controllers\Controller;
use App\Http\Requests\EditTimeRequest;
use App\Http\Requests\TimeScheduleRequest;
use App\Services\Time\DeleteTimeScheduleService;
use App\Services\Time\EditTimeScheduleService;
use App\Services\Time\StoreTimeScheduleService;
use Exception;

class TimeController extends Controller
{
    public function add_time_schedule($type,$id,TimeScheduleRequest $request)
    {
        try
        {
            StoreTimeScheduleService::store($request,$type,$id);
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
            'data' => 'TimeSchedule is added successfuly'
        ]);
    }

    public function edit($type,$time_id,EditTimeRequest $request)
    {
        try
        {
            EditTimeScheduleService::edit($request,$type,$time_id);
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
            'data' => 'Time is edited successfuly'
        ]);
    }

    public function delete($time_id)
    {
        try
        {
            DeleteTimeScheduleService::delete($time_id);
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
            'data' => 'Time is deleted successfuly'
        ]);
    }
}
