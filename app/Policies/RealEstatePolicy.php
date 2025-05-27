<?php
namespace App\Policies;

use App\Models\RealEstate;
use App\Models\User;

class RealEstatePolicy
{
    public function create(User $user)
    {
        return $user->hasRole('real_estate_agent');
    }

    public function edit(User $user, RealEstate $real_estate)
    {
        return $real_estate->agent_id === $user->id;
    }
}
