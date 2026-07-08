<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StartSessionRequest;
use App\Http\Requests\Api\EndSessionRequest;
use App\Http\Requests\Api\HeartbeatRequest;
use App\Http\Resources\WorkSessionResource;
use App\Http\Resources\EmployeeResource;
use App\Models\Employee;
use App\Models\WorkSession;
use App\Services\SessionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SessionController extends Controller
{
    protected SessionService $sessionService;

    public function __construct(SessionService $sessionService)
    {
        $this->sessionService = $sessionService;
    }

    /**
     * Get the employee's working hours.
     */
    public function getWorkingHours(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'employee_id' => 'required|integer|exists:employees,id',
        ]);

        $employee = Employee::where('id', $validated['employee_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return response()->json(new EmployeeResource($employee));
    }

    /**
     * Start a new work session for an employee.
     */
    public function startSession(StartSessionRequest $request): JsonResponse
    {
        $employeeId = $request->validated('employee_id');

        // Check if there's already an active session
        $activeSession = WorkSession::where('employee_id', $employeeId)
            ->active()
            ->first();

        if ($activeSession) {
            return response()->json([
                'error' => 'Employee already has an active session.',
                'session' => new WorkSessionResource($activeSession),
            ], 409);
        }

        $session = $this->sessionService->startSession(
            $employeeId,
            (bool) $request->validated('face_verified', false),
            $request->validated('face_match_score'),
            (bool) $request->validated('location_verified', false),
            $request->ip()
        );

        return response()->json([
            'message' => 'Session started successfully.',
            'session' => new WorkSessionResource($session),
        ], 201);
    }

    /**
     * End a work session.
     */
    public function endSession(EndSessionRequest $request): JsonResponse
    {
        $session = WorkSession::where('id', $request->validated('session_id'))
            ->where('employee_id', $request->validated('employee_id'))
            ->where('user_id', Auth::id())
            ->active()
            ->firstOrFail();

        $endedSession = $this->sessionService->endSession(
            $session,
            (bool) $request->validated('face_verified', false)
        );

        return response()->json([
            'message' => 'Session ended successfully.',
            'session' => new WorkSessionResource($endedSession),
        ]);
    }

    /**
     * Heartbeat ping from the desktop client to track uptime.
     */
    public function heartbeat(HeartbeatRequest $request): JsonResponse
    {
        $session = WorkSession::where('id', $request->validated('session_id'))
            ->where('employee_id', $request->validated('employee_id'))
            ->where('user_id', Auth::id())
            ->active()
            ->firstOrFail();

        $this->sessionService->recordHeartbeat(
            $session,
            (bool) $request->validated('is_active', true)
        );

        return response()->json(['status' => 'ok', 'server_time' => now()->toIso8601String()]);
    }
}
