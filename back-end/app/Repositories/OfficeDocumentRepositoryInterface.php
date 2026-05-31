<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Domain\Entities\OfficeDocument;
use Illuminate\Database\Eloquent\Collection;

interface OfficeDocumentRepositoryInterface
{
    public function findByExternalId(string $externalId): ?OfficeDocument;

    public function findByUser(int $userId): Collection;

    public function findUnreadByUser(int $userId): Collection;

    public function saveFromApi(array $data, int $userId): OfficeDocument;

    public function upsertMany(array $documents, int $userId): int;

    public function deleteOldDocuments(int $userId, int $daysToKeep = 30): int;
}
