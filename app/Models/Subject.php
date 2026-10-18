<?php

namespace App\Models;

use App\Enums\LoaiMonHoc;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'faculty_id',
    'ma_mh',
    'ten_mh',
    'so_tin_chi',
    'loai_mh',
    'so_tiet_ly_thuyet',
    'so_tiet_thuc_hanh',
])]
class Subject extends Model
{
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
        return $this->belongsTo(Faculty::class);
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }
}
