<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Helpers\ApiResponse;
use Exception;
use Illuminate\Http\JsonResponse;

/**
 * Exception thrown when a user is not found.
 */
class UserNotFoundException extends Exception
{
    public function __construct(string $identifier)
    {
        parent::__construct("User not found: {$identifier}");
    }

    public function render(): JsonResponse
    {
        return ApiResponse::notFound(trans('messages.auth.user_not_found'));
    }
}
