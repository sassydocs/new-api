<?php

namespace App\Http\Controllers\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\CurrentUserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class CurrentUserController extends Controller
{
    /**
     * Get the current user
     *
     * @return JsonResponse
     * @group Auth
     */
    public function __invoke(): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user()
            ->load(['apps']);

        return $this->success([
            'auth' => new CurrentUserResource($user),
        ]);
    }
}
