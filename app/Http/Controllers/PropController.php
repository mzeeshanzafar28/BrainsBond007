<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePropRequest;
use App\Http\Requests\UpdatePropRequest;
use App\Http\Resources\PropResource;
use App\Models\Prop;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class PropController extends Controller
{
    /**
     * Add new organization prop/client config.
     */
    public function add_prop(StorePropRequest $request): JsonResponse
    {
        $lastPort = Prop::max('port');
        $port = $lastPort ? $lastPort + 1 : 1000;
        $siteUrl = config('app.url');
        $authSlug = Str::slug(Auth::user()->name);
        $authId = Auth::id();
        $connectionUrl = "{$siteUrl}/{$authSlug}/{$authId}/{$port}";

        $prop = Prop::create(array_merge(
            $request->validated(),
            [
                'user_id' => $authId,
                'is_premium' => false,
                'port' => $port,
                'connection_url' => $connectionUrl,
            ]
        ));

        return response()->json(new PropResource($prop), 201);
    }

    /**
     * Update an organization prop config.
     */
    public function update_prop(UpdatePropRequest $request): JsonResponse
    {
        $prop = Prop::where('id', $request->validated('prop_id'))
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $prop->update($request->validated());

        return response()->json(new PropResource($prop->fresh()), 200);
    }

    /**
     * Delete an organization prop config.
     */
    public function delete_prop(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'prop_id' => 'required|integer|exists:props,id'
        ]);

        $prop = Prop::where('id', $validated['prop_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $prop->delete();

        return response()->json(['message' => 'Prop deleted successfully'], 200);
    }

    /**
     * Get all organization props for authenticated admin.
     */
    public function get_props(): JsonResponse
    {
        $props = Prop::forAdmin()->get();

        return response()->json(PropResource::collection($props), 200);
    }

    /**
     * Call the external compiler to build a personalized client EXE.
     */
    public function generate_exe(): JsonResponse
    {
        $user = Auth::user();
        $prop = Prop::where('user_id', $user->id)->latest()->first();

        if (!$prop) {
            return response()->json(['error' => 'No prop found for the authenticated user.'], 404);
        }

        $exeGeneratorServerUrl = env('EXE_GENERATOR_SERVER_URL');

        if (!$exeGeneratorServerUrl) {
            return response()->json(['error' => 'EXE Generator Server URL is not configured.'], 500);
        }

        try {
            $response = Http::post($exeGeneratorServerUrl, [
                'connection_url' => $prop->connection_url,
                'port' => $prop->port,
            ]);

            if ($response->successful()) {
                return response()->json(['status' => 'Waiting for exe to be generated.']);
            }
        } catch (\Exception $e) {
            // Log or handle
        }

        return response()->json(['error' => 'Failed to generate exe.'], 500);
    }
}
