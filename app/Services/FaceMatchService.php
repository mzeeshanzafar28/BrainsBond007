<?php

namespace App\Services;

use App\Models\Employee;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class FaceMatchService
{
    protected Client $client;
    protected ?string $url;

    public function __construct()
    {
        $this->client = new Client();
        $this->url = env('FACE_MATCH_SERVICE_URL');
    }

    /**
     * Compare a base64-encoded received face image against all registered face images of an admin's employees.
     */
    public function verifyFaceForAdmin(int $adminId, string $receivedImageBase64): ?int
    {
        // Fetch all active employees for this admin
        $employees = Employee::where('user_id', $adminId)
            ->where('status', 'active')
            ->get();

        foreach ($employees as $employee) {
            $faceImages = is_string($employee->face_images) 
                ? json_decode($employee->face_images, true) 
                : $employee->face_images;

            if (!is_array($faceImages)) {
                continue;
            }

            foreach ($faceImages as $storedImageBase64) {
                $response = $this->compareFaces($storedImageBase64, $receivedImageBase64);

                if ($response && ($response['match'] ?? false)) {
                    return $employee->id;
                }
            }
        }

        return null;
    }

    /**
     * Call the external Python Flask microservice to compare two face images.
     */
    public function compareFaces(string $storedImageBase64, string $receivedImageBase64): ?array
    {
        if (!$this->url) {
            Log::error('Face match service URL is not configured.');
            return null;
        }

        try {
            $response = $this->client->post($this->url, [
                'json' => [
                    'stored_image' => $storedImageBase64,
                    'received_image' => $receivedImageBase64,
                ],
                'timeout' => 15,
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $e) {
            Log::error('Failed to communicate with Face Match Service: ' . $e->getMessage());
            return null;
        }
    }
}
