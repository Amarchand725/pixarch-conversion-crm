<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PermissionActionsMiddleware
{
    /**
     * Universal action-to-permission suffix map
     * Used by all modules
     */
    protected array $map = [
        // Standard REST actions
        'index' => 'list',
        'store' => 'create',
        'show' => 'view',
        'update' => 'edit',
        'destroy' => 'delete',

        // Common extended CRUD actions
        'bulkDelete' => 'bulk-delete',
        'permanentlyDestroy' => 'permanent-delete',
        'restore' => 'restore',
        'exportCsv' => 'export',
        'export' => 'export',
        'download' => 'download',

        // User-specific
        'directPermission' => 'direct-permission',
        'statusUpdate' => 'status',

        // Lead-specific
        'assignUser' => 'assign-user',
        'assignedUsers' => 'assigned-users-list',
        'updateStatus' => 'status',

        // Team-specific
        'assignUsers' => 'assign-user',
        'removeUser' => 'remove-user',
    ];

    /**
     * Handle an incoming request.
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
                return response()->json([
                    'success' => false,
                    "status_code" => 403,
                    'message' => "You do not have required authorization: {$permission}.",
                ], 403);
            }
        }

        return $next($request);
    }
}
