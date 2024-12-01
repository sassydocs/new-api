<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class View extends Model
{
    use HasUuids;

    public function viewer(): BelongsTo
    {
        return $this->belongsTo(Viewer::class);
    }
}
