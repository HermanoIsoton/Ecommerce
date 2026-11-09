<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use App\Services\CartService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        private AuthService $authService,
        private CartService $cartService,
    ) {}

    public function register(RegisterRequest $request)
    {
        $user = $this->authService->register(
            $request->validated(),
            $request
        );
        $this->cartService->mergeGuestCartIntoUser(
            $request->header('X-Cart-Token'),
            $user,
        );

        return (new UserResource($user))
            ->response()
            ->setStatusCode(201)
            ->header('Cache-Control', 'no-store');
    }

    public function login(LoginRequest $request)
    {
        $user = $this->authService->login(
            $request->validated(),
            $request
        );
        $this->cartService->mergeGuestCartIntoUser(
            $request->header('X-Cart-Token'),
            $user,
        );

        return (new UserResource($user))
            ->response()
            ->header('Cache-Control', 'no-store');
    }

    public function me(Request $request)
    {
        return new UserResource($request->user());
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = $this->authService->updateProfile(
            $request->user(),
            $request->validated()
        );

        return (new UserResource($user))
            ->response()
            ->header('Cache-Control', 'no-store');
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        $this->authService->changePassword(
            $request->user(),
            $request->validated()['password']
        );

        return response()->noContent();
    }

    public function logout(Request $request)
    {
        $this->authService->logout($request);

        return response()->noContent();
    }
}
