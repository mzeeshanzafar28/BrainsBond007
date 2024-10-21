<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeController extends Controller
{

    public function add_employee(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email',
            'face_images' => 'required|json',
            'cnic' => 'required|string|max:15',
            'start_working_hour' => 'required|date_format:H:i',
            'end_working_hour' => 'required|date_format:H:i',
            'allow_remote' => 'boolean',
            'remote_locations' => 'nullable|json',
            'is_seized' => 'boolean',
            'screenshots' => 'nullable|json',
        ]);

        $employee = Employee::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'email' => $request->email,
            'face_images' => $request->face_images,
            'cnic' => $request->cnic,
            'start_working_hour' => $request->start_working_hour,
            'end_working_hour' => $request->end_working_hour,
            'allow_remote' => $request->allow_remote ?? false,
            'remote_locations' => $request->remote_locations,
            'is_seized' => $request->is_seized ?? false,
            'screenshots' => $request->screenshots,
        ]);

        return response()->json($employee, 201);
    }

    public function update_employee(Request $request)
    {
        $employee_id = $request->validate([
            'employee_id' => 'required|exists:employees,id',
        ]);
        $employee = Employee::where('id', $employee_id)->where('user_id', Auth::id())->firstOrFail();

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:employees,email,' . $employee->id,
            'face_images' => 'sometimes|required|json',
            'cnic' => 'sometimes|required|string|max:15',
            'start_working_hour' => 'sometimes|required|date_format:H:i',
            'end_working_hour' => 'sometimes|required|date_format:H:i',
            'allow_remote' => 'boolean',
            'remote_locations' => 'nullable|json',
            'is_seized' => 'boolean',
            'screenshots' => 'nullable|json',
        ]);

        $employee->update($request->only([
            'name',
            'email',
            'face_images',
            'cnic',
            'start_working_hour',
            'end_working_hour',
            'allow_remote',
            'remote_locations',
            'is_seized',
            'screenshots',
        ]));

        return response()->json($employee, 200);
    }

    public function delete_employee(Request $request)
    {
        $employee_id = $request->validate([
            'employee_id' => 'required|exists:employees,id',
        ]);
        $employee = Employee::where('id', $employee_id)->where('user_id', Auth::id())->firstOrFail();

        $employee->delete();

        return response()->json(['message' => 'Employee deleted successfully'], 200);
    }

    public function get_employees()
    {
        $user_id = Auth::id();
        $employees = Employee::where('user_id', $user_id)->get();

        return response()->json($employees, 200);
    }
}
