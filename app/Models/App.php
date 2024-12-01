<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Cashier\Billable;

class App extends Model
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;
    use Billable;

    protected $with = 'owner';

    protected $fillable = [
        'name',
        'owner_id',
        'description',
        'private_key',
        'public_key',
        'document_limit',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'app_users')
            //->withPivotValue('role')
            ->withTimestamps();
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function hasExceededDocumentLimit(): bool
    {
        return $this->documents()->count() >= $this->document_limit;
    }
}
