<?php

declare(strict_types=1);

namespace App\Domain\Entities;

use App\Infrastructure\Models\VnptCredential as VnptCredentialModel;

class VnptCredentialEntity extends VnptCredentialModel
{
    protected $table = 'vnpt_credentials';

    public const DEVICE_IOS = 'IOS';
    public const DEVICE_ANDROID = 'ANDROID';

    public static function availableDeviceTypes(): array
    {
        return [self::DEVICE_IOS, self::DEVICE_ANDROID];
    }
}
