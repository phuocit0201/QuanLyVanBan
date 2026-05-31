<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Helpers\ApiResponse;
use Exception;
use Illuminate\Http\JsonResponse;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

/**
 * Exception thrown when authentication fails.
 */
class AuthenticationException extends Exception
{
    public function __construct(string $message = 'Authentication failed')
    {
        parent::__construct($message);
    }

    public function render(): JsonResponse
    {
        return ApiResponse::unauthorized(trans('messages.auth.invalid_credentials'));
    }
}
