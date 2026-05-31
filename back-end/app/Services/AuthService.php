<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\LoginDTO;
use App\DTOs\RegisterDTO;
use App\DTOs\TokenDTO;
use App\Domain\Entities\UserEntity;
use App\Repositories\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * Application Service - Orchestrates authentication workflows.
 * Depends on Repository Interface (Dependency Inversion).
 */
class AuthService
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
    ) {}

    public function register(RegisterDTO $dto): array
    {
        $user = $this->userRepository->create([
            'name' => $dto->name,
            'email' => $dto->email,
            'password' => Hash::make($dto->password),
        ]);

        $token = JWTAuth::fromUser($user);

        return [
            'user' => $user,
            'token' => $this->buildTokenDTO($token),
        ];
    }

    public function login(LoginDTO $dto): TokenDTO
    {
        $credentials = $dto->getCredentials();

        $token = JWTAuth::attempt($credentials);

        if ($token === false) {
            throw new \App\Exceptions\AuthenticationException('Invalid credentials');
        }

        return $this->buildTokenDTO($token);
    }

    public function logout(): void
    {
        JWTAuth::invalidate(JWTAuth::getToken());
    }

    public function refresh(): TokenDTO
    {
        $token = JWTAuth::refresh(JWTAuth::getToken());

        return $this->buildTokenDTO($token);
    }

    public function getAuthenticatedUser(): UserEntity
    {
        $user = JWTAuth::parseToken()->authenticate();

        if ($user === false) {
            throw new \App\Exceptions\AuthenticationException('User not found');
        }

        return $user;
    }

    private function buildTokenDTO(string $token): TokenDTO
    {
        return new TokenDTO(
            accessToken: $token,
            tokenType: 'bearer',
            expiresIn: (int) (JWTAuth::factory()->getTTL() * 60),
        );
    }
}
