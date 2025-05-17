<?php
namespace App\Services;

use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Collection;

class RealEstateService
{
    public function getAllRealEstateAgents(): Collection
    {
        $role = Role::findByName('real_estate_agent');

        $real_estate_agents = $role->users()->get()->makeHidden('pivot');

        return $real_estate_agents;
    }

    public function getRealEstateAgentById($agent_id): Collection|null
    {

        $agent = User::where('id', $agent_id)
            ->whereHas('roles', function ($query) {
                $query->where('name', 'real_estate_agent');
            })
            ->first();

        return $agent;

    }
}
