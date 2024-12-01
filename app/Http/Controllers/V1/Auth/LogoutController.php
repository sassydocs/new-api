<?php

namespace App\Http\Controllers\V1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class LogoutController extends Controller
{
    /**
     * Logout
     *
     * @return JsonResponse
     * @group Auth
     */
    public function __invoke(): JsonResponse
    {
        Auth::guard('web')->logout();

        return $this->empty();
    }
}
