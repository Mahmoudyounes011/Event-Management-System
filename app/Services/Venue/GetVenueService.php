<?php
namespace App\Services\Venue;

use App\Models\Venue;
use App\Services\Rating\GetRatingService;
use App\Services\User\GetUserService;
use Exception;

class GetVenueService
{
    public static function find($venue_id,$with=null,$ignoreDeletion=null)
    {
        if(isset($with))
        {
            if(isset($ignoreDeletion) && $ignoreDeletion)
                $venue = Venue::withoutGlobalScope('available')->with($with)->find($venue_id);
            else
                $venue = Venue::with($with)->find($venue_id);

        }
        else
        {
            if(isset($ignoreDeletion) && $ignoreDeletion)
                $venue = Venue::withoutGlobalScope('available')->find($venue_id);
            else
                $venue = Venue::find($venue_id);
        }

        if(!isset($venue))
            throw new Exception('Venue not found');

        return $venue;
    }

    public function search($name,$paginate=10)
    {
        $venues = Venue::where('name', 'like',$name.'%')->with('photos','phones','times','ratings')->paginate($paginate);

        foreach($venues as $key => $venue)
        {
            $times = $venue['times']->groupBy('day');
            unset($venues[$key]['times']);
            $venues[$key]['times'] = $times;
            $venues[$key]['rate'] = (new GetRatingService)->get($venue);
            unset($venues[$key]['ratings']);


        }
        if(!isset($venues[0]))
            throw new Exception('There is no venues has this name');

        return $venues;
    }

    public static function for_user($paginate=10)
    {
        $owner = GetUserService::find();

        $venues = $owner->venues()->with('photos','phones','times')->paginate($paginate);

        foreach($venues as $key => $venue)
        {
            $venues[$key]['rate'] = (new GetRatingService)->get($venue);
            unset($venues[$key]['ratings']);
        }

        if(!isset($venues))
            throw new Exception('This owner does not have any venue');

        return $venues;
    }

    public static function all($paginate=10)
    {
        $venues = Venue::with('phones','photos','times','wallet')->paginate($paginate);

        foreach($venues as $key => $venue)
        {
            $times = $venue->times->groupBy('day');
            unset($venues[$key]->times);

            $t=[];
            foreach($times as $time)
                $t[] = $time;

            $venues[$key]->times = $t;
            $venues[$key]['rate'] = (new GetRatingService)->get($venue);
            unset($venues[$key]['ratings']);
        }

        if(!isset($venues[0]))
            throw new Exception('There are no venues');

        return $venues;
    }

    public static function times_of_day(Venue $venue)
    {
        $venues = Venue::with('phones','photos','times')->paginate(10);

        if(!isset($venues[0]))
            throw new Exception('There are no venues');

        return $venues;
    }


}
