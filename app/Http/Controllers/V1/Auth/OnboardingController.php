<?php

namespace App\Http\Controllers\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Repositories\V1\Auth\AuthRepository;
use App\Http\Requests\V1\OnboardingRequest;
use App\Http\Resources\V1\CurrentUserResource;
use Illuminate\Http\JsonResponse;

class OnboardingController extends Controller
{
    public function __invoke(OnboardingRequest $request, AuthRepository $repository): JsonResponse
    {
        $user = $repository->createUser($request->validated());

        return $this->success([
            'auth' => new CurrentUserResource($user),
        ]);
    }
}
