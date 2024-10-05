<?php

namespace App\Services\Payment;

use App\Models\User;
use App\Services\User\GetUserService;
use Exception;

class CreateWalletService
{

    public static function add($user = null)
    {
        $user->wallet()->create(['balance' => 1000000000]);
    }

}
