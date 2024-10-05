<?php

namespace App\Services\User;

use App\Http\Requests\BalanceReqeust;
use App\Models\User;
use App\Models\Wallet;
use App\Notifications\UserNotification;
use Exception;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\DB;

class BalanceUserService
{


    public function updateBalance($userId, $balance)
    {

        $user = User::findOrFail($userId);
        $wallet = $user->wallet ?? new Wallet();
        $wallet->balance += $balance;

        $user->wallet()->save($wallet);

        $message = 'Hello '.$user['name'].' '.$balance.' is added to your wallet succesfully';

        Notification::send($user, new UserNotification($userId, $message));


    }
}
