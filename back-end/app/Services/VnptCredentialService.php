<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\VnptCredentialRepositoryInterface;

class VnptCredentialService
{
    public function __construct(
        private readonly VnptCredentialRepositoryInterface $vnptCredentialRepository,
    ) {}

    public function getByUser(int $userId): ?array
    {
        $credential = $this->vnptCredentialRepository->findByUser($userId);

        if ($credential === null) {
            return null;
        }

        return [
            'username' => $credential->username,
            'password' => $credential->password,
            'device' => $credential->device_name,
            'type' => $credential->device_type,
        ];
    }

    public function save(int $userId, array $data): array
    {
        $credential = $this->vnptCredentialRepository->save(
            userId: $userId,
            username: $data['username'],
            password: $data['password'],
            deviceName: $data['device_name'] ?? null,
            deviceType: $data['device_type'] ?? null,
        );

        return [
            'username' => $credential->username,
            'device_name' => $credential->device_name,
            'device_type' => $credential->device_type,
            'is_active' => $credential->is_active,
        ];
    }

    public function delete(int $userId): bool
    {
        return $this->vnptCredentialRepository->deleteByUser($userId);
    }
}
