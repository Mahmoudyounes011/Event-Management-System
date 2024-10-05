<?php

namespace App\Services\Image;

use App\Services\Event\GetEventService;
use App\Services\Product\GetAllService;
use App\Services\Store\GetStoreService;
use App\Services\Venue\GetVenueService;
use App\Traits\AssistentFunctions;

class ImageService
{
    use AssistentFunctions;

    public function upload_image($images,$type,$object_id = null)
    {
        $object = null;

        if(isset($object_id))
            $object = $this->get_object($type,$object_id,true);

        $paths = [];

        foreach($images as $image)
        {
            if(isset($object))
                $paths[] = ['imagable_id' => $object->id,'imagable_type' => 'App\Models\\'.$type,'path' => $image->store($type, 'public')];
            else
                $paths[] = $image->store($type, 'public');
        }

        if(isset($object))
        {
            $object->photos()->insert($paths);
            return;
        }

        return $paths;
    }
}


