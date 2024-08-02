<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Manager;
use App\Models\User;
use Illuminate\Http\Request;
use App\Filters\v1\ManagerFilter;
use App\Http\Requests\UserRequest;
use App\Http\Resources\Manager\ManagerCollection;
use Illuminate\Support\Facades\Hash;
use App\Enums\RoleTypeEnum;
use App\Http\Resources\Manager\ManagerResource;

class ManagerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filter = new ManagerFilter();
        $filterItems = $filter->transform($request); // [['column', 'operator', 'value']]

        try {
            if (count($filterItems) == 0) {
                return response()->json(new ManagerCollection(Manager::with('user')->withTrashed()->paginate(10)));
            } else {
                $collections = Manager::with('user')->withTrashed()->where($filterItems)->paginate(10);
                return response()->json(new ManagerCollection($collections->appends($request->query())));
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

            $user->assignRole(RoleTypeEnum::MANAGER->value);

            $manager = Manager::create([
                'user_id' => $user->id
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([ManagerResource::make($manager), 'token' => $token]);
        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Manager $manager)
    {
        try {
            return response()->json(new ManagerResource($manager));
        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Manager $manager)
    {
        try {
            $manager->update($request->all());
            return response()->json(ManagerResource::make($manager));
        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Manager $manager)
    {
        try {
            $manager->delete();
            return response()->json(['message' => 'Manager Id ' . $manager->id . ' successfully deleted.']);
        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
