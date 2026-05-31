<?php

declare(strict_types=1);

namespace App\Domain\Enums;

enum UserRole: string
{
    case USER = 'user';
    case ADMIN = 'admin';
    case MODERATOR = 'moderator';

    public function label(): string
    {
        return match ($this) {
            self::USER => 'Người dùng',
            self::ADMIN => 'Quản trị viên',
            self::MODERATOR => 'Người kiểm duyệt',
        };
    }

    public function isAdmin(): bool
    {
        return $this === self::ADMIN;
    }
}
