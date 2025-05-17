<?php
namespace App\Http\Controllers\Core;

use App\Classes\AdResponseMessages;
use App\Http\Controllers\Controller;
use App\Http\Requests\Core\CreateAdRequest;
use App\Http\Requests\Core\FilterAdsRequest;
use App\Models\Ad;
use App\Models\City;
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
                'message' => AdResponseMessages::CREATED_SUCCESS,
                'ad'      => $ad->load(['user', 'images']),
            ], 201);
        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'message' => AdResponseMessages::CREATE_ERROR,
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
                'message' => AdResponseMessages::DELETED_SUCCESS,
            ], 200);
        } catch (\Throwable $e) {

            return response()->json([
                'message' => AdResponseMessages::DELETE_ERROR,
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

            return response()->json(['message' => AdResponseMessages::SAVED_SUCCESS], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => AdResponseMessages::SAVE_ERROR,
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

            return response()->json(['message' => AdResponseMessages::DELETE_FROM_LIST_SUCCESS], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => AdResponseMessages::DELETE_FROM_LIST_ERROR,
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

        return response()->json(['message' => AdResponseMessages::SAVED_ADS_NOT_FOUND], 404);

    }

    public function deleteAllSavedAds(): JsonResponse
    {

        try {

            $user = $this->getAuthenticatedUser();

            $result = $this->adService->deleteAllSavedAds($user);

            if (! $result) {
                return response()->json([
                    'message' => AdResponseMessages::ADS_NOT_FOUND,
                ], 404);
            }

            return response()->json(['message' => AdResponseMessages::DELETE_ALL_SAVED_ADS_SUCCESS], 200);

        } catch (\Throwable $e) {

            return response()->json([
                'message' => AdResponseMessages::DELETE_ALL_SAVED_ADS_ERROR,
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
                return response()->json(['message' => AdResponseMessages::ADS_NOT_FOUND], 404);
            }

            return response()->json([
                'message' => AdResponseMessages::DELETED_SUCCESS,
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting user ads: ' . $e->getMessage(), ['exception' => $e]);

            return response()->json([
                'error'   => AdResponseMessages::DELETE_ERROR,
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
            return response()->json(['message' => AdResponseMessages::ADS_NOT_FOUND], 404);
        }

        try {
            $this->authorize('delete', $ad);

        } catch (\Throwable $e) {
            return response()->json(['message' => AdResponseMessages::DELETE_AUTHORIZE_ERROR], 403);
        }

        if (! $this->adService->deleteAdById($ad)) {
            return response()->json(['message' => AdResponseMessages::DELETE_ERROR], 404);
        }

        return response()->json(['message' => AdResponseMessages::DELETED_SUCCESS], 200);
    }

    public function adsFilter(FilterAdsRequest $request): JsonResponse
    {
        $ads = Ad::query()
            ->byCity($request->city)
            ->bySellPriceOrder($request->sell_price)
            ->byRentPriceOrder($request->rent_price)
            ->MinPrice($request->tr_type, $request->min_price)
            ->MaxPrice($request->tr_type, $request->max_price)
            ->byPrType($request->pr_type)
            ->byTrType($request->tr_type)
            ->byArea($request->area)
            ->byFloorNumbers($request->floor_numbers)
            ->byTitle($request->title)
            ->byRooms($request->rooms)
            ->HasElevator($request->hasElevator)
            ->HasParking($request->hasParking)
            ->TypeOfWc($request->typeOfWc)
            ->get();

        return response()->json($ads, 200);
    }

    public function getCities(): JsonResponse
    {
        return response()->json(City::all());
    }

    public function getRealEstateAgents(): JsonResponse
    {
        $real_estate_agents = $this->adService->getAllRealEstateAgents();

        return response()->json($real_estate_agents);
    }

}
