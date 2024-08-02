<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Employee\EmployeeCollection;
use App\Http\Resources\Employee\EmployeeResource;
use App\Models\Employee;
use App\Models\User;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Filters\v1\EmployeeFilter;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\UserRequest;
use App\Enums\RoleTypeEnum;
use App\Events\EmployeeCreated;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filter = new EmployeeFilter();
        $filterItems = $filter->transform($request); // [['column', 'operator', 'value']]

        try {
            if (count($filterItems) == 0) {
                return response()->json(new EmployeeCollection(Employee::with('user')->with('category')->withTrashed()->paginate(10)));
            } else {
                $collections = Employee::with('user')->with('category')->withTrashed()->where($filterItems)->paginate(10);
                return response()->json(new EmployeeCollection($collections->appends($request->query())));
            }
        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request)
    {
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->string('password')),
            ]);

            $user->assignRole(RoleTypeEnum::EMPLOYEE->value);

            $employee = Employee::create([
                'user_id' => $user->id,
                'category_id' => Category::first()->id
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            EmployeeCreated::dispatch($employee);

            return response()->json([EmployeeResource::make($employee), 'token' => $token]);
        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee)
    {
        try {
            return response()->json(new EmployeeResource($employee));
        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Employee $employee)
    {
        try {
            $employee->update($request->all());
            return response()->json(EmployeeResource::make($employee));
        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {
        try {
            $employee->delete();

            return response()->json(['message' => 'Employee Id ' . $employee->id . ' successfully deleted.']);
        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
