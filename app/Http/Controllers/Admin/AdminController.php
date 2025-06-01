<?php

namespace App\Http\Controllers\Admin;

use App\Classes\ResponseMessages;
use App\Http\Controllers\Controller;
use App\Models\Ad;
use App\Models\RealEstate;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\JsonResponse;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    public function getCounts(): JsonResponse
    {
        $adsCount = Ad::withoutGlobalScopes()->count();;
        $usersCount = User::count();

        $counts = [
            'users' => $usersCount,
            'ads' => $adsCount
        ];

        return response()->json($counts);
    }

    public function getAllUsers(): Collection
    {
        return User::all();
    }

    public function getAllAgents(): Collection
    {
        return User::role('real_estate_agent')->get();
    }

    public function getAllRealEstates(): Collection
    {
        return RealEstate::with(['agent'])->get();
    }

    public function getAllAds(): LengthAwarePaginator
    {
        return Ad::withoutGlobalScopes()->with(['images', 'user'])->paginate(20);
    }

    public function deleteAd($ad_id): JsonResponse
    {
        $ad = Ad::withoutGlobalScopes()->find($ad_id);

        try {

            foreach ($ad->images as $image) {

                if (Storage::exists($image->path)) {
                    Storage::delete($image->path);
                }
            }

            $ad->images()->delete();

            $ad->delete();

            return response()->json(['message' => ResponseMessages::DELETED_SUCCESS], 200);
        } catch (Exception $e) {
            Log::info("error in deleting the ad : ", [$e->getMessage()]);
            return response()->json(ResponseMessages::DELETE_ERROR, 500);
        }
    }
}
