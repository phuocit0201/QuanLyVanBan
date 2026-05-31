<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\VnptCredentialEntity;
use App\Infrastructure\Models\VnptCredential;
use App\Repositories\VnptCredentialRepositoryInterface;

class EloquentVnptCredentialRepository implements VnptCredentialRepositoryInterface
{
    public function findByUser(int $userId): ?VnptCredentialEntity
    {
        $model = VnptCredential::where('user_id', $userId)
            ->where('is_active', true)
            ->first();

        return $model ? VnptCredentialEntity::find($model->id) : null;
    }

    public function save(int $userId, string $username, string $password, ?string $deviceName = null, ?string $deviceType = null): VnptCredentialEntity
    {
        $model = VnptCredential::updateOrCreate(
            ['user_id' => $userId],
            [
                'username' => $username,
                'password' => $password,
                'device_name' => $deviceName,
                'device_type' => $deviceType ?? VnptCredentialEntity::DEVICE_IOS,
                'is_active' => true,
            ]
        );

        return VnptCredentialEntity::find($model->id);
    }

    public function deleteByUser(int $userId): bool
    {
        return (bool) VnptCredential::where('user_id', $userId)->delete();
    }
}
