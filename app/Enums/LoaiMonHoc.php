<?php

namespace App\Enums;

use App\Enums\Concerns\HasLabel;

enum LoaiMonHoc: string
{
    use HasLabel;

    case BAT_BUOC = 'bat_buoc';
    case TU_CHON = 'tu_chon';

    public function label(): string
    {
        return match ($this) {
            self::BAT_BUOC => 'Bắt buộc',
            self::TU_CHON => 'Tự chọn',
        };
    }
}
