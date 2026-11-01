<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['khoa_id', 'ma_nganh', 'ten_nganh', 'so_nam_dao_tao'])]
class Nganh extends Model
{
    protected $table = 'nganh';

    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Khoa::class, 'khoa_id');
    }

    public function schoolClasses(): HasMany
    {
        return $this->hasMany(Lop::class, 'nganh_id');
    }
}
