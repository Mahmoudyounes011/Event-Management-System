<?php

namespace App\Traits;

use App\Services\Event\GetEventService;
use App\Services\Product\GetAllService;
use App\Services\Section\GetSectionService;
use App\Services\Store\GetStoreService;
use App\Services\User\UserVerificationService;
use App\Services\Venue\GetVenueService;
use Exception;

trait AssistentFunctions
{

    public function get_object($type,$object_id,$auth=null)
    {
        $object = match($type)
        {
            'Venue' => GetVenueService::find($object_id,null,true),
            'Section' => GetSectionService::find($object_id,['venue'],true),
            'Store' => GetStoreService::find($object_id,true),
            'Product' => GetAllService::find($object_id),
            'Event' => GetEventService::find($object_id,'placed',null),
            'PublicEvent' => GetEventService::find($object_id,'unplaced',null),
            default => throw new Exception('Invalid type')
        };

        $owner_id = match($type)
        {

            'Venue' => $object['user_id'],
            'Section' => $object['venue']['user_id'],
            'Store' => $object['user_id'],
            'Event' => $object['user_id'],
            'Public_event' => $object['user_id'],
        };

        if(isset($auth))
            UserVerificationService::verify($owner_id);

        return $object;
    }
    public function opposite($array1,$array2,$_3d,$_2d,$message,$array3 = null)
    {

        foreach($array1 as $key1 => $sub_array1)
        {
            if(!isset($array2[$key1]) && (isset($array3) && !isset($array3[$key1])))
                throw new Exception($message);

            if(isset($array2[$key1]) && isset($array3) && isset($array3[$key1]))
                throw new Exception($message);

            if(!isset($array2[$key1]) && !isset($array3))
                throw new Exception($message);

            if($_2d)
            {
                foreach($sub_array1 as $key2 => $_)
                    if(!isset($array2[$key1][$key2]))
                        throw new Exception($message);
            }

            if($_3d)
            {
                foreach($sub_array1 as $key2 => $sub_sub_array1)
                {
                    if(!isset($array2[$key1][$key2]))
                        throw new Exception($message);

                    foreach($sub_sub_array1 as $key3 => $_)
                    {
                        if(!isset($array2[$key1][$key2][$key3]))
                            throw new Exception($message);
                    }
                }
            }
        }

    }
}

?>
