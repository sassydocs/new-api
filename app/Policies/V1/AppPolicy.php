<?php

namespace App\Policies\V1;

use App\Models\App;
use App\Models\User;

class AppPolicy extends BasePolicy
{
    public function view(User $user, App $app): bool
    {
        return $this->memberOfApp($user, $app);
    }

    public function update(User $user, App $app): bool
    {
        return $this->adminOfApp($user, $app);
    }

    public function destroy(User $user, App $app): bool
    {
        return $this->ownerOfApp($user, $app);
    }

    public function managePrivateKeys(User $user, App $app): bool
    {
        return $this->adminOfApp($user, $app);
    }
}
