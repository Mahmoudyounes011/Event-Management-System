<?php

namespace App\Services\User;

use App\Models\User;
use Exception;

class GetUserService
{

    public function all($paginate=10)
    {
        $users = User::with('wallet')->select('id', 'name', 'email')->paginate($paginate);

        if(!isset($users[0]))
            throw new Exception('There are no users');

        return $users;
    }

    public function all_for_notify()
    {
        $users = User::select('id')->get();

        if(!isset($users[0]))
            throw new Exception('There are no users');

        return $users;
    }

    public static function find($id = null)
    {
        if(isset($id))
        {
            $user = User::with('wallet')->find($id);

            if(!isset($user))
                throw new Exception('User not found');

            return $user;
        }

        return auth()->user();
    }

    public static function search($request)
    {
        $name = $request->input('name');

        if(!isset($name))
            throw new Exception('user name is required');

        $users = User::where('name','Like',$name.'%')->where('id','!=',1)->paginate($request->input('per_page')?$request->input('per_page'):10);

        if(!isset($users[0]))
            throw new Exception('User not found');

        return $users;
    }
}
