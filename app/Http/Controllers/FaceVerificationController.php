<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FaceVerificationController extends Controller
{
    public function verify_face(Request $request)
    {
        $validated = $request->validate([
            'admin_id' => 'required|integer',
            'image' => 'required|string', // Base64 encoded image
        ]);

        $admin_id = $validated['admin_id'];
        $received_image = base64_decode($validated['image']);

        // Fetch all employees of this admin
        $employees = DB::table('employees')
            ->where('user_id', $admin_id)
            ->select('id', 'face_images')
            ->get();

        if ($employees->isEmpty()) {
            return response()->json(['error' => 'No employees found for this admin'], 404);
        }

        // Prepare for image comparison
        $face_match_service_url = env('FACE_MATCH_SERVICE_URL'); // Set in .env file
        $matching_employee_id = null;

        foreach ($employees as $employee) {
            $face_images = json_decode($employee->face_images, true); // Decode JSON

            foreach ($face_images as $stored_image) {
                // Send comparison request to the face match service
                $response = $this->compare_faces($face_match_service_url, $stored_image, $received_image);

                if ($response && $response['match']) {
                    $matching_employee_id = $employee->id;
                    break 2; // Break both loops if a match is found
                }
            }
        }

        if ($matching_employee_id) {
            return response()->json(['employee_id' => $matching_employee_id]);
        } else {
            return response()->json(['error' => 'No matching employee found'], 404);
        }
    }

    private function compare_faces($url, $stored_image, $received_image)
    {
        $client = new \GuzzleHttp\Client();

        try {
            $response = $client->post($url, [
                'json' => [
                    'stored_image' => $stored_image,
                    'received_image' => base64_encode($received_image),
                ],
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $e) {
            return null; // Handle errors
        }
    }
}
