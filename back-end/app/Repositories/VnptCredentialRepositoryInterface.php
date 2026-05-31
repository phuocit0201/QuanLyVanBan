<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Domain\Entities\VnptCredentialEntity;
use Illuminate\Database\Eloquent\ModelNotFoundException;

interface VnptCredentialRepositoryInterface
{
    public function findByUser(int $userId): ?VnptCredentialEntity;

    public function save(int $userId, string $username, string $password, ?string $deviceName = null, ?string $deviceType = null): VnptCredentialEntity;

    public function deleteByUser(int $userId): bool;
}
