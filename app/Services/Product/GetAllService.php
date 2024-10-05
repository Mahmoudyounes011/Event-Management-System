<?php

namespace App\Services\Product;

use App\Models\Product;

use App\Traits\AssistentFunctions;
use Exception;

class GetAllService
{
    public static function all($paginate=20)
    {
        $products = Product::paginate($paginate);
        if(!isset($products[0]))
            throw new Exception('There are no products');

        return $products;
    }

    public static function find($ids=null)
    {
        $products = Product::whereIn('id',$ids)->get();
        if(!isset($products[0]))
            throw new Exception('There are no products');

        return $products;
    }
}
