<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['major_id', 'ma_lop', 'ten_lop', 'khoa_hoc', 'gvcn_id'])]
class SchoolClass extends Model
{
    protected $table = 'classes';

    public function major(): BelongsTo
    {
        return $this->belongsTo(Major::class);
    }

    public function giaoVienChuNhiem(): BelongsTo
    {
        return $this->belongsTo(User::class, 'gvcn_id');
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'lop_id');
    }
}
