<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class Controller
{
    public function success(array $data = []): JsonResponse
    {
        return $this->response($data, 200);
    }

    public function fail(array $data = [], int $code = 400): JsonResponse
    {
        return $this->response($data, $code);
    }

    public function unauthorised(): JsonResponse
    {
        return $this->error('You do not have permission to do that', 403);
    }

    public function created(array $data = []): JsonResponse
    {
        return $this->response($data, 201);
    }

    public function error(string $message, int $code = 422): JsonResponse
    {
        return $this->response([
            'message' => $message,
        ], $code);
    }

    public function empty(): JsonResponse
    {
        return $this->response([], 204);
    }

    public function response(array $data, int $code): JsonResponse
    {
        return response()->json($data, $code);
    }
}
