<?php
namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Http\Requests\Core\CreateAdRequest;
use App\Models\Ad;
use App\Models\User;
use App\Services\AdService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

        try {

            $user = $this->getAuthenticatedUser();

            $result = $this->adService->deleteAllSavedAds($user);

            if (! $result) {
                return response()->json([
                    'message' => 'آگهی یافت نشد !',
                ], 404);
            }

            return response()->json(['message' => 'تمام آگهی های ذخیره شدن کاربر با موفقیت حذف گردید'], 200);

        } catch (\Throwable $e) {

            return response()->json([
                'message' => 'ارور در حذف آگهی های ذخیره شده !',
                'error'   => $e->getMessage(),
            ], 500);

        }

    }

    public function showMyAds(): JsonResponse
    {
        try {

            $user = $this->getAuthenticatedUser();

            $ads = $this->adService->getMyAds($user);

            if ($ads->isEmpty()) {
                return response()->json([
                    'message' => 'آگهی یافت نشد !',
                ], 404);
            }

            return response()->json($ads, 200);

        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'server error !',
                'error'   => $e->getMessage(),
            ], 500);
        }

    }

    public function deleteAllMyAds(): JsonResponse
    {

        try {
            $user = $this->getAuthenticatedUser();

            $result = $this->adService->deleteAllUserAds($user);

            if (! $result) {
                return response()->json(['message' => 'آگهی یافت نشد !'], 404);
            }

            return response()->json([
                'message' => 'تمامی آگهی‌ها و تصاویر مربوطه با موفقیت حذف شدند',
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting user ads: ' . $e->getMessage(), ['exception' => $e]);

            return response()->json([
                'error'   => 'خطا در حذف آگهی‌ها و تصاویر',
                'details' => $e->getMessage(),
            ], 500);
        }

    }

    public function deleteMyAdsById(int $adId): JsonResponse
    {

        try {
            $ad = Ad::findOrFail($adId);

        } catch (ModelNotFoundException $e) {
            // Catch the error if ad is not found and return a custom JSON response
            return response()->json(['message' => 'آگهی یافت نشد!'], 404);
        }

        try {
            $this->authorize('delete', $ad);

        } catch (\Throwable $e) {
            return response()->json(['message' => 'شما مجاز به حذف این آگهی نیستید !'], 403);
        }

        if (! $this->adService->deleteAdById($ad)) {
            return response()->json(['message' => 'خطا در حذف آگهی ! '], 404);
        }

        return response()->json(['message' => 'آگهی و تصاویر مربوطه با موفقیت حذف شد'], 200);
    }

}
