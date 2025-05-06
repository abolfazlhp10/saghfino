<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;
use App\Models\Ad;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class AdService
{

    /**
     * Get paginated ads
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */

    public function getPaginatedAds(int $perPage): LengthAwarePaginator
    {
        return Ad::with(['user', 'images'])
            ->latest()
            ->paginate($perPage);
    }

}
