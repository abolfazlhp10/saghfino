<?php
namespace App\Services;

use App\Models\RealEstate;
use App\Models\RealEstateRate;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

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

     public function saveRate(object $request): bool
    {
        $userId = auth()->id();
        $realEstateId = $request->real_estate_id;
        $newRate = (int) $request->rate;

        DB::beginTransaction();

        try {
            $realEstate = RealEstate::findOrFail($realEstateId);

            $existingRate = RealEstateRate::where('real_estate_id', $realEstateId)
                ->where('user_id', $userId)
                ->first();

            $previousRateValue = $existingRate?->rate ?? 0;

            $rate = RealEstateRate::updateOrCreate(
                ['real_estate_id' => $realEstateId, 'user_id' => $userId],
                ['rate' => $newRate]
            );

            if ($rate->wasRecentlyCreated) {
                $realEstate->rate_count++;
                $realEstate->rate_sum += $newRate;
            } else {
                $realEstate->rate_sum = $realEstate->rate_sum - $previousRateValue + $newRate;
            }

            $realEstate->rate = (int) ceil($realEstate->rate_sum / max($realEstate->rate_count, 1));
            $realEstate->save();

            DB::commit();

            return 'true';
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to save real estate rate', [
                'user_id' => $userId,
                'real_estate_id' => $realEstateId,
                'error' => $e->getMessage(),
            ]);

            return 'false';
        }
    }
}
