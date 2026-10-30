<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUsersRequest;
use App\Http\Requests\UpdateUsersRequest;
use App\Http\Resources\UserResource;
use App\Services\UserService;

class UserController extends Controller
{
    public function __construct(
        private UserService $userService
    ) {}

    public function index()
    {
        $users = $this->userService->listAll();

        return UserResource::collection($users);
    }

    public function store(StoreUsersRequest $request)
    {

        $user = $this->userService->create($request->validated());

        return new UserResource($user);
    }

    public function show(string $id)
    {
        $user = $this->userService->findById($id);

        return new UserResource($user);
    }

    public function update(UpdateUsersRequest $request, string $id)
    {
        $user = $this->userService->update($id, $request->validated());

        return new UserResource($user);
    }

    public function destroy(string $id)
    {
        $this->userService->delete($id);

        return response()->noContent();
    }
}
