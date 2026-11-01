<?php

namespace App\Models;

use App\Enums\TrangThaiDangKy;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable(['sinh_vien_id', 'lop_hoc_phan_id', 'trang_thai', 'lan_hoc'])]
class GhiDanh extends Model
{
    protected $table = 'ghi_danh';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'trang_thai' => TrangThaiDangKy::class,
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(SinhVien::class, 'sinh_vien_id');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(LopHocPhan::class, 'lop_hoc_phan_id');
    }

    public function grade(): HasOne
    {
        return $this->hasOne(Diem::class, 'ghi_danh_id');
    }
}
