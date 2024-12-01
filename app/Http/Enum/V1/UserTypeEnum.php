<?php

namespace App\Http\Enum\V1;

enum UserTypeEnum: string
{
    case Owner = 'owner';
    case Admin = 'admin';
    case User = 'user';
}
