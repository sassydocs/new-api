<?php

namespace App\Http\Repositories\V1\Document;

use App\Models\App;
use App\Models\Document;
use Illuminate\Pagination\LengthAwarePaginator;
use Mpociot\Versionable\Version;
use Spatie\QueryBuilder\QueryBuilder;

class DocumentRepository
{
   public function index(App $app): LengthAwarePaginator
   {
       return QueryBuilder::for($app->documents())
           ->allowedFilters(['title'])
           ->with('category')
           ->paginate(10)
           ->appends(request()->query());
   }

   public function store(App $app, array $data): Document
   {
       return $app->documents()->create($data);
   }

   public function update(Document $document, array $data): Document
   {
       $document->update($data);

       return $document->refresh();
   }

    public function versions(Document $document): LengthAwarePaginator
    {
        return $document->versions()
            ->orderBy('created_at', 'asc')
            ->paginate(10);
    }

    public function revert(Document $document, Version $version): void
    {
        $version->revert();
    }

    public function diff(Document $document, Version $version): array
    {
        return $document->currentVersion()->diff($version);
    }
}
