<?php

use App\Models\Permission;
use App\Models\Status;
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

function pipelines(){
    return [
        'paid social - leads',
        'sales pipeline',
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

if (!function_exists('activityEventBadgeClass')) {
    function activityEventBadgeClass(string $event): string
    {
        $classes = [
            'created'  => 'bg-success text-white',
            'updated'  => 'bg-warning text-white',
            'deleted'  => 'bg-danger text-white',
            'restored' => 'bg-info text-white',
        ];

        return $classes[strtolower($event)] ?? 'bg-secondary text-white';
    }
}

if (!function_exists('module_label')) {
    function module_label(string $action, string $module, string $field = null)
    {
        $moduleName = __('labels.' . $module);

        switch ($action) {
            case 'add':
                return __('ui.add_item', ['module' => $moduleName]);
            case 'edit':
                return __('ui.edit_item', ['module' => $moduleName]);
            case 'list':
                return __('ui.list_item', ['module' => $moduleName]);
            case 'save':
                return __('ui.save', ['module' => $moduleName]);
            case 'delete':
                return __('ui.delete', ['module' => $moduleName]);
            case 'module_title':
                return __('ui.module_title', ['module' => $moduleName]);
            case 'placeholder':
                if ($field) {
                    $fieldName = __('labels.' . $field);
                    return __('ui.enter_field', ['field' => $fieldName]);
                }
                return '';
            case 'tooltip_add':
                return __('ui.tooltip_add', ['module' => $moduleName]);
            case 'tooltip_edit':
                return __('ui.tooltip_edit', ['module' => $moduleName]);
            case 'tooltip_delete':
                return __('ui.tooltip_delete', ['module' => $moduleName]);
            default:
                return '';
        }
    }
}

if (!function_exists('module_message')) {
    function module_message(string $type, string $module, string $user = null)
    {
        $moduleName = __('labels.' . $module);
        $params = ['module' => $moduleName];
        if ($user) {
            $params['user'] = $user;
        }
        return __('messages.' . $type, $params);
    }
}

function statusName($model, $status_id){
    $status = Status::where('model', $model)->where('id', $status_id)->first();
    return $status ? $status->name : null;
}

// @php
// $module = 'users';
// $field = 'name';
// @endphp

// <h1>{{ module_label('module_title', $module) }}</h1>
// <input type="text" placeholder="{{ module_label('placeholder', $module, $field) }}">
// <button title="{{ module_label('tooltip_add', $module) }}">{{ module_label('add', $module) }}</button>

// @if(session('success'))
//     <div class="alert alert-success">
//         {{ module_message('created', $module) }}
//     </div>
// @endif
