<?php

namespace App\Http\Controllers\Role;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoleRequest;
use App\Models\UserRole;
use App\Services\Role\GetRoleService;
use App\Services\Role\StoreRoleService;
use App\Services\User\GetUserService;
use Exception;
use Illuminate\Http\Request;

class RoleController extends Controller
{

    public function index(GetRoleService $getRoleService)
    {
        $roles = $getRoleService->getAllRoles();

        return response(['roles' => $roles]);

    }

    public function addRole(RoleRequest $request,StoreRoleService $roleService)
    {

         $role = $roleService->createRole($request);

        return response(
            [
            'status' => 'success',
            'role' => $role['role'],
            ]);

    }

    public function buy_owner_account(Request $request,StoreRoleService $roleService)
    {

        try
        {
            $roleService->convert_to_owner($request);
        }
        catch(Exception $e)
        {
            return response(
                [
                'status' => 'fail',
                'message' => $e->getMessage()
                ]);
        }
        return response(
            [
            'status' => 'success',
            'message' => 'You are owner now'
            ]);

    }
    public function make_admin($user_id)
    {
        $user = GetUserService::find($user_id);

        if(!isset($user))
            return response(['status' => 'fail','message' => 'User not found']);

        UserRole::create(['user_id' => $user_id,'role_id' => 1]);
    }

}
