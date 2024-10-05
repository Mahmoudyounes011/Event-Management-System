<?php

namespace App\Services\Rating;

use App\Models\Rating;
use App\Models\Venue;
use App\Models\Store;
use App\Models\User;
use App\Services\Store\GetStoreService;
use App\Services\Venue\GetVenueService;
use Exception;

class GetRatingService
{
    public function get($object)
    {
        $ratings = $object['ratings'];

        if(count($ratings)==0)
            return 0;

        $total_rating = 0;

        foreach($ratings as $rating)
            $total_rating += $rating->pivot->stars;

        return $total_rating/count($ratings);

    }
}
