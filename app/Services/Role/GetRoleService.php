<?php

namespace App\Services\Role;

use App\Models\Role;

class GetRoleService
{
    public function getAllRoles()
    {
        return Role::all();
    }
}
