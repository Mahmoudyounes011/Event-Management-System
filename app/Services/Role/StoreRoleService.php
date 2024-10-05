<?php

namespace App\Services\Role;

use App\Http\Requests\RoleRequest;
use App\Models\Role;
use App\Models\UserRole;
use App\Services\Payment\UpdateBalanceService;
use App\Services\User\GetUserService;
use Exception;
use Illuminate\Support\Facades\Hash;

class StoreRoleService
{
    public function createRole(RoleRequest $request)
    {
        $data = $request->validated();
        $role = Role::create([
            'role' => $request->role
        ]);
        return $role;
    }

    public function convert_to_owner($request,$user_id=null)
    {
        $user = GetUserService::find();

        if(!isset($user))
            throw new Exception('User not found');

        if($request->password==null)
        throw new Exception('You must enter your password');


        if($request->password!=null && !Hash::check($request->password,$user['password']))
            throw new Exception('Wrong password');

        if(isset($user->roles[0]) && $user->roles[0]['role'] == 'owner' || isset($user->roles[1]) && $user->roles[1]['role'] == 'owner')
            throw new Exception('You are already owner');

        UpdateBalanceService::pay($user,20,GetUserService::find(1));

        UserRole::create(['user_id' => $user->id,'role_id' => 2]);

    }

}
