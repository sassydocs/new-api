<?php

namespace App\Http\Controllers\V1\Apps;

use App\Http\Controllers\Controller;
use App\Http\Repositories\V1\App\AppRepository;
use App\Http\Requests\V1\CreateAppRequest;
use App\Http\Requests\V1\DeleteAppRequest;
use App\Http\Requests\V1\UpdateAppRequest;
use App\Http\Resources\V1\AppResource;
use App\Models\App;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\JsonResponse;

class AppController extends Controller
{
    public function __construct(public AppRepository $repository)
    {
    }

    public function store(CreateAppRequest $request): JsonResponse
    {
        $app = $this->repository->createApp($request->validated());

        $app->load('owner');

        return $this->created([
            'app' => AppResource::make($app),
        ]);
    }

    public function update(UpdateAppRequest $request, App $app): JsonResponse
    {
        Gate::authorize('update', $app);

        $app = $this->repository->updateApp($app, $request->validated());

        $app->load('owner');

        return $this->created([
            'app' => AppResource::make($app),
        ]);
    }

    public function destroy(DeleteAppRequest $request, App $app): JsonResponse
    {
        Gate::authorize('destroy', $app);

        $this->repository->deleteApp($app);

        return $this->empty();
    }
}
