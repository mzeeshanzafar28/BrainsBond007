<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\Location;
use Illuminate\Support\Facades\DB;

class LocationService
{
    /**
     * Verify if the coordinates fall within allowed locations (office/remote).
     */
    public function verifyLocation(int $employeeId, float $latitude, float $longitude): bool
    {
        $employee = Employee::find($employeeId);

        if (!$employee) {
            return false;
        }

        $allowedLocations = [];

        if ($employee->allow_remote) {
            $remoteLocations = is_string($employee->remote_locations) 
                ? json_decode($employee->remote_locations, true) 
                : $employee->remote_locations;

            if (is_array($remoteLocations)) {
                $allowedLocations = array_merge($allowedLocations, $remoteLocations);
            }
        } else {
            // Check defined location props for the admin/user who created this employee
            $orgLocations = Location::where('user_id', $employee->user_id)
                ->active()
                ->get();

            foreach ($orgLocations as $loc) {
                $allowedLocations[] = [
                    'latitude' => $loc->latitude,
                    'longitude' => $loc->longitude,
                    'radius_meters' => $loc->radius_meters,
                ];
            }
        }

        // If no locations are registered, default to false or fall back to old props table location logic
        if (empty($allowedLocations)) {
            $orgLocation = DB::table('props')->where('user_id', $employee->user_id)->value('organization_location');
            if ($orgLocation) {
                $decoded = json_decode($orgLocation, true);
                if ($decoded) {
                    $allowedLocations[] = array_merge($decoded, ['radius_meters' => 100]);
                }
            }
        }

        foreach ($allowedLocations as $location) {
            $radiusKm = ($location['radius_meters'] ?? 100) / 1000; // default 100 meters
            $distance = $this->calculateDistance($latitude, $longitude, $location['latitude'], $location['longitude']);
            
            if ($distance <= $radiusKm) {
                return true;
            }
        }

        return false;
    }

    /**
     * Calculate distance between two GPS coordinates using Haversine formula (in km).
     */
    public function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371; // km

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
