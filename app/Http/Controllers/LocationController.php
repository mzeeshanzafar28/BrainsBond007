<?php

namespace App\Http\Controllers;

use App\Http\Requests\VerifyLocationRequest;
use App\Services\LocationService;
use Illuminate\Http\JsonResponse;

class LocationController extends Controller
{
    protected LocationService $locationService;

    public function __construct(LocationService $locationService)
    {
        $this->locationService = $locationService;
    }

    /**
     * Verify employee coordinates against allowed locations.
     */
    public function verify_location(VerifyLocationRequest $request): JsonResponse
    {
        $verified = $this->locationService->verifyLocation(
            $request->validated('employee_id'),
            $request->validated('latitude'),
            $request->validated('longitude')
        );

        return response()->json(['status' => $verified]);
    }
}
