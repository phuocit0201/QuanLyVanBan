<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\DTOs\LoginDTO;
use App\DTOs\RegisterDTO;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\TokenResource;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;

/**
 * Auth Controller - Presentation Layer.
 * Thin controller that delegates to Application Layer (Services).
 */
class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $authService,
    ) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $dto = RegisterDTO::fromRequest($request);

        $result = $this->authService->register($dto);

        return ApiResponse::created(
            data: [
                'user' => new UserResource($result['user']),
                'token' => new TokenResource($result['token']->toArray()),
            ],
            message: trans('messages.auth.registered'),
        );
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $dto = LoginDTO::fromRequest($request);

        $token = $this->authService->login($dto);

        return ApiResponse::success(
            data: [
                'token' => new TokenResource($token->toArray()),
            ],
            message: trans('messages.auth.login_success'),
        );
    }

    public function logout(): JsonResponse
    {
        $this->authService->logout();

        return ApiResponse::success(
            message: trans('messages.auth.logout_success'),
        );
    }

    public function refresh(): JsonResponse
    {
        $token = $this->authService->refresh();

        return ApiResponse::success(
            data: [
                'token' => new TokenResource($token->toArray()),
            ],
            message: trans('messages.auth.token_refreshed'),
        );
    }

    public function me(): JsonResponse
    {
        $user = $this->authService->getAuthenticatedUser();

        return ApiResponse::success(
            data: new UserResource($user),
        );
    }
}
