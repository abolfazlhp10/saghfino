<?php
namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Http\Requests\Core\CreateAdRequest;
use App\Models\Ad;
use App\Models\User;
use App\Services\AdService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AdController extends Controller
{
    use AuthorizesRequests;

    const DEFAULT_PAGINATION = 20;

    public function __construct(private readonly AdService $adService)
    {}

    /**
     * Get the authenticated user
     *
     * @return User
     */
    protected function getAuthenticatedUser(): User
    {
        return Auth::user();
    }

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
        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'message' => 'خطا در ایجاد آگهی',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified ad
     *
     * @param Ad $ad
     * @return JsonResponse
     */
    public function destroy(Ad $ad): JsonResponse
    {

        try {

            $this->authorize('delete', $ad);

            $this->adService->deleteAd($ad);

            return response()->json([
                'message' => 'آگهی و تصاویر مربوطه با موفقیت حذف شدند',
            ], 200);
        } catch (\Throwable $e) {

            return response()->json([
                'message' => 'خطا در حذف آگهی',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified ad
     *
     * @param Ad $ad
     * @return JsonResponse
     */
    public function show(Ad $ad): JsonResponse
    {
        return response()->json($ad->load(['user', 'images']), 200);
    }

    /**
     * Save the specified ad
     *
     * @param Ad $ad
     * @return JsonResponse
     */
    public function saveAd(Ad $ad): JsonResponse
    {

        try {

            $user = $this->getAuthenticatedUser();

            $this->adService->saveAdForUser($ad, $user);

            return response()->json(['message' => 'آگهی با موفقیت ذخیره شد'], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'خطا در ذخیره آگهی',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove an ad from user's saved ads
     *
     * @param Ad $ad
     * @return JsonResponse
     */
    public function unsaveAd(Ad $ad): JsonResponse
    {

        try {
            $user = $this->getAuthenticatedUser();

            $this->adService->unsaveAdForUser($ad, $user);

            return response()->json(['message' => 'آگهی از لیست ذخیره‌ها حذف شد'], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'خطا در حذف آگهی از لیست ذخیره‌ها',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function showSavedAds(): JsonResponse
    {
        $user = $this->getAuthenticatedUser();

        $savedAds = $this->adService->getSavedAds($user);

        if (count($savedAds)) {

            return response()->json([$savedAds->load(['images'])], 200);
        }

        return response()->json(['message' => '!آگهی ذخیره شده ای برای این کاربر وجود ندارد'], 404);

    }

    public function deleteAllSavedAds(): JsonResponse
    {
        $user = $this->getAuthenticatedUser();

        $user->savedAds()->detach();

        return response()->json(['message' => 'All saved ads have been removed.'], 200);
    }

    public function showMyAds(): JsonResponse
    {
        $user = $this->getAuthenticatedUser();

        $ads = $user->ads()->get();

        return response()->json($ads, 200);
    }

    public function deleteAllMyAds(): JsonResponse
    {
        // Start a transaction to ensure atomicity
        DB::beginTransaction();

        try {
            $user = $this->getAuthenticatedUser();

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
