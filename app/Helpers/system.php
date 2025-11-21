<?php

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
