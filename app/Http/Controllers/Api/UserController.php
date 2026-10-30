<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\UserService;

class UserController extends Controller
{
    public function __construct(
        private UserService $userService
    ) {}
   
    public function index()
    {
        $users = $this->userService->listAll();
        return response()->json($users);
    }

   
    public function store(Request $request)
    {

        $user = $this->userService->create($request->all());
        return response()->json($user, 201);
    }

    
    public function show(string $id)
    {
         $user = $this->userService->findById($id);

        return response()->json($user);
    }

   
    public function update(Request $request, string $id)
    {
          $user = $this->userService->update($id, $request->all());

        return response()->json($user);
    }


    public function destroy(string $id)
    {
        $this->userService->delete($id);

        return response()->noContent();
    }
}
