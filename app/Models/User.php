<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Http\Enum\V1\UserTypeEnum;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use HasUuids;
    use Notifiable;
    use HasApiTokens;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'max_apps',
        'last_login_at',
        'google_id',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_login_at' => 'datetime',
        ];
    }

    public function ownedApps(): HasMany
    {
        return $this->hasMany(App::class, 'owner_id');
    }


    public function apps(): BelongsToMany
    {
        return $this->belongsToMany(App::class, 'app_users')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function hasExceededAppLimits(): bool
    {
        return $this->apps()->count() >= $this->getAttribute('max_apps');
    }

    public function roleOnApp(App $app): UserTypeEnum|null
    {
        $userApp = $this->apps()->find($app);

        if (!$userApp) {
            return null;
        }

        return UserTypeEnum::tryFrom($userApp->pivot->role);
    }
}
