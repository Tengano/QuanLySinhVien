<?php

namespace App\Enums;

use App\Enums\Concerns\HasLabel;

enum TrangThaiLopHocPhan: string
{
    use HasLabel;

    case MO = 'mo';
    case DONG = 'dong';
    case KET_THUC = 'ket_thuc';

    public function label(): string
    {
        return match ($this) {
            self::MO => 'Đang mở',
            self::DONG => 'Đã đóng',
            self::KET_THUC => 'Đã kết thúc',
        };
    }
}
