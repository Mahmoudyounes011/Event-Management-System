<?php

namespace App\Services\Rating;

use App\Models\Rating;
use App\Models\Venue;
use App\Services\Store\GetStoreService;
use App\Services\User\GetUserService;
use App\Services\Venue\GetVenueService;
use Exception;

class RatingService
{
    public function store($request,$id,$type)
    {
        $data = $request->validated();

        $user = GetUserService::find();

        $object = match($type)
        {
            'venue' => GetVenueService::find($id),
            'store' => GetStoreService::find($id),
            default => throw new Exception('Invalid type')
        };

        $user_rating = match($type)
        {
            'venue' => $user->rate_venues($object->id)->get(),
            'store' => $user->rate_stores($object->id)->get(),
            default => throw new Exception('Invalid type')
        };

        if(isset($user_rating[0]))
        {
            if($user_rating[0]->stars != $data['stars'])
            {
                $user_rating[0]->stars = $data['stars'];
                $user_rating[0]->save();
            }
            else
                throw new Exception('This is the same for your last rate');
        }
        else
            $object->ratings()->attach($user,['stars' => $data['stars']]);
    }

}
