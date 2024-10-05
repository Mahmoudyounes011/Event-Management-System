<?php

namespace App\Services\Payment;

use App\Models\Payment;
use App\Models\User;
use App\Models\Wallet;
use App\Services\User\GetUserService;
use Exception;

class GetBalanceService
{

    public static function get($user = null)
    {

        $wallet = isset($user) ? $user->wallet : (GetUserService::find())->wallet;

        return $wallet;
    }

    public static function all($paginate=20)
    {

        $payements = Payment::with(['payer','payee'])->paginate($paginate);

        if(!isset($payements[0]))
            throw new Exception('There are no payments');

        return $payements;
    }

}
