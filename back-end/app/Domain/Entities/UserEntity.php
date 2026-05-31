<?php

declare(strict_types=1);

namespace App\Domain\Entities;

use App\Infrastructure\Models\User;
use App\Domain\Enums\UserRole;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * Domain Entity - Contains business logic.
 * Extends Infrastructure Model for Eloquent capabilities.
 */
class UserEntity extends User implements JWTSubject
{
    protected $table = 'users';
    public function getJWTIdentifier(): mixed
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        return [
            'role' => $this->role ?? UserRole::USER->value,
        ];
    }

    public function hasRole(UserRole $role): bool
    {
        return ($this->role ?? UserRole::USER->value) === $role->value;
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(UserRole::ADMIN);
    }

    public function isModerator(): bool
    {
        return $this->hasRole(UserRole::MODERATOR);
    }

    public function assignRole(UserRole $role): void
    {
        $this->role = $role->value;
        $this->save();
    }
}
