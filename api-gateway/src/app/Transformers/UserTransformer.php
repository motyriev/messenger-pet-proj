<?php

declare(strict_types=1);

namespace App\Transformers;

use App\Models\User;

class UserTransformer
{
    public static function transform(User $user)
    {
        return [
            'id'   => $user->id,
            'name' => $user->email,
        ];
    }
}