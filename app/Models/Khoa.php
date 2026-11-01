<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

#[Fillable(['ma_khoa', 'ten_khoa', 'truong_khoa', 'dien_thoai', 'email', 'dia_chi'])]
class Khoa extends Model
{
    protected $table = 'khoa';

    public function majors(): HasMany
    {
        return $this->hasMany(Nganh::class, 'khoa_id');
    }

    public function subjects(): HasMany
    {
        return $this->hasMany(MonHoc::class, 'khoa_id');
    }

    public function schoolClasses(): HasManyThrough
    {
        return $this->hasManyThrough(Lop::class, Nganh::class, 'khoa_id', 'nganh_id');
    }
}
