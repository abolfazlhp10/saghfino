<?php
namespace App\Actions;

use App\Models\RealEstate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RealesateUpdateAction
{
    public function execute(Request $request,RealEstate $realestate): RealEstate
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {

            if ($realestate->image && Storage::exists($realestate->image)) {
                Storage::delete($realestate->image);
            }

            $data['image'] = $request->file('image')->store('realestates/images');
        }

        $realestate->fill($data);

        $realestate->save();

        return $realestate;

    }

}
