<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantIsolation
{
    /**
     * Ensure all data access is scoped to the authenticated admin (tenant).
     * This middleware sets a global 'tenant_id' that models can reference.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user) {
            // Set tenant context for the request lifecycle
            app()->instance('tenant_id', $user->id);

            // Share tenant info with views
            view()->share('tenant', [
                'id' => $user->id,
                'name' => $user->organization_name ?? $user->name,
                'plan' => $user->plan_type ?? 'free',
            ]);
        }

        return $next($request);
    }
}
