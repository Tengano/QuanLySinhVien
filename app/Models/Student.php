<?php

namespace App\Models;

use App\Enums\GioiTinh;
use App\Enums\TrangThaiSinhVien;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'user_id',
    'ma_sv',
    'ho_ten',
    'ngay_sinh',
    'gioi_tinh',
    'lop_id',
    'email',
    'sdt',
    'cccd',
    'dia_chi',
    'anh',
    'trang_thai',
    'ngay_nhap_hoc',
    'ghi_chu',
])]
class Student extends Model
{
    use SoftDeletes;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'ngay_sinh' => 'date',
            'ngay_nhap_hoc' => 'date',
            'gioi_tinh' => GioiTinh::class,
            'trang_thai' => TrangThaiSinhVien::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lop(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'lop_id');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'enrollments')
            ->withPivot(['trang_thai', 'lan_hoc'])
            ->withTimestamps();
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['keyword'] ?? null, function (Builder $query, string $keyword): void {
                $query->where(function (Builder $query) use ($keyword): void {
                    $query->where('ma_sv', 'like', "%{$keyword}%")
                        ->orWhere('ho_ten', 'like', "%{$keyword}%")
                        ->orWhere('email', 'like', "%{$keyword}%")
                        ->orWhere('sdt', 'like', "%{$keyword}%");
                });
            })
            ->when($filters['lop_id'] ?? null, fn (Builder $query, mixed $lopId): Builder => $query->where('lop_id', $lopId))
            ->when($filters['trang_thai'] ?? null, fn (Builder $query, mixed $trangThai): Builder => $query->where('trang_thai', $trangThai));
    }
}
