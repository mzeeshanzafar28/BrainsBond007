<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AgentLoginRequest;
use App\Http\Resources\EmployeeResource;
use App\Models\Employee;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AgentAuthController extends Controller
{
    /**
     * Authenticate the desktop agent.
     * Employee provides their admin_id + email, and the system issues a Sanctum token.
     */
    public function login(AgentLoginRequest $request): JsonResponse
    {
        $employee = Employee::where('user_id', $request->validated('admin_id'))
            ->where('email', $request->validated('employee_email'))
            ->first();

        if (!$employee) {
            return response()->json([
                'error' => 'Employee not found for this organization.'
            ], 404);
        }

        if ($employee->status !== 'active') {
            return response()->json([
                'error' => 'Employee account is not active.'
            ], 403);
        }

        // Use the admin user to create a Sanctum token scoped to this employee
        $user = $employee->user;
        $token = $user->createToken('agent-' . $employee->id, [
            'agent',
            'employee:' . $employee->id,
        ]);

        return response()->json([
            'token' => $token->plainTextToken,
            'employee' => new EmployeeResource($employee),
            'admin_id' => $request->validated('admin_id'),
        ]);
    }

    /**
     * Revoke the current agent token.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Agent logged out successfully.']);
    }
}
