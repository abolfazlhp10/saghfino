<?php
namespace App\Http\Controllers\Core;

use App\Actions\RealesateUpdateAction;
use App\Classes\ResponseMessages;
use App\Http\Controllers\Controller;
use App\Http\Requests\Core\CreateRealEstateRequest;
use App\Http\Requests\Core\RateRealEstateRequest;
use App\Http\Requests\Core\RealEstateEditRequest;
use App\Models\RealEstate;
use App\Services\RealEstateService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RealEstatesController extends Controller
{

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

        if (! $result) {

            return response()->json(['message' => ResponseMessages::REAL_ESTATE_RATE_ERROR], Response::HTTP_INTERNAL_SERVER_ERROR);

        }

        return response()->json(['message' => ResponseMessages::REAL_ESTATE_RATE_ADDED], Response::HTTP_OK);
    }

    public function updateRealEstateProfile(RealEstateEditRequest $request,RealEstate $realestate,RealesateUpdateAction $realesateUpdateAction): JsonResponse 
    {
        try {
            $updatedRealEstate = $realesateUpdateAction->execute($request, $realestate);

            return response()->json([
                'message'    => ResponseMessages::REALESTATE_PROFILE_UPDATED_SUCCESS,
                'realestate' => $updatedRealEstate->fresh(),
            ], 200);

        } catch (\Throwable $e) {
            Log::error('Error updating real estate profile: ', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => ResponseMessages::PROFILE_UPDATED_ERROR,
            ], 500);
        }
    }
}
