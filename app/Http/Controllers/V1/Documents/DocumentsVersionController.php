<?php

namespace App\Http\Controllers\V1\Documents;

use App\Http\Controllers\Controller;
use App\Http\Repositories\V1\Document\DocumentRepository;
use App\Http\Requests\V1\CreateDocumentRequest;
use App\Http\Resources\V1\DocumentResource;
use App\Http\Resources\V1\DocumentVersionResource;
use App\Models\App;
use App\Models\Document;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use Mpociot\Versionable\Version;
use Spatie\QueryBuilder\QueryBuilder;

class DocumentsVersionController extends Controller
{
    public function __construct(public DocumentRepository $repository)
    {
    }

    public function index(App $app, Document $document): JsonResponse
    {
        Gate::authorize('view', $app);

        $versions = $this->repository->versions($document);

        return $this->success([
            'versions' => DocumentVersionResource::collection($versions),
        ]);
    }

    public function revert(App $app, Document $document, Version $version): JsonResponse
    {
        Gate::authorize('view', $app);

        $this->repository->revert($document, $version);

        return $this->empty();
    }

    public function diff(App $app, Document $document, Version $version): JsonResponse
    {
        Gate::authorize('view', $app);

        $diff = $this->repository->diff($document, $version);

        return $this->success([
            'diff' => $diff,
        ]);
    }
}
