<?php

namespace App\Http\Controllers\V1\Documents;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\DocumentCategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;

class DocumentCategoryController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $categories = Category::all();

        return $this->success([
            'categories' => DocumentCategoryResource::collection($categories),
        ]);
    }
}
