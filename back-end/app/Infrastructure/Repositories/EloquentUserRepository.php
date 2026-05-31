<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\UserEntity;
use App\Domain\Enums\UserRole;
use App\Infrastructure\Models\User;
use App\Repositories\UserRepositoryInterface;

/**
 * Eloquent Repository Implementation - Infrastructure Layer.
 * Concrete implementation of UserRepositoryInterface using Eloquent.
 */
class EloquentUserRepository implements UserRepositoryInterface
{
    public function findById(int $id): ?UserEntity
    {
        $model = User::find($id);

        if ($model === null) {
            return null;
        }

        return $this->toEntity($model);
    }

    public function findByEmail(string $email): ?UserEntity
    {
        $model = User::where('email', $email)->first();

        if ($model === null) {
            return null;
        }

        return $this->toEntity($model);
    }

    public function create(array $data): UserEntity
    {
        $model = User::create($data);

        return $this->toEntity($model);
    }

    public function update(int $id, array $data): ?UserEntity
    {
        $model = User::find($id);

        if ($model === null) {
            return null;
        }

        $model->update($data);

        return $this->toEntity($model->fresh());
    }

    public function delete(int $id): bool
    {
        $model = User::find($id);

        if ($model === null) {
            return false;
        }

        return $model->delete();
    }

    public function findByRole(UserRole $role): array
    {
        return User::where('role', $role->value)
            ->get()
            ->map(fn(User $model) => $this->toEntity($model))
            ->all();
    }

    private function toEntity(User $model): UserEntity
    {
        $entity = new UserEntity();
        $entity->setRawAttributes($model->getAttributes(), true);

        return $entity;
    }
}
