<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Ad;

class AdPolicy
{
    public function delete(User $user, Ad $ad)
    {
        return $user->id === $ad->user_id || $user->hasRole('admin');
    }
}
