<?php
namespace App\Services\Store;

use App\Models\Store;
use App\Services\Rating\GetRatingService;
use App\Services\User\GetUserService;
use Exception;
use Illuminate\Support\Facades\DB;

class GetStoreService
{

    public static function find($store_id,$with = null,$ignoreDeletion=null)
    {
        if(isset($with))
        {
            if(isset($ignoreDeletion) && $ignoreDeletion)
                $store = Store::withoutGlobalScope('available')->with($with)->find($store_id);
            else
                $store = Store::with($with)->find($store_id);

        }
        else
        {
            if(isset($ignoreDeletion) && $ignoreDeletion)
                $store = Store::withoutGlobalScope('available')->find($store_id);
            else
                $store = Store::find($store_id);
        }

        if(!isset($store))
            throw new Exception('Store not found');

        return $store;
    }

    public function search($name,$paginate=10)
    {
        $stores = Store::where('name', 'like',$name.'%')->with('photos','phones','times','products.product','ratings')->paginate($paginate);

        foreach($stores as $key => $store)
        {
            $times = $store['times']->groupBy('day');
            unset($stores[$key]['times']);
            $stores[$key]['times'] = $times;
            $stores[$key]['rate'] = (new GetRatingService)->get($store);
            unset($stores[$key]['ratings']);

        }

        if(!isset($stores[0]))
            throw new Exception('There is no stores has this name');

        return $stores;
    }

    public static function for_user($paginate=10)
    {
        $owner = GetUserService::find();

        $stores = $owner->stores()->with('photos','phones','times','products.product','ratings')->paginate($paginate);

        foreach($stores as $key => $store)
        {
            $times = $store->times->groupBy('day');
            unset($stores[$key]->times);
            $stores[$key]->times = $times;
            $stores[$key]['rate'] = (new GetRatingService)->get($store);
            unset($stores[$key]['ratings']);
        }

        if(!isset($stores))
            throw new Exception('This owner does not have any store');

        return $stores;
    }

    public static function all($paginate=10)
    {
        $stores = Store::with('phones','photos','times','products.product','ratings')->paginate($paginate);

        foreach($stores as $key => $store)
        {
            $times = $store->times->groupBy('day');
            unset($stores[$key]->times);
            $stores[$key]->times = $times;
            $stores[$key]['rate'] = (new GetRatingService)->get($store);
            unset($stores[$key]['ratings']);

        }
        if(!isset($stores[0]))
            throw new Exception('There are no stores');

        return $stores;
    }
}
