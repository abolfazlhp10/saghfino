<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Http\Requests\Core\CreateAdRequest;
use App\Models\Ad;
use App\Models\User;
use App\Services\AdService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AdController extends Controller
{

    const DEFAULT_PAGINATION = 20;

    public function __construct(private readonly AdService $adService) {}
    

    /**
     * Display a paginated list of ads
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {

        $ads = $this->adService->getPaginatedAds(self::DEFAULT_PAGINATION);

        return response()->json($ads, 200);
    }


    /**
     * Store a newly created ad
     *
     * @param CreateAdRequest $request
     * @return JsonResponse
     */

    public function store(CreateAdRequest $request): JsonResponse
    {
        try {

            DB::beginTransaction();

            $ad = $this->adService->createAd($request->validated());

            if ($request->has('images')) {
                $this->adService->storeAdImages($ad, $request->file('images'));
            }

            DB::commit();

            return response()->json([
                'message' => 'آگهی با موفقیت ایجاد شد',
                'ad'      => $ad->load(['user', 'images']),
            ], 201);
        } catch (\Exception $th) {

            DB::rollBack();

            return response()->json([
                'message' => 'خطا در ایجاد آگهی',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Ad $ad): JsonResponse
    {
        // Delete the associated images from storage
        foreach ($ad->images as $image) {
            // Add back the 'public/' prefix for storage operations
            $fullPath = $image->path;
            // Check if the image exists in storage and delete it
            if (Storage::exists($fullPath)) {
                Storage::delete($fullPath);
            }
        }

        // Delete the images from the database
        $ad->images()->delete();

        // Now delete the ad itself
        $ad->delete();

        // Return success response
        return response()->json(['message' => 'Ad and associated images deleted successfully'], 200);
    }

    public function show(Ad $ad): JsonResponse
    {
        return response()->json($ad->load(['user', 'images']), 200);
    }

    public function saveAds($adId): JsonResponse
    {

        $ad = Ad::findOrFail($adId);

        $user = Auth::user();

        $user->savedAds()->attach($ad);

        return response()->json(['message' => 'Ad saved successfully!'], 200);
    }

    public function unsaveAd($adId): JsonResponse
    {
        $ad = Ad::findOrFail($adId);

        $user = Auth::user();

        $user->savedAds()->detach($ad);

        return response()->json(['message' => 'ad detached successfully!'], 200);
    }

    public function showSavedAds(): JsonResponse
    {
        $user = Auth::user();

        $savedAds = $user->savedAds()->get();

        return response()->json($savedAds);
    }

    public function deleteAllSavedAds(): JsonResponse
    {
        $user = Auth::user();

        $user->savedAds()->detach();

        return response()->json(['message' => 'All saved ads have been removed.'], 200);
    }

    public function showMyAds(): JsonResponse
    {
        $user = Auth::user();

        $ads = $user->ads()->get();

        return response()->json($ads, 200);
    }

    public function deleteAllMyAds(): JsonResponse
    {
        // Start a transaction to ensure atomicity
        DB::beginTransaction();

        try {
            $user = Auth::user();

            // Process ads in chunks to reduce memory usage
            $user->ads()->with('images')->chunk(100, function ($ads) {
                foreach ($ads as $ad) {
                    // Delete each ad's images from storage and database
                    foreach ($ad->images as $image) {
                        if (Storage::exists($image->path)) {
                            Storage::delete($image->path);
                        }
                    }

                    // Delete the images from database
                    $ad->images()->delete();
                }

                // After processing images, delete the ads
                $ads->each->delete();
            });

            // Commit the transaction
            DB::commit();

            return response()->json([
                'message' => 'تمامی آگهی‌ها و تصاویر مربوطه با موفقیت حذف شدند',
            ], 200);
        } catch (\Exception $e) {
            // Rollback in case of any failure
            DB::rollBack();

            return response()->json([
                'error'   => 'خطا در حذف آگهی‌ها و تصاویر',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    public function deleteMyAdsById(Ad $ad): JsonResponse
    {

        foreach ($ad->images as $image) {

            if (Storage::exists($image->path)) {
                Storage::delete($image->path);
            }
        }

        $ad->images()->delete();

        $ad->delete();

        return response()->json(['message' => 'the ad deleted successfully!'], 200);
    }
}
