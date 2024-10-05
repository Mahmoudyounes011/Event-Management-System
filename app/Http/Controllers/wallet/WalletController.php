<?php

namespace App\Http\Controllers\wallet;

use App\Http\Controllers\Controller;
use App\Http\Requests\BalanceReqeust;
use App\Models\User;
use Illuminate\Http\Request;
use App\Services\User\BalanceUserService;
use App\Http\Controllers\wallet\Auth;
use App\Services\Payment\GetBalanceService;
use App\Services\Payment\UpdateBalanceService;
use Exception;

class WalletController extends Controller
{

    public function updateBalance(BalanceReqeust $request,$user_id, UpdateBalanceService $walletService)
    {
        try
        {
            $walletService->add($request,$user_id);
        }
        catch(Exception $e)
        {
            return response([
                'status' => 'fail',
                'message' => $e->getMessage()
            ]);
        }
        return response([
            'status' => 'success',

        ]);
    }

    public function get(GetBalanceService $walletService)
    {
        try
        {
            $wallet = $walletService->get();
        }
        catch(Exception $e)
        {
            return response([
                'status' => 'fail',
                'message' => $e->getMessage()
            ]);
        }
        return response([
            'status' => 'success',
            'data' => $wallet

        ]);
    }

}
