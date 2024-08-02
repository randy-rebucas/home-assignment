<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\User\UserCollection;
use App\Http\Resources\User\UserResource;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use App\Enums\RoleTypeEnum;
use App\Filters\v1\UserFilter;

class UserController extends Controller
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
                return response()->json(new UserCollection(User::with('roles')->with('permissions')->paginate(10)));
            } else {
                $collections = User::with('roles')->with('permissions')->where($filterItems)->paginate(10);
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
        try {
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->string('password')),
            ]);

            $user->assignRole(RoleTypeEnum::EMPLOYEE);

            $token = $user->createToken('auth_token')->plainTextToken;

            event(new Registered($user));

            return response()->json([UserResource::make($user), 'token' => $token]);
        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        try {
            return response()->json(new UserResource($user));
        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        try {
            $user->update($request->all());
            return response()->json(UserResource::make($user));
        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        try {
            $user->delete();

            return response()->json(['message' => 'User Id ' . $user->id . ' successfully deleted.']);
        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function filter(Request $request)
    {
        $filter = new UserFilter();
        $filterItems = $filter->transform($request); // [['column', 'operator', 'value']]

        try {
            if (count($filterItems) == 0) {
                return response()->json(new UserCollection(User::with('roles')->with('permissions')->with('manager')->role($request->role)->paginate(10)));
            } else {
                $collections = User::with('roles')->with('permissions')->with('manager')->role($request->role)->where($filterItems)->paginate(10);
                return response()->json(new UserCollection($collections->appends($request->query())));
            }
        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
