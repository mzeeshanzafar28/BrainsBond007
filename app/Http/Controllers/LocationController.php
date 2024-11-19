<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LocationController extends Controller
{
    public function verify_location(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|integer',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $employee = DB::table('employees')->where('id', $validated['employee_id'])->first();

        if (!$employee) {
            return response()->json(['error' => 'Employee not found'], 404);
        }

        $user_lat = $validated['latitude'];
        $user_long = $validated['longitude'];

        $allowed_locations = [];

        if ($employee->allow_remote) {
            $remote_locations = json_decode($employee->remote_locations, true);
            if ($remote_locations) {
                $allowed_locations = array_merge($allowed_locations, $remote_locations);
            }
        } else {
            $org_location = DB::table('props')->where('user_id', $employee->id)->value('organization_location');
            if ($org_location) {
                $allowed_locations[] = json_decode($org_location, true);
            }
        }

        foreach ($allowed_locations as $location) {
            $distance = $this->calculate_distance($user_lat, $user_long, $location['latitude'], $location['longitude']);
            if ($distance <= 0.1) { // 0.1 km = 100 meters
                return response()->json(['status' => true]);
            }
        }

        return response()->json(['status' => false]);
    }

    private function calculate_distance($lat1, $lon1, $lat2, $lon2)
    {
        $earth_radius = 6371;

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earth_radius * $c; 
    }
}
