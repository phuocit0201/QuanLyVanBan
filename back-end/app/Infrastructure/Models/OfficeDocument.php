<?php

declare(strict_types=1);

namespace App\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OfficeDocument extends Model
{
    protected $fillable = [
        'external_id',
        'trich_yeu',
        'so_ki_hieu',
        'co_quan_ban_hanh',
        'ngay_van_ban',
        'han_xu_ly',
        'ngay_den_di',
        'ngay_nhan',
        'do_khan',
        'cong_van_den_di',
        'process_definition_id',
        'process_instance_id',
        'is_read',
        'type',
        'raw_data',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'ngay_van_ban' => 'date',
            'han_xu_ly' => 'datetime',
            'ngay_den_di' => 'datetime',
            'ngay_nhan' => 'datetime',
            'is_read' => 'boolean',
            'raw_data' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
