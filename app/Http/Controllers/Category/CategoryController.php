<?php

namespace App\Http\Controllers\Category;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddCategoryRequest;
use App\Services\Category\GetCategoryService;
use App\Services\Category\StoreCategoryService;
use Exception;

class CategoryController extends Controller
{
    public function add(AddCategoryRequest $request,StoreCategoryService $category)
    {
        try
        {
            $categories = $category->add($request);
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
            'data' => $categories
        ]);
    }
    public function get_all(GetCategoryService $category)
    {
        try
        {
            $categories = $category->all();
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
            'data' => $categories
        ]);
    }
}
