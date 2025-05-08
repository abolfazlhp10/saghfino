<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;
use App\Models\Ad;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

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

    public function unsaveAdForUser(Ad $ad,User $user): void
    {

        $user->savedAds()->detach($ad);
    }

    public function getSavedAds(User $user){
        return $user->savedAds()->get();
    }
}
