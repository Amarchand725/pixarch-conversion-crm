<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PermissionActionsMiddleware
{
    /**
     * Universal action-to-permission suffix map
     * Used by all modules (Lead, Team, DBA, etc.)
     */
    protected array $map = [
        // Standard REST actions
        'index' => 'list',
        'store' => 'create',
        'show' => 'view',
        'edit' => 'edit',
        'destroy' => 'delete',

        // Common extended CRUD actions
        'bulkDelete' => 'bulk-delete',
        'permanentDelete' => 'permanent-delete',
        'restore' => 'restore',

        // User-specific
        'directPermission' => 'direct-permission',
        'status' => 'status',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $prefix): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $action = $request->route()->getActionMethod();
        $suffix = $this->map[$action] ?? null;
        if ($suffix) {
            $permission = "{$prefix}-{$suffix}";
            if (!$user->can($permission)) {
                return redirect()->route('back-office.auth.dashboard') // you can create a named route
                ->with('error', "You do not have required authorization");
            }
        }

        return $next($request);
    }
}
