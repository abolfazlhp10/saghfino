<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class AdminController extends Controller
{
    public function getCounts(): JsonResponse
    {
        $adsCount = Ad::count();
        $usersCount=User::count();

        $counts=[
            'users'=>$usersCount,
            'ads'=>$adsCount
        ];

        return response()->json($counts);
    }

}
