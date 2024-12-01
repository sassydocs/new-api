<?php

namespace App\Policies\V1;

use App\Http\Enum\V1\UserTypeEnum;
use App\Models\App;
use App\Models\User;

class BasePolicy
{
    public function ownerOfApp(User $user, App $app): bool
    {
        return $user->roleOnApp($app) === UserTypeEnum::Owner;
    }

    public function adminOfApp(User $user, App $app): bool
    {
        return in_array($user->roleOnApp($app), [
            UserTypeEnum::Admin,
            UserTypeEnum::Owner,
        ]);
    }

    public function memberOfApp(User $user, App $app): bool
    {
        return $user->roleOnApp($app) !== null;
    }
}
