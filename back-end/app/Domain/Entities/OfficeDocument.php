<?php

declare(strict_types=1);

namespace App\Domain\Entities;

use App\Infrastructure\Models\OfficeDocument as OfficeDocumentModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OfficeDocument extends OfficeDocumentModel
{
    public function user(): BelongsTo
    {
        return $this->belongsTo(UserEntity::class);
    }

    public function markAsRead(): void
    {
        $this->is_read = true;
        $this->save();
    }

    public function isUrgent(): bool
    {
        return $this->do_khan === 'Hoả tốc';
    }
}
