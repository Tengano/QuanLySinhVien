<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'ghi_danh_id',
    'diem_qua_trinh',
    'diem_thi',
    'diem_tb',
    'diem_chu',
    'diem_he4',
    'dat',
    'nguoi_nhap_id',
    'nhap_luc',
    'khoa_luc',
    'ghi_chu',
])]
class Diem extends Model
{
    protected $table = 'diem';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'diem_qua_trinh' => 'decimal:2',
            'diem_thi' => 'decimal:2',
            'diem_tb' => 'decimal:2',
            'diem_he4' => 'decimal:2',
            'dat' => 'boolean',
            'nhap_luc' => 'datetime',
            'khoa_luc' => 'datetime',
        ];
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(GhiDanh::class, 'ghi_danh_id');
    }

    public function nguoiNhap(): BelongsTo
    {
        return $this->belongsTo(NguoiDung::class, 'nguoi_nhap_id');
    }
}
