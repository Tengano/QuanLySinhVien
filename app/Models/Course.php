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
    'subject_id',
    'semester_id',
    'giang_vien_id',
    'phong_hoc',
    'lich_hoc',
    'so_sv_toi_da',
    'trang_thai',
    'ghi_chu',
])]
class Course extends Model
{
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
        return $this->belongsTo(Subject::class);
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    public function giangVien(): BelongsTo
    {
        return $this->belongsTo(User::class, 'giang_vien_id');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'enrollments')
            ->withPivot(['trang_thai', 'lan_hoc'])
            ->withTimestamps();
    }
}
