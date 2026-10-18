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
    'lop_hp_id',
    'nguoi_nhan_id',
])]
class Announcement extends Model
{
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
        return $this->belongsTo(User::class, 'nguoi_gui_id');
    }

    public function khoa(): BelongsTo
    {
        return $this->belongsTo(Faculty::class, 'khoa_id');
    }

    public function lop(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'lop_id');
    }

    public function lopHocPhan(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'lop_hp_id');
    }

    public function nguoiNhan(): BelongsTo
    {
        return $this->belongsTo(User::class, 'nguoi_nhan_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'announcement_user')
            ->withPivot(['da_doc', 'doc_luc'])
            ->withTimestamps();
    }
}
