<?php

namespace App\Enums;

use App\Enums\Concerns\HasLabel;

enum TrangThaiSinhVien: string
{
    use HasLabel;

    case DANG_HOC = 'dang_hoc';
    case BAO_LUU = 'bao_luu';
    case TOT_NGHIEP = 'tot_nghiep';
    case THOI_HOC = 'thoi_hoc';

    public function label(): string
    {
        return match ($this) {
            self::DANG_HOC => 'Đang học',
            self::BAO_LUU => 'Bảo lưu',
            self::TOT_NGHIEP => 'Tốt nghiệp',
            self::THOI_HOC => 'Thôi học',
        };
    }
}
