<?php

namespace App\Http\Controllers;

use App\Actions\LoginAction;
use App\Actions\RegisterAction;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Register a new user into the blog system
     *
     * @param RegisterRequest $request
     * @param RegisterAction $action
     * @return JsonResponse
     */
    public function register(RegisterRequest $request, RegisterAction $action): JsonResponse
    {
        $action->execute($request->only(['name', 'email', 'password']));

        return $this->createdResponse('User registered successfully');
    }

    /**
     * Login a user into the blog system
     *
     * @param LoginRequest $request
     * @param LoginAction $action
     * @return JsonResponse
     */
    public function login(LoginRequest $request, LoginAction $action): JsonResponse
    {
        if(!Auth::attempt($request->only(['email', 'password']))){
            return $this->badRequestAlert('Invalid login credentials');
        }

        $generateToken = $action->execute($request->validated());

        return $this->successResponse('User Logged In Successfully', $generateToken);
    }

    /**
     * Logout a user from the blog system
     *
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        request()->user()->tokens()->delete();

        return $this->successResponse('User Logged Out Successfully');
    }
}
