<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['ten_hoc_ky', 'hoc_ky_so', 'nam_hoc', 'ngay_bat_dau', 'ngay_ket_thuc'])]
class Semester extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'ngay_bat_dau' => 'date',
            'ngay_ket_thuc' => 'date',
        ];
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }
}
