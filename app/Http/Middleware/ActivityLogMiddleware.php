<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\ActivityLog;

class ActivityLogMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (auth()->check()) {
            $action = $request->method() . ' ' . $request->path();
            $description = $this->getDescription($request);

            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => $action,
                'description' => $description,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now(),
            ]);
        }

        return $response;
    }

    private function getDescription(Request $request): string
    {
        $method = $request->method();
        $path = $request->path();
        $routeName = $request->route()?->getName();

        if ($routeName) {
            return "Accessed route: {$routeName}";
        }

        return "{$method} request to {$path}";
    }
}