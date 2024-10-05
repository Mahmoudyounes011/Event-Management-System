<?php

namespace app\Services\User;

use App\Http\Requests\StoreUserRequest;
use App\Models\User;

class StoreUserService
{
    public static function create(StoreUserRequest $request)
    {
        $data = $request->validated();
        $data['password'] = bcrypt($data['password']);
        $user = User::create($data)->makeVisible('email');
        return $user;
    }
}
