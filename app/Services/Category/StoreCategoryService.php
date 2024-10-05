<?php

namespace App\Services\Category;

use App\Models\Category;
use Exception;

class StoreCategoryService
{
    public static function add($request)
    {
        $name = $request->validated();

        Category::create($name);
    }
}
