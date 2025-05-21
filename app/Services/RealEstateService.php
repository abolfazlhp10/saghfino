<?php
namespace App\Services;

use App\Models\RealEstate;
use App\Models\RealEstateRate;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

class RealEstateService
{
    public function getAllRealEstateAgents(): Collection
    {
        $role = Role::findByName('real_estate_agent');

        $real_estate_agents = $role->users()->get()->makeHidden('pivot');

        return $real_estate_agents;
    }

    public function getRealEstateAgentById($agent_id): Collection | null
    {

        $agent = User::where('id', $agent_id)
            ->whereHas('roles', function ($query) {
                $query->where('name', 'real_estate_agent');
            })
            ->first();

        return $agent;
    }

    public function createNewRealEstate($request)
    {

        try {

            return RealEstate::create([
                'agent_id' => auth()->user()->id,
                'name'    => $request->name,
                'tagline' => $request->tagline,
                'address' => $request->address,
                'city'    => $request->city,
                'image'   => $request->hasFile('image') ? $request->file('image')->store('realEstates', 'public') : null,
            ]);
        } catch (\Throwable $e) {
            Log::error('error in creating new real estate : ', [$e->getMessage()]);
        }
    }

    public function getAllRealEstates(Request $request)
    {
        $query = RealEstate::query();

        if ($request->city) {
            $real_estates = $query->where('city', $request->city)->get();
            return $real_estates;
        } else {
            return RealEstate::paginate(12);
        }
    }

    public function saveRate($request): string
    {
        try {

            RealEstateRate::create([
                'rate'           => $request->rate,
                'user_id'        => auth()->user()->id,
                'real_estate_id' => $request->real_estate_id,
            ]);

            return 'true';

        } catch (\Exception $e) {
            Log::error('error in save the real estate rate : ', [$e->getMessage()]);
            return 'false';
        }

    }
}
