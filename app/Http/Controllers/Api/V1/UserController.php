<?php

namespace App\Http\Controllers\Api\V1;

use App\Dtos\Utils\PaginationDto;
use App\Filters\UserFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\IndexRequest;
use App\Http\Requests\User\StoreRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\UseCases\User\CreateUseCase;
use App\UseCases\User\UpdateUseCase;
use Exception;

class UserController extends Controller
{
    public function index(IndexRequest $request)
    {
        $query = User::query()->with('company');

        $paginator = (new UserFilter($query, $request))
            ->apply()
            ->paginate(perPage: request('per_page', 10), page: request('page', 1));

        $users = $paginator->map(fn ($user) => new UserResource($user));

        $paginator = $users->isEmpty() ? null : $paginator;

        return [
            'users' => $users,
            'pagination' => PaginationDto::from(compact('paginator'))
        ];
    }

    /**
     * @throws Exception
     */
    public function store(StoreRequest $request, CreateUseCase $useCase)
    {
        $user = $useCase->perform($request->toDto());

        $user = new UserResource($user);

        return compact('user');
    }

    public function show($user)
    {
        $user = User::find($user);

        error_if($user === null, 'MODEL_NOT_FOUND');

        $user = new UserResource($user);

        return compact('user');
    }

    /**
     * @throws Exception
     */
    public function update(User $user, StoreRequest $request, UpdateUseCase $useCase)
    {
        $user = $useCase->perform($user, $request->toDto());

        $user = new UserResource($user);

        return compact('user');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return 'User deleted successfully.';
    }
}
