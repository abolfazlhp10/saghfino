<?php
namespace App\Http\Controllers\Core;

use App\Classes\ResponseMessages;
use App\Http\Controllers\Controller;
use App\Http\Requests\Core\CreateRealEstateRequest;
use App\Http\Requests\Core\RateRealEstateRequest;
use App\Models\RealEstate;
use App\Services\RealEstateService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RealEstatesController extends Controller
{

    use AuthorizesRequests;

    public function __construct(private readonly RealEstateService $realEstateService)
    {}

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

        return response()->json($agent->load(['realEstate']));
    }

    public function save(CreateRealEstateRequest $request)
    {
        try {
            $this->authorize('create', RealEstate::class);
        } catch (Exception $e) {
            return response()->json(['message' => 'Unauthorize action ! '], Response::HTTP_UNAUTHORIZED);
        }

        $result = $this->realEstateService->createNewRealEstate($request);

        if ($result) {
            return response()->json(['message' => ResponseMessages::REAL_ESTATE_CREATE_SUCCESS]);
        } else {
            return response()->json(['message' => ResponseMessages::REAL_ESTATE_CREATE_ERROR]);
        }
    }

    public function getAllRealEstates(Request $request)
    {
        return $this->realEstateService->getAllRealEstates($request);
    }

    public function getRealEstateById(int $id)
    {
        try {
            $realEstate = RealEstate::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => ResponseMessages::REAL_ESTATE_NOT_FOUND], Response::HTTP_NOT_FOUND);
        }

        return $realEstate->load(['agent']);
    }

    public function rateRealEsate(RateRealEstateRequest $request): JsonResponse
    {
        $result = $this->realEstateService->saveRate($request);

        if (!$result) {

            return response()->json(['message' => ResponseMessages::REAL_ESTATE_RATE_ERROR], Response::HTTP_INTERNAL_SERVER_ERROR);

        }

        return response()->json(['message' => ResponseMessages::REAL_ESTATE_RATE_ADDED], Response::HTTP_OK);
    }
}
