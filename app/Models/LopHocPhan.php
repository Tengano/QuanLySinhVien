<?php

namespace App\Models;

use App\Enums\TrangThaiLopHocPhan;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'ma_lhp',
    'mon_hoc_id',
    'hoc_ky_id',
    'giang_vien_id',
    'phong_hoc',
    'lich_hoc',
    'so_sv_toi_da',
    'trang_thai',
    'ghi_chu',
])]
class LopHocPhan extends Model
{
    protected $table = 'lop_hoc_phan';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'trang_thai' => TrangThaiLopHocPhan::class,
        ];
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(MonHoc::class, 'mon_hoc_id');
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(HocKy::class, 'hoc_ky_id');
    }

    public function giangVien(): BelongsTo
    {
        return $this->belongsTo(NguoiDung::class, 'giang_vien_id');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(GhiDanh::class, 'lop_hoc_phan_id');
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(SinhVien::class, 'ghi_danh', 'lop_hoc_phan_id', 'sinh_vien_id')
            ->withPivot(['trang_thai', 'lan_hoc'])
            ->withTimestamps();
    }
}
