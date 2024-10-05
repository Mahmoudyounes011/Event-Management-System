<?php

namespace App\Services\Payment;

use App\Models\User;
use App\Services\Notification\SendNotificationService;
use App\Services\User\GetUserService;
use Exception;

class UpdateBalanceService
{

    public static function pay($user = null,$cost,$owner,$notUser=null)
    {
        $user = isset($user) ? $user : GetUserService::find();

        $wallet = GetBalanceService::get($user);
        UpdateBalanceService::check($wallet,$cost);

        // dd($owner);
        $wallet->balance -= $cost;

        $wallet->save();

        $wallet = GetBalanceService::get($owner);

        $wallet->balance += $cost;

        $wallet->save();

        if(isset($notUser) && $notUser)
            $owner = $owner['owner'];

        $user->payments()->attach($owner,['amount' => $cost,'date' => now()]);

        (new SendNotificationService)->paymentNotify($user,$cost,'pay');

        // if($owner->id != 1)
        (new SendNotificationService)->paymentNotify($owner,$cost,'add');

        return $wallet;
    }

    public function add($request,$user_id)
    {
        $balance = ($request->validated())['balance'];

        $user = GetUserService::find($user_id);

        $wallet = $user->wallet;

        $wallet->balance += $balance;

        $wallet->save();

        $admin = GetUserService::find(1);

        $admin->payments()->attach($user,['amount' => $balance,'date' => now()]);

        (new SendNotificationService)->paymentNotify($user,$balance,'add');
    }

    public function addAfterReject($balance,$user,$owner,$notUser=null)
    {
        $wallet = $user->wallet;

        $wallet->balance += $balance;

        $wallet->save();

        $wallet = $owner->wallet;

        $wallet->balance -= $balance;

        $wallet->save();

        if(isset($notUser) && $notUser)
            $owner = $owner['owner'];

        $owner->payments()->attach($user,['amount' => $balance,'date' => now()]);

        (new SendNotificationService)->paymentNotify($user,$balance,'add');



        (new SendNotificationService)->paymentNotify($owner,$balance,'sub');
    }

    private static function check($wallet,$cost)
    {
        return $wallet->balance>=$cost ? $wallet : throw new Exception('You heve not enough money to create this event , please charge your card and try again');
    }
}
