<?php

namespace App\Enums;

use App\Enums\Concerns\HasLabel;

enum TrangThaiDangKy: string
{
    use HasLabel;

    case DANG_HOC = 'dang_hoc';
    case DU_THI = 'du_thi';
    case HOC_LAI = 'hoc_lai';
    case RUT = 'rut';

    public function label(): string
    {
        return match ($this) {
            self::DANG_HOC => 'Đang học',
            self::DU_THI => 'Đủ điều kiện thi',
            self::HOC_LAI => 'Học lại',
            self::RUT => 'Đã rút',
        };
    }
}
