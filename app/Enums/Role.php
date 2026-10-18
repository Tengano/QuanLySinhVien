<?php

namespace App\Enums;

use App\Enums\Concerns\HasLabel;

enum Role: string
{
    use HasLabel;

    case ADMIN = 'admin';
    case GIAO_VU = 'giao_vu';
    case GIANG_VIEN = 'giang_vien';
    case SINH_VIEN = 'sinh_vien';

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'Quản trị viên',
            self::GIAO_VU => 'Giáo vụ',
            self::GIANG_VIEN => 'Giảng viên',
            self::SINH_VIEN => 'Sinh viên',
        };
    }
}
