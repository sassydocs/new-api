<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Mpociot\Versionable\VersionableTrait;

class Document extends Model
{
    use HasUuids;
    use HasFactory;
    use SoftDeletes;
    use VersionableTrait;

    protected $fillable = [
        'title',
        'description',
        'category_id',
        'sample_questions',
        'content',
    ];

    protected $casts = [
        'content' => 'json',
        'sample_questions' => 'json',
    ];

    public function app(): BelongsTo
    {
        return $this->belongsTo(App::class);
    }

    public function views(): HasMany
    {
        return $this->hasMany(View::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
