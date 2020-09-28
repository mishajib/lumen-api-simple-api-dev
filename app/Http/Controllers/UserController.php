<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function index()
    {
        $users = app('db')->table('users')->get();
        return response()->json($users);
    }

    public function create(Request $request)
    {
        try {
            $this->validate($request, [
                'name'     => 'required',
                'username' => 'required|min:4|unique:users',
                'email'    => 'required|email|unique:users',
                'password' => 'required|min:6',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                                        'success' => false,
                                        'message' => $e->getMessage()
                                    ], 422);
        }


        try {
            $id   = app('db')->table('users')->insertGetId([
                                                               'name'       => trim($request->input('name')),
                                                               'username'   => strtolower(trim($request->input('username'))),
                                                               'email'      => strtolower(trim($request->input('email'))),
                                                               'password'   => app('hash')->make($request->input('password')),
                                                               'created_at' => Carbon::now(),
                                                               'updated_at' => Carbon::now()

                                                           ]);
            $user = app('db')->table('users')->select('name', 'username', 'email')->where('id', $id)->first();
            return response()->json([
                                        'id'       => $id,
                                        'name'     => $user->name,
                                        'username' => $user->username,
                                        'email'    => $user->email,
                                    ], 201);
        } catch (\PDOException $e) {
            return response()->json([
                                        'success' => false,
                                        'message' => $e->getMessage()
                                    ], 400);
        }
    }

    public function authenticate(Request $request)
    {
        // Validation
        try {
            $this->validate($request, [
                'email'    => 'required|email',
                'password' => 'required|min:6',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                                        'success' => false,
                                        'message' => $e->getMessage()
                                    ], 422);
        }
        $token = app('auth')->attempt($request->only('email', 'password'));
        if ($token) {
            return response()->json([
                                        'success' => true,
                                        'message' => "User Authenticated",
                                        'token'   => $token,
                                    ]);
        }
        return response()->json([
                                    'success' => false,
                                    'message' => "Invalid Credentials!"
                                ], 400);
    }


    public function me()
    {
        $user = app('auth')->user();

        if ($user) {
            return response()->json([
                                        'success' => true,
                                        'message' => "User profile found",
                                        'user'    => $user,
                                    ]);
        }
        return response()->json([
                                    'success' => false,
                                    'message' => 'User not found!'
                                ], 404);
    }
}
