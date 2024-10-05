<?php

namespace App\Services\Phone;

use App\Traits\AssistentFunctions;

class StorePhoneService
{
    use AssistentFunctions;

    public function store($request,$type,$object_id)
    {
        $object = $this->get_object($type,$object_id,true);

        $phone = $request->validated();

        $object->phones()->create($phone);
    }
}


