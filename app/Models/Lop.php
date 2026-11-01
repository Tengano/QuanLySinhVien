<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['nganh_id', 'ma_lop', 'ten_lop', 'khoa_hoc', 'giao_vien_chu_nhiem_id'])]
class Lop extends Model
{
    protected $table = 'lop';

    public function major(): BelongsTo
    {
        return $this->belongsTo(Nganh::class, 'nganh_id');
    }

    public function giaoVienChuNhiem(): BelongsTo
    {
        return $this->belongsTo(NguoiDung::class, 'giao_vien_chu_nhiem_id');
    }

    public function students(): HasMany
    {
        return $this->hasMany(SinhVien::class, 'lop_id');
    }
}
