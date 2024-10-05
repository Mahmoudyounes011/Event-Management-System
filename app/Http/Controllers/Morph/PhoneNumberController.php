<?php

namespace App\Http\Controllers\Morph;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddPhoneRequest;
use App\Services\Phone\EditPhoneService;
use App\Services\Phone\DeletePhoneService;
use App\Services\Phone\StorePhoneService;
use Exception;
use Illuminate\Http\Request;

class PhoneNumberController extends Controller
{
    public function store(AddPhoneRequest $request,$type,$object_id)
    {
        try
        {
            (new StorePhoneService)->store($request,$type,$object_id);
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
            'message' => 'Phone number is added successfully'
        ]);
    }

    public function edit(AddPhoneRequest $request,$type,$phone_id)
    {
        try
        {
            (new EditPhoneService)->edit($request,$type,$phone_id);
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
            'message' => 'Phone number is edited successfully'
        ]);
    }
    public function delete($type,$phone_id)
    {
        try
        {
            (new DeletePhoneService)->delete($type,$phone_id);
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
            'message' => 'Phone number is deleted successfully'
        ]);
    }
}
