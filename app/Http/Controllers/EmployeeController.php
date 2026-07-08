<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Http\Resources\EmployeeResource;
use App\Models\Employee;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeController extends Controller
{
    /**
     * Add a new employee.
     */
    public function add_employee(StoreEmployeeRequest $request): JsonResponse
    {
        $employee = Employee::create(array_merge(
            $request->validated(),
            ['user_id' => Auth::id()]
        ));

        return response()->json(new EmployeeResource($employee), 201);
    }

    /**
     * Update an employee's details.
     */
    public function update_employee(UpdateEmployeeRequest $request): JsonResponse
    {
        $employee = Employee::where('id', $request->validated('employee_id'))
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $employee->update($request->validated());

        return response()->json(new EmployeeResource($employee->fresh()), 200);
    }

    /**
     * Delete an employee.
     */
    public function delete_employee(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
        ]);

        $employee = Employee::where('id', $validated['employee_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $employee->delete();

        return response()->json(['message' => 'Employee deleted successfully'], 200);
    }

    /**
     * List all employees for the authenticated admin.
     */
    public function get_employees(): JsonResponse
    {
        $employees = Employee::forAdmin()->get();

        return response()->json(EmployeeResource::collection($employees), 200);
    }
}
