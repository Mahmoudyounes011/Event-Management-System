<?php

namespace App\Services\Promotion;

use App\Models\Promotion;
use Exception;

class GetPromotionService
{
    public function all()
    {
        return Promotion::all();
    }

    public function find($id)
    {
        $promotion = Promotion::find($id);

        if(!isset($promotion))
            throw new Exception('Promotion not found');

        return $promotion;
    }
}
