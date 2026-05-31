<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\OfficeDocument;
use App\Infrastructure\Models\OfficeDocument as OfficeDocumentModel;
use App\Repositories\OfficeDocumentRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class EloquentOfficeDocumentRepository implements OfficeDocumentRepositoryInterface
{
    public function findByExternalId(string $externalId): ?OfficeDocument
    {
        $model = OfficeDocumentModel::where('external_id', $externalId)->first();

        return $model ? OfficeDocument::find($model->id) : null;
    }

    public function findByUser(int $userId): Collection
    {
        return OfficeDocument::where('user_id', $userId)
            ->orderBy('ngay_nhan', 'desc')
            ->get();
    }

    public function findUnreadByUser(int $userId): Collection
    {
        return OfficeDocument::where('user_id', $userId)
            ->where('is_read', false)
            ->orderBy('ngay_nhan', 'desc')
            ->get();
    }

    public function saveFromApi(array $data, int $userId): OfficeDocument
    {
        $model = OfficeDocumentModel::updateOrCreate(
            ['external_id' => $data['id']],
            [
                'trich_yeu' => $data['trichYeu'] ?? null,
                'so_ki_hieu' => $data['soKihieu'] ?? null,
                'co_quan_ban_hanh' => $data['coQuanBanHanh'] ?? null,
                'ngay_van_ban' => $this->parseDate($data['ngayVanBan'] ?? null),
                'han_xu_ly' => $this->parseDateTime($data['hanxuly'] ?? null),
                'ngay_den_di' => $this->parseDateTime($data['ngayDenDi'] ?? null),
                'ngay_nhan' => $this->parseDateTime($data['ngayNhan'] ?? null),
                'do_khan' => $data['doKhan'] ?? null,
                'cong_van_den_di' => $data['congVanDenDi'] ?? '1',
                'process_definition_id' => $data['processDefinitionId'] ?? null,
                'process_instance_id' => $data['processInstanceId'] ?? null,
                'is_read' => ($data['isRead'] ?? 'FALSE') === 'TRUE',
                'type' => $data['type'] ?? null,
                'raw_data' => $data,
                'user_id' => $userId,
            ]
        );

        return OfficeDocument::find($model->id);
    }

    public function upsertMany(array $documents, int $userId): int
    {
        $count = 0;

        foreach ($documents as $doc) {
            $this->saveFromApi($doc, $userId);
            $count++;
        }

        Log::info('EloquentOfficeDocumentRepository: Upserted documents', [
            'count' => $count,
            'userId' => $userId,
        ]);

        return $count;
    }

    public function deleteOldDocuments(int $userId, int $daysToKeep = 30): int
    {
        $cutoffDate = now()->subDays($daysToKeep);

        $count = OfficeDocumentModel::where('user_id', $userId)
            ->where('created_at', '<', $cutoffDate)
            ->delete();

        Log::info('EloquentOfficeDocumentRepository: Deleted old documents', [
            'count' => $count,
            'userId' => $userId,
            'cutoffDate' => $cutoffDate->toDateTimeString(),
        ]);

        return $count;
    }

    private function parseDate(?string $date): ?string
    {
        if ($date === null || $date === '') {
            return null;
        }

        try {
            return \Carbon\Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    private function parseDateTime(?string $dateTime): ?string
    {
        if ($dateTime === null || $dateTime === '') {
            return null;
        }

        try {
            if (str_contains($dateTime, ' ')) {
                return \Carbon\Carbon::createFromFormat('d/m/Y H:i', $dateTime)->format('Y-m-d H:i:s');
            }

            return \Carbon\Carbon::createFromFormat('d/m/Y', $dateTime)->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            return null;
        }
    }
}
