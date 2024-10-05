<?php

namespace App\Http\Controllers\Product;

use App\Http\Requests\EditRequest;
use App\Services\Product\EditProductService;
use App\Http\Controllers\Controller;
use App\Http\Requests\AddProductRequest;
use App\Models\Store;
use App\Services\Product\AddProductService;
use App\Services\Product\GetAllService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function get_all(Request $request,GetAllService $all)
    {
        try
        {
            $products = $all->all($request->input('per_page')?$request->input('per_page'):20);
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
            'data' => $products
        ]);
    }
    public function add_from_store($store_id,AddProductRequest $request,AddProductService $add)
    {
        try
        {
            $add->add($store_id,$request);
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
            'message' => 'the products are stored successfully'
        ]);
    }


    public function update(EditProductService $product,$new_price,$product_id)
    {

        try
        {
            $product->edit($product_id,$new_price);
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
            'message' => 'product is updated successfully'
        ]);
    }

    public function delete(EditProductService $product,$product_id)
    {

        try
        {
            $product->make_hidden($product_id);
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
            'message' => 'product is deleted successfully'
        ]);
    }


}
