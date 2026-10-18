<?php

namespace App\Enums;

use App\Enums\Concerns\HasLabel;

enum DoiTuongThongBao: string
{
    use HasLabel;

    case TOAN_TRUONG = 'toan_truong';
    case KHOA = 'khoa';
    case LOP = 'lop';
    case LOP_HOC_PHAN = 'lop_hoc_phan';
    case NGUOI_DUNG = 'nguoi_dung';

    public function label(): string
    {
        return match ($this) {
            self::TOAN_TRUONG => 'Toàn trường',
            self::KHOA => 'Theo khoa',
            self::LOP => 'Theo lớp hành chính',
            self::LOP_HOC_PHAN => 'Theo lớp học phần',
            self::NGUOI_DUNG => 'Một người dùng',
        };
    }
}
