<?php

namespace App\Models;

use App\Enums\VaiTro;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['ho_ten', 'email', 'password', 'vai_tro', 'trang_thai', 'so_dien_thoai', 'anh_dai_dien', 'dang_nhap_cuoi_luc'])]
#[Hidden(['password', 'remember_token'])]
class NguoiDung extends Authenticatable
{
    protected $table = 'nguoi_dung';

    use Notifiable;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'vai_tro' => VaiTro::class,
            'trang_thai' => 'boolean',
            'dang_nhap_cuoi_luc' => 'datetime',
        ];
    }

    public function student(): HasOne
    {
        return $this->hasOne(SinhVien::class, 'nguoi_dung_id');
    }

    public function courses(): HasMany
    {
        return $this->hasMany(LopHocPhan::class, 'giang_vien_id');
    }

    public function announcementsSent(): HasMany
    {
        return $this->hasMany(ThongBao::class, 'nguoi_gui_id');
    }

    public function announcements(): BelongsToMany
    {
        return $this->belongsToMany(ThongBao::class, 'thong_bao_nguoi_dung', 'nguoi_dung_id', 'thong_bao_id')
            ->withPivot(['da_doc', 'doc_luc'])
            ->withTimestamps();
    }
}
