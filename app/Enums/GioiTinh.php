<?php

namespace App\Enums;

use App\Enums\Concerns\HasLabel;

enum GioiTinh: string
{
    use HasLabel;

    case NAM = 'nam';
    case NU = 'nu';
    case KHAC = 'khac';

    public function label(): string
    {
        return match ($this) {
            self::NAM => 'Nam',
            self::NU => 'Nữ',
            self::KHAC => 'Khác',
        };
    }
}
