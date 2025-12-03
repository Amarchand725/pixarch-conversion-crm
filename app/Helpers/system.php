<?php

use App\Models\Permission;
use Carbon\Carbon;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use PhpParser\Node\Scalar\MagicConst\Dir;

function exceptionErrors($exception)
{
    $code = (int) $exception->getCode();
    $httpCode = ($code >= 500 || $code < 200) ? 500 : (int)$code;
    switch (class_basename($exception)) {
        case 'UnauthorizedException':
            $response = errorResponse('You do not have required authorization.', 403);
            break;

        case 'AuthenticationException':
            return errorResponse('You are not authenticated. Please login.', 401);

        case 'ValidationException':
            $response = errorResponse($exception->getMessage(), 422, $exception->errors());
            break;

        case 'ModelNotFoundException':
            $modelBaseName = basename($exception->getModel());
            $ids  = $exception->getIds();
            $ids = implode(',', $ids);
            $response = errorResponse("No record found for model {$modelBaseName}"
                . ($ids != null ? " with ID {$ids}." : '.'), 404);
            break;

        case 'NotFoundHttpException':
            $response = errorResponse('The specified URL cannot be found.', 404);
            break;

        case 'AuthorizationException':
            $response = errorResponse($exception->getMessage(), 403);
            break;

        case 'MethodNotAllowedHttpException':
            $response = errorResponse($exception->getMessage(), 405);
            break;

        case 'ErrorException':
            $response = errorResponse($exception->getMessage(), $code);
            break;
        case 'Error':

            $message = $exception->getMessage();
            $twillioFind = str_contains($message, "Unable to create record: Authenticate",);
            preg_match('/\b\d{3}\b/', $message, $matches);
            $statusCode = @$matches[0] ? 400 : $code;
            $message = $twillioFind != '' ?
                "Unable to send message due to insufficient Twillio balance" : $message;
            $response = errorResponse($message,  $statusCode);
            break;


        default:
            $errors = [];
            if(config('app.debug')) {
                $errors =$exception->getTrace()?? $exception->getError();
            }
            $response = errorResponse($exception->getMessage(),  $httpCode, $errors);
            break;
    }


    return $response;
}

function generateOtp($otpLength = 6)
{
    return str_pad(mt_rand(0, pow(10, $otpLength) - 1), $otpLength, '0', STR_PAD_LEFT);
}

function getDateTimeFormat($dateTime){
    if (!$dateTime) {
        return '-';
    }

    try {
        return Carbon::parse($dateTime)->format('d, M Y | h:i A');
    } catch (\Exception $e) {
        return $dateTime; // fallback to raw value if parse fails
    }
}

function getDateFormat($date){
    return Carbon::parse($date)->format('d, M Y');
}


function SubPermissions($label)
{
    return Permission::where('label', $label)->get();
}

// Function to group permissions by their common prefix
function groupPermissions($permissions) {
    $groups = [];

    foreach ($permissions as $permission) {
        // Extract group name before the hyphen
        $groupName = strtok($permission, '-');

        // Add permission to the group
        $groups[$groupName][] = $permission;
    }

    return $groups;
}

function subPermissionFields(){
    return $sub_permission_fields = [
        'list' => 'list',
        'create' => 'create',
        'show' => 'show',
        'edit' => 'edit',
        'delete' => 'delete',
        'status' => 'status',
        'trashed' => 'trashed',
        'restore' => 'restore',
    ];
}

function badgeClass(string $status): string
{
    $statusColors = [
        'created'             => 'bg-primary text-white',
        'assigned'            => 'bg-info text-white',
        'no contacted'        => 'bg-secondary text-white',
        'contact established' => 'bg-warning text-white',
        'junk'                => 'bg-danger text-white',
        'potential'           => 'bg-success text-white',
        'follow up'           => 'bg-info text-white',
        'hot client'          => 'bg-danger text-white',
        'sales closed'        => 'bg-success text-white',
        'pool'                => 'bg-info text-white',
        'active'              => 'badge bg-success',
        'de-active'           => 'badge bg-danger',
    ];

    return $statusColors[strtolower($status)] ?? 'bg-dark text-white';
}

function statuses(){
    return [
        'open',
        'lost',
        'won',
        'abandoned'
    ];
}

function campaignTypes(): array
{
    return [
        'Email',
        'Social',
        'Call',
    ];
}