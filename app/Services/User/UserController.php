<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\ForgetPasswordRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Services\User\DeleteUserService;
use App\Services\User\GetUserService;
use Exception;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function get_all_users(Request $request,GetUserService $user)
    {
        try
        {
            $users = $user->all($request->input('per_page')?$request->input('per_page'):10);
        }
        catch(Exception $e)
        {
            return response(['status' => 'fail','message' => $e->getMessage()]);
        }

        return response(['status' => 'success','data' =>  $users]);
    }

    public function search(GetUserService $user,Request $request)
    {
        try
        {
            $users = $user->search($request);
        }
        catch(Exception $e)
        {
            return response(['status' => 'fail','message' => $e->getMessage()]);
        }

        return response(['status' => 'success','data' =>  $users]);
    }

    public function delete($user_id,DeleteUserService $user)
    {
        try
        {
            $users = $user->delete($user_id);
        }
        catch(Exception $e)
        {
            return response(['status' => 'fail','message' => $e->getMessage()]);
        }

        return response(['status' => 'success','message' =>  'User deleted successfuly']);
    }

    public function reset_password(ResetPasswordRequest $request, $password)
    {

        try
        {
            $result = $password->verify_code($request);

            return response([
                'status' => 'success',
                'data' => ['verified' => $result]
            ]);
        }
        catch(Exception $e)
        {
            return response([
                'status' => 'fail',
                'message' => $e->getMessage()
            ]);
        }

    }
}
