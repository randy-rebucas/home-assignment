<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;

class AssignPermissionToRoleController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, Role $role)
    {
        $role->givePermissionTo($request->permission);

        return response()->json($role);
    }
}
