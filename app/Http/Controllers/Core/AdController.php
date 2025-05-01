<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Http\Requests\Core\CreateAdRequest;
use App\Models\Ad;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AdController extends Controller
{

    public function index()
    {
        $ads = Ad::with(['user', 'images'])
            ->latest()
            ->paginate(10);

        return response()->json($ads, 200);
    }

    public function store(CreateAdRequest $request): JsonResponse
    {

        $ad = Ad::create([
            'user_id' => $request->user_id, // The ID of the user creating the ad
            'title' => $request->title,
            'description' => $request->description,
            'address' => $request->address,
            'area' => $request->area, // in square meters
            'floor' => $request->floor,
            'number_of_floors' => $request->number_of_floors,
            'rooms' => $request->rooms,
            'city' => $request->city,
            'type_of_wc' => $request->type_of_wc, // Options: 'farangi', 'wc', 'both'
            'elevator' => $request->elevator, // boolean: true or false
            'parking' => $request->parking, // boolean: true or false
        ]);

        // Add images to the ad
        if ($request->hasFile('images')) {
            $ad->images()->createMany(
                collect($request->file('images'))->map(function ($image) {
                    return [
                        'path' => $image->store('/ads/images'),
                    ];
                })->toArray()
            );
        }

        return response()->json([
            $ad,
        ], 200);
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

    public function saveAds($adId)
    {

        $ad = Ad::findOrFail($adId);

        $user = Auth::user();

        $user->savedAds()->attach($ad);

        return response()->json(['message' => 'Ad saved successfully!'], 201);

    }
}
