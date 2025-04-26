<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Http\Requests\Core\CreateAdRequest;
use App\Models\Ad;

class AdController extends Controller
{
    public function store(CreateAdRequest $request)
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
                        'path' => str_replace('public/', '', $image->store('public/ads/images')),
                    ];
                })->toArray()
            );
        }

        return response()->json([
            $ad,
        ], 200);

    }

}
