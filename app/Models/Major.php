<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['faculty_id', 'ma_nganh', 'ten_nganh', 'so_nam_dao_tao'])]
class Major extends Model
{
    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class);
    }

    public function schoolClasses(): HasMany
    {
        return $this->hasMany(SchoolClass::class);
    }
}
