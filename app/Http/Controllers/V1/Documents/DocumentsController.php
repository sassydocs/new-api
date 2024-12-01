<?php

namespace App\Http\Controllers\V1\Documents;

use App\Http\Controllers\Controller;
use App\Http\Repositories\V1\Document\DocumentRepository;
use App\Http\Requests\V1\CreateDocumentRequest;
use App\Http\Requests\V1\UpdateDocumentRequest;
use App\Http\Resources\V1\DocumentResource;
use App\Models\App;
use App\Models\Document;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use Spatie\QueryBuilder\QueryBuilder;

class DocumentsController extends Controller
{
    public function __construct(public DocumentRepository $repository)
    {
    }

    public function index(App $app): JsonResponse
    {
        Gate::authorize('view', $app);

        $documents = $this->repository->index($app);

        return $this->success([
            'documents' => DocumentResource::collection($documents),
        ]);
    }

    public function store(CreateDocumentRequest $request, App $app): JsonResponse
    {
        Gate::authorize('view', $app);

        $document = $this->repository->store($app, $request->validated());

        return $this->success([
            'document' => DocumentResource::make($document),
        ]);
    }

    public function update(UpdateDocumentRequest $request, App $app, Document $document): JsonResponse
    {
        Gate::authorize('view', $app);

        $document = $this->repository->update($document, $request->validated());

        return $this->success([
            'document' => DocumentResource::make($document),
        ]);
    }

    public function show(App $app, Document $document): JsonResponse
    {
        Gate::authorize('view', $app);

        return $this->success([
            'document' => DocumentResource::make($document),
        ]);
    }

    public function destroy(App $app, Document $document): JsonResponse
    {
        Gate::authorize('view', $app);

        $document->delete();

        return $this->empty();
    }
}
