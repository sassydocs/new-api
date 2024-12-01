<?php

namespace App\Http\Controllers\V1\Apps;

use App\Http\Controllers\Controller;
use App\Http\Repositories\V1\App\AppRepository;
use App\Http\Resources\V1\AppPrivateKeyResource;
use App\Models\App;
use Illuminate\Http\JsonResponse;

class AppPrivateKeyController extends Controller
{
    public function __construct(public AppRepository $repository)
    {
    }

    public function reveal(App $app): JsonResponse
    {
        if (request()->user()->cannot('managePrivateKeys', $app)) {
            return $this->unauthorised();
        }

        return $this->success([
            'app' => AppPrivateKeyResource::make($app),
        ]);
    }

    public function cycle(App $app): JsonResponse
    {
        $this->repository->cyclePrivateKey($app);

        return $this->created([
            'app' => AppPrivateKeyResource::make($app),
        ]);
    }
}
