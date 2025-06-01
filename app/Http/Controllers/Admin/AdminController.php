<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use App\Models\RealEstate;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class AdminController extends Controller
{
    public function getCounts(): JsonResponse
    {
        $adsCount = Ad::withoutGlobalScopes()->count();;
        $usersCount=User::count();

        $counts=[
            'users'=>$usersCount,
            'ads'=>$adsCount
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

}
