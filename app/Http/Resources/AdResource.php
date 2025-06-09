<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [
            'id'                   => $this->id,
            'title'                => $this->title,
            'description'          => $this->description,
            'address'              => $this->address,
            'area'                 => $this->area,
            'floor'                => $this->floor,
            'number_of_floors'     => $this->number_of_floors,
            'rooms'                => $this->rooms,
            'city'                 => $this->city,
            'type_of_wc'           => $this->type_of_wc,
            'elevator'             => $this->elevator,
            'parking'              => $this->parking,
            'transaction_type'     => $this->transaction_type,
            'property_type'        => $this->property_type,
            'sell_price'           => $this->sell_price,
            'rent_price'           => $this->rent_price,
            'mortgage_price'       => $this->mortgage_price,
            'status'               => $this->status,
            'created_at'           => $this->created_at,

            'is_saved'             => $this->is_saved,
            'saved_by_users_count' => $this->saved_by_users_count,
            'images'               => $this->images,
            'user'                 => new UserResource($this->whenLoaded('user')),
            'similar_ads'          => $this->similar_ads,
        ];
    }
}
