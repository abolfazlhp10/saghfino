<?php
namespace App\Services;

use App\Models\Ad;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

class AdService
{

    private const IMAGE_SAVE_PATH = 'ads/images/';

    /**
     * Get paginated ads
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getPaginatedAds(int $perPage): LengthAwarePaginator
    {
        return Ad::with(['user', 'images'])
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Create a new ad
     *
     * @param array $data
     * @return Ad
     */
    public function createAd(array $data): Ad
    {
        $data['user_id'] = Auth::user()->id;

        return Ad::create($data);
    }

    /**
     * Store images for an ad
     *
     * @param Ad $ad
     * @param array $images
     * @return void
     */
    public function storeAdImages(Ad $ad, array $images): void
    {
        $ad->images()->createMany(
            collect($images)->map(function ($image) {
                return [
                    'path' => $image->store(self::IMAGE_SAVE_PATH),
                ];
            })->toArray()
        );
    }

    /**
     * Delete an ad and its associated images
     *
     * @param Ad $ad
     * @return void
     */
    public function deleteAd(Ad $ad)
    {
        foreach ($ad->images as $image) {
            if (Storage::exists($image->path)) {
                Storage::delete($image->path);
            }
        }
        $ad->images()->delete();

        $ad->delete();
    }

    /**
     * Save an ad for a user
     *
     * @param Ad $ad
     * @param User $user
     * @return void
     */
    public function saveAdForUser(Ad $ad, User $user): void
    {
        $user->savedAds()->attach($ad);
    }

    public function unsaveAdForUser(Ad $ad, User $user): void
    {

        $user->savedAds()->detach($ad);
    }

    public function getSavedAds(User $user): Collection
    {
        return $user->savedAds()->get();
    }

    public function deleteAllSavedAds(User $user)
    {
        return $user->savedAds()->detach();
    }

    public function getMyAds(User $user): Collection
    {
        return $user->ads()->get();
    }

    public function deleteAllUserAds(User $user): bool
    {
        DB::beginTransaction();

        try {
            if (! $user->ads()->exists()) {
                return false;
            }

            $user->ads()->with('images')->chunk(100, function ($ads) {
                foreach ($ads as $ad) {
                    foreach ($ad->images as $image) {
                        if (Storage::exists($image->path)) {
                            Storage::delete($image->path);
                        }
                    }

                    $ad->images()->delete();
                    $ad->delete(); // Delete each ad directly
                }
            });

            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e; // Rethrow the exception to be caught by the calling function
        }
    }

    public function deleteAdById(Ad $ad): bool
    {
        try {

            foreach ($ad->images as $image) {
                if (Storage::exists($image->path)) {
                    Storage::delete($image->path);
                }
            }

            $ad->images()->delete();

            return $ad->delete(); // This will return true if successful, false if not
        } catch (\Exception $e) {
            Log::error('Error deleting ad or images: ' . $e->getMessage());
            return false;
        }
    }

    public function getAllRealEstateAgents(): Collection
    {
        $role = Role::findByName('real_estate_agent');

        $real_estate_agents = $role->users()->get()->makeHidden('pivot');

        return $real_estate_agents;
    }

    public function prepareAdForShow(Ad $ad): Ad
    {
        $ad->load([
            'user' => function ($query) {
                $query->withCount('ads');
            },
            'images',
        ])->loadCount('savedByUsers');

        $ad->is_saved = false;

        if ($user = Auth::guard('sanctum')->user()) {

            $ad->is_saved = $user->savedAds()->where('ad_id', $ad->id)->exists();

        }

        $ad->similar_ads = $this->getSimilarAds($ad);

        return $ad;
    }

    private function getSimilarAds(Ad $ad): Collection
    {
        return Ad::where('city', $ad->city)
            ->where('id', '!=', $ad->id)
            ->with(['images'])
            ->limit(6)
            ->get();
    }
}
