<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Viewer extends Model
{
    use HasUuids;

    public function views(): HasMany
    {
        return $this->hasMany(View::class);
    }
}
