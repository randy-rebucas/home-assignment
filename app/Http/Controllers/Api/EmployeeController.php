<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use App\Enums\RoleTypeEnum;
use App\Http\Resources\User\UserCollection;
use App\Filters\v1\UserFilter;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filter = new UserFilter();
        $filterItems = $filter->transform($request); // [['column', 'operator', 'value']]

        try {
            if (count($filterItems) == 0) {
                return response()->json(new UserCollection(User::with('roles')->with('permissions')->with('employee')->role(RoleTypeEnum::EMPLOYEE->value)->paginate(10)));
            } else {
                $collections = User::with('roles')->with('permissions')->with('employee')->role(RoleTypeEnum::EMPLOYEE->value)->where($filterItems)->paginate(10);
                return response()->json(new UserCollection($collections->appends($request->query())));
            }
        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Employee $employee)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {
        //
    }
}
