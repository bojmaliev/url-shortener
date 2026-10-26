<?php

namespace App\Policies;

use App\Models\Link;
use App\Models\User;

class LinkPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Link $link): bool
    {
        return $user->is_admin || $link->user()->is($user);
    }

    public function create(User $user): bool
    {
        return true;
    }
}
