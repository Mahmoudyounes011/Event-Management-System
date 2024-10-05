<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Services\Payment\GetBalanceService;
use Exception;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function get_all(Request $request,GetBalanceService $payement)
    {
        try
        {
            $payments = $payement->all($request->input('per_page')?$request->input('per_page'):20);
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
            'data' => $payments
        ]);
    }
}
