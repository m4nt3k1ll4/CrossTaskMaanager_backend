<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AppUser;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\AppUserResource;
use Validator;

class AppUserController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:app_users',
            'password' => 'required|string|min:8',
            'role_id' => 'required|exists:roles,id',
            'headquarter_id' => 'nullable|exists:headquarters,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
            'headquarter_id' => $request->role_id == 3 ? $request->headquarter_id : null,
        ];

        $user = AppUser::create($data);

        $role = Role::find($request->role_id);
        $scopes = json_decode($role->scopes);

        $token = $user->createToken('LaravelAuthApp', $scopes)->accessToken;

        return response()->json(['token' => $token, 'user' => $user], 201);
    }

    public function index()
    {
        $users = AppUser::with('role')->get();
        return response()->json(AppUserResource::collection($users), 200);
    }


    /**
     * Display the specified user.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $user = AppUser::with('role')->find($id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        return response()->json(new AppUserResource($user), 200);
    }

    /**
     * Update the specified user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $user = AppUser::find($id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:app_users,email,' . $id,
            'password' => 'sometimes|string|min:8',
            'role_id' => 'sometimes|exists:roles,id',
            'headquarter_id' => 'nullable|exists:headquarters,id', 
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        if ($request->has('name')) {
            $user->name = $request->name;
        }

        if ($request->has('email')) {
            $user->email = $request->email;
        }

        if ($request->has('password')) {
            $user->password = Hash::make($request->password);
        }

        if ($request->has('role_id')) {
            $user->role_id = $request->role_id;

            if ($request->role_id != 3) {
                $user->headquarter_id = null;
            }
        }

        if ($request->has('headquarter_id') && $user->role_id == 3) {
            $user->headquarter_id = $request->headquarter_id;
        }

        $user->save();

        return response()->json(['message' => 'User updated successfully', 'user' => $user], 200);
    }
        public function destroy($id)
    {
        $user = AppUser::find($id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted successfully'], 200);
    }

}
