<?php

namespace App\Models;

use App\Enums\LoaiMonHoc;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'khoa_id',
    'ma_mh',
    'ten_mh',
    'so_tin_chi',
    'loai_mh',
    'so_tiet_ly_thuyet',
    'so_tiet_thuc_hanh',
])]
class MonHoc extends Model
{
    protected $table = 'mon_hoc';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'loai_mh' => LoaiMonHoc::class,
        ];
    }

    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Khoa::class, 'khoa_id');
    }

    public function courses(): HasMany
    {
        return $this->hasMany(LopHocPhan::class, 'mon_hoc_id');
    }
}
