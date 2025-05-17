<?php
namespace App\Http\Controllers\Core;

use App\Classes\ResponseMessages;
use App\Http\Controllers\Controller;
use App\Services\RealEstateService;
use Illuminate\Http\JsonResponse;

class RealEstatesController extends Controller
{

    public function __construct(private readonly RealEstateService $realEstateService)
    {

    }

    public function getRealEstateAgents(): JsonResponse
    {
        $real_estate_agents = $this->realEstateService->getAllRealEstateAgents();

        return response()->json($real_estate_agents);
    }

    public function getRealEstateAgentById($agent_id): JsonResponse
    {

        $agent = $this->realEstateService->getRealEstateAgentById($agent_id);

        if ($agent == null) {
            return response()->json(['message' => ResponseMessages::NOT_FOUND]);
        }

        return response()->json($agent);
    }

}
