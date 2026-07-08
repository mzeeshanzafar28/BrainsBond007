<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UploadScreenshotRequest;
use App\Http\Requests\Api\UploadWebcamRequest;
use App\Http\Requests\Api\SubmitActivityRequest;
use App\Http\Resources\ActivityLogResource;
use App\Models\ActivityLog;
use App\Models\Employee;
use App\Models\WorkSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityController extends Controller
{
    /**
     * Upload a screenshot from the desktop agent.
     */
    public function uploadScreenshot(UploadScreenshotRequest $request): JsonResponse
    {
        $employeeId = $request->validated('employee_id');
        $sessionId = $request->validated('session_id');

        $employee = Employee::where('id', $employeeId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $session = WorkSession::where('id', $sessionId)
            ->where('employee_id', $employee->id)
            ->active()
            ->firstOrFail();

        $path = $request->file('screenshot')->store(
            Auth::id() . '/screenshots/' . $employee->id . '/' . now()->toDateString(),
            'local'
        );

        $log = ActivityLog::create([
            'employee_id' => $employee->id,
            'session_id' => $session->id,
            'user_id' => Auth::id(),
            'type' => 'screenshot',
            'file_path' => $path,
            'metadata' => [
                'ip_address' => $request->ip(),
                'resolution' => $request->validated('resolution'),
            ],
            'captured_at' => now(),
        ]);

        return response()->json([
            'message' => 'Screenshot uploaded successfully.',
            'activity_log' => new ActivityLogResource($log),
        ], 201);
    }

    /**
     * Upload a webcam capture from the desktop agent.
     */
    public function uploadWebcamCapture(UploadWebcamRequest $request): JsonResponse
    {
        $employeeId = $request->validated('employee_id');
        $sessionId = $request->validated('session_id');

        $employee = Employee::where('id', $employeeId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $session = WorkSession::where('id', $sessionId)
            ->where('employee_id', $employee->id)
            ->active()
            ->firstOrFail();

        $path = $request->file('image')->store(
            Auth::id() . '/webcam/' . $employee->id . '/' . now()->toDateString(),
            'local'
        );

        $log = ActivityLog::create([
            'employee_id' => $employee->id,
            'session_id' => $session->id,
            'user_id' => Auth::id(),
            'type' => 'webcam',
            'file_path' => $path,
            'metadata' => [
                'ip_address' => $request->ip(),
            ],
            'captured_at' => now(),
        ]);

        return response()->json([
            'message' => 'Webcam capture uploaded successfully.',
            'activity_log' => new ActivityLogResource($log),
        ], 201);
    }

    /**
     * Submit an activity summary (app usage, keystroke data, etc).
     */
    public function submitActivitySummary(SubmitActivityRequest $request): JsonResponse
    {
        $employeeId = $request->validated('employee_id');
        $sessionId = $request->validated('session_id');

        $employee = Employee::where('id', $employeeId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $session = WorkSession::where('id', $sessionId)
            ->where('employee_id', $employee->id)
            ->active()
            ->firstOrFail();

        $log = ActivityLog::create([
            'employee_id' => $employee->id,
            'session_id' => $session->id,
            'user_id' => Auth::id(),
            'type' => $request->validated('type'),
            'metadata' => $request->validated('data'),
            'captured_at' => now(),
        ]);

        return response()->json([
            'message' => 'Activity summary recorded.',
            'activity_log' => new ActivityLogResource($log),
        ], 201);
    }

    /**
     * Get screenshots/activity for an employee (admin endpoint).
     */
    public function getEmployeeActivity(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'employee_id' => 'required|integer|exists:employees,id',
            'type' => 'nullable|in:screenshot,webcam,keystroke_summary,app_usage,idle_alert',
            'date' => 'nullable|date',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        $query = ActivityLog::forAdmin()
            ->where('employee_id', $validated['employee_id']);

        if (!empty($validated['type'])) {
            $query->ofType($validated['type']);
        }

        if (!empty($validated['date'])) {
            $query->whereDate('captured_at', $validated['date']);
        }

        $activities = $query->orderBy('captured_at', 'desc')
            ->paginate($validated['per_page'] ?? 20);

        return response()->json([
            'data' => ActivityLogResource::collection($activities->items()),
            'meta' => [
                'current_page' => $activities->currentPage(),
                'last_page' => $activities->lastPage(),
                'per_page' => $activities->perPage(),
                'total' => $activities->total(),
            ]
        ]);
    }
}
