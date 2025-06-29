<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'firstName'   => $this->firstName,
            'lastName'    => $this->lastName,
            'phoneNumber' => $this->phoneNumber,
            'image'       => $this->image,
            'role'        => $this->roles->first()?->name,
            'active_ads'  => $this->ads_count,
            'created_at'  => $this->created_at
        ];
    }
}
