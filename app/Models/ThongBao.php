<?php

namespace App\Models;

use App\Enums\DoiTuongThongBao;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable([
    'tieu_de',
    'noi_dung',
    'nguoi_gui_id',
    'doi_tuong',
    'khoa_id',
    'lop_id',
    'lop_hoc_phan_id',
    'nguoi_nhan_id',
])]
class ThongBao extends Model
{
    protected $table = 'thong_bao';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'doi_tuong' => DoiTuongThongBao::class,
        ];
    }

    public function nguoiGui(): BelongsTo
    {
        return $this->belongsTo(NguoiDung::class, 'nguoi_gui_id');
    }

    public function khoa(): BelongsTo
    {
        return $this->belongsTo(Khoa::class, 'khoa_id');
    }

    public function lop(): BelongsTo
    {
        return $this->belongsTo(Lop::class, 'lop_id');
    }

    public function lopHocPhan(): BelongsTo
    {
        return $this->belongsTo(LopHocPhan::class, 'lop_hoc_phan_id');
    }

    public function nguoiNhan(): BelongsTo
    {
        return $this->belongsTo(NguoiDung::class, 'nguoi_nhan_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(NguoiDung::class, 'thong_bao_nguoi_dung', 'thong_bao_id', 'nguoi_dung_id')
            ->withPivot(['da_doc', 'doc_luc'])
            ->withTimestamps();
    }
}
