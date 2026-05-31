<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Domain\Entities\UserEntity;
use App\Domain\Enums\UserRole;

/**
 * Repository Interface - Domain Layer.
 * Defines contracts for data access, independent of implementation.
 */
interface UserRepositoryInterface
{
    public function findById(int $id): ?UserEntity;

    public function findByEmail(string $email): ?UserEntity;

    public function create(array $data): UserEntity;

    public function update(int $id, array $data): ?UserEntity;

    public function delete(int $id): bool;

    public function findByRole(UserRole $role): array;
}
