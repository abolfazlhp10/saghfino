<?php

namespace App\Policies;

use App\Models\User;

class RealEstatePolicy
{
    public function create(User $user)
    {
        return $user->hasRole('real_estate_agent');
    }
}
