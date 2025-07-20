<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpgradeBuildingRequest;
use App\Services\VillageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VillageController extends Controller
{
    public function __construct(protected VillageService $villageService)
    {
        // Using PHP 8 constructor property promotion, the service is automatically
        // injected and available as $this->villageService.
    }

    /**
     * Display the authenticated user's village state.
     */
    public function show(Request $request): JsonResponse
    {
        $village = $this->villageService->getVillageForUser($request->user());
        return response()->json($village);
    }

    /**
     * Handle a request to upgrade a building.
     */
    public function upgrade(UpgradeBuildingRequest $request): JsonResponse
    {
        // The $request is already validated here.
        $validated = $request->validated();
        $building = $validated['building'];

        $result = $this->villageService->upgradeBuilding(
            $request->user()->village,
            $building
        );

        if ($result['success']) {
            return response()->json($result['village']);
        }

        return response()->json(['error' => $result['message']], 422); // 422 Unprocessable Entity
    }
}