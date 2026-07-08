<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    /**
     * Features gated by plan tier.
     */
    private array $planFeatures = [
        'free' => [
            'max_employees' => 3,
            'screenshots' => true,
            'webcam_capture' => false,
            'live_monitoring' => false,
            'face_recognition' => false,
            'keystroke_tracking' => false,
            'system_seize' => false,
        ],
        'starter' => [
            'max_employees' => 15,
            'screenshots' => true,
            'webcam_capture' => true,
            'live_monitoring' => false,
            'face_recognition' => false,
            'keystroke_tracking' => false,
            'system_seize' => false,
        ],
        'pro' => [
            'max_employees' => 50,
            'screenshots' => true,
            'webcam_capture' => true,
            'live_monitoring' => true,
            'face_recognition' => true,
            'keystroke_tracking' => true,
            'system_seize' => true,
        ],
        'enterprise' => [
            'max_employees' => 9999,
            'screenshots' => true,
            'webcam_capture' => true,
            'live_monitoring' => true,
            'face_recognition' => true,
            'keystroke_tracking' => true,
            'system_seize' => true,
        ],
    ];

    /**
     * Handle an incoming request.
     * Checks if the user's subscription plan allows the requested feature.
     *
     * Usage in routes: ->middleware('subscription:live_monitoring')
     */
    public function handle(Request $request, Closure $next, string $feature = null): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        $plan = $user->plan_type ?? 'free';
        $features = $this->planFeatures[$plan] ?? $this->planFeatures['free'];

        // If a specific feature is required, check it
        if ($feature && isset($features[$feature]) && !$features[$feature]) {
            return response()->json([
                'error' => 'Feature not available on your current plan.',
                'feature' => $feature,
                'current_plan' => $plan,
                'upgrade_required' => true,
            ], 403);
        }

        // Share plan features with the request for downstream use
        $request->merge(['_plan_features' => $features]);

        return $next($request);
    }
}
