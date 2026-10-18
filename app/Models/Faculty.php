<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

#[Fillable(['ma_khoa', 'ten_khoa', 'truong_khoa', 'dien_thoai', 'email', 'dia_chi'])]
class Faculty extends Model
{
    public function majors(): HasMany
    {
        return $this->hasMany(Major::class);
    }

    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class);
    }

    public function schoolClasses(): HasManyThrough
    {
        return $this->hasManyThrough(SchoolClass::class, Major::class);
    }
}
