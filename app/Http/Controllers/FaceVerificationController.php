<?php

namespace App\Http\Controllers;

use App\Http\Requests\VerifyFaceRequest;
use App\Services\FaceMatchService;
use Illuminate\Http\JsonResponse;

class FaceVerificationController extends Controller
{
    protected FaceMatchService $faceMatchService;

    public function __construct(FaceMatchService $faceMatchService)
    {
        $this->faceMatchService = $faceMatchService;
    }

    /**
     * Verify received face against all active employees belonging to the specified admin.
     */
    public function verify_face(VerifyFaceRequest $request): JsonResponse
    {
        $matchingEmployeeId = $this->faceMatchService->verifyFaceForAdmin(
            $request->validated('admin_id'),
            $request->validated('image')
        );

        if ($matchingEmployeeId) {
            return response()->json(['employee_id' => $matchingEmployeeId]);
        }

        return response()->json(['error' => 'No matching employee found'], 404);
    }
}
