<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Http\Requests\DeleteStoreRequest;
use App\Http\Requests\EditRequest;
use App\Models\Store;
use App\Services\Store\DeleteStoreService;
use App\Services\Store\EditStoreService;
use App\Services\Store\GetStoreService;
use App\Services\Store\SearchStoreService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;




class StoreController extends Controller
{


    public function get_owner_stores(Request $request,GetStoreService $stores)
    {
        try
        {
            $stores = $stores->for_user($request->input('per_page')?$request->input('per_page'):10);
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
            'data' => $stores
        ]);
    }

    public function get_all(Request $request,GetStoreService $stores)
    {
        try
        {
            $stores = $stores->all($request->input('per_page')?$request->input('per_page'):10);
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
            'data' => $stores
        ]);
    }

    public function search($name,Request $request,GetStoreService $search)
    {
        try
        {
            $stores = $search->search($name,$request->input('per_page')?$request->input('per_page'):10);
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
            'data' => $stores
        ]);
    }

    public function deletestore( DeleteStoreService $section,$store_id)
    {
        try
        {
            $section->deleteStore($store_id);
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

        ]);
    }

    public function update(EditRequest $request,EditStoreService $store,$store_id)
    {
        try
        {
            $store->EditStore($store_id, $request);

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

        ]);
    }

}
