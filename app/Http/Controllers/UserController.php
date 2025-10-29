<?php

namespace App\Http\Controllers;

use App\Actions\Users\CreateUserAction;
use App\Actions\Users\DeleteUserAction;
use App\Actions\Users\ListUsersAction;
use App\Actions\Users\UpdateUserAction;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class UserController extends Controller
{
    public function index(ListUsersAction $listUsersAction): AnonymousResourceCollection
    {
        $users = $listUsersAction();

        return UserResource::collection($users);
    }

    public function store(UserStoreRequest $request, CreateUserAction $createUserAction): JsonResponse
    {
        $user = $createUserAction($request->validated());

        return (new UserResource($user))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(User $user): UserResource
    {
        return new UserResource($user);
    }

    public function update(UserUpdateRequest $request, User $user, UpdateUserAction $updateUserAction): UserResource
    {
        $updatedUser = $updateUserAction($user, $request->validated());

        return new UserResource($updatedUser);
    }

    public function destroy(User $user, DeleteUserAction $deleteUserAction): Response
    {
        $deleteUserAction($user);

        return response()->noContent();
    }
}
