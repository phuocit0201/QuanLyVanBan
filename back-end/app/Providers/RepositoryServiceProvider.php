<?php

declare(strict_types=1);

namespace App\Providers;

use App\Infrastructure\Repositories\EloquentOfficeDocumentRepository;
use App\Infrastructure\Repositories\EloquentUserRepository;
use App\Repositories\OfficeDocumentRepositoryInterface;
use App\Repositories\UserRepositoryInterface;
use Illuminate\Support\ServiceProvider;

/**
 * Repository Service Provider - Binds interfaces to implementations.
 * Key part of Clean Architecture - Dependency Injection.
 */
class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            UserRepositoryInterface::class,
            EloquentUserRepository::class,
        );

        $this->app->bind(
            OfficeDocumentRepositoryInterface::class,
            EloquentOfficeDocumentRepository::class,
        );
    }

    public function boot(): void
    {
        //
    }
}
