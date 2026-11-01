<?php

namespace App\Policies;

use App\Enums\VaiTro;
use App\Models\LopHocPhan;
use App\Models\NguoiDung;

class LopHocPhanPolicy
{
    public function view(NguoiDung $user, LopHocPhan $course): bool
    {
        return $user->vai_tro === VaiTro::ADMIN
            || $user->vai_tro === VaiTro::GIAO_VU
            || ($user->vai_tro === VaiTro::GIANG_VIEN && $course->giang_vien_id === $user->id);
    }

    public function manage(NguoiDung $user, LopHocPhan $course): bool
    {
        return $user->vai_tro === VaiTro::ADMIN || $user->vai_tro === VaiTro::GIAO_VU;
    }

    public function manageEnrollments(NguoiDung $user, LopHocPhan $course): bool
    {
        return $user->vai_tro === VaiTro::ADMIN || $user->vai_tro === VaiTro::GIAO_VU;
    }

    public function manageGrades(NguoiDung $user, LopHocPhan $course): bool
    {
        return $user->vai_tro === VaiTro::ADMIN
            || $user->vai_tro === VaiTro::GIAO_VU
            || ($user->vai_tro === VaiTro::GIANG_VIEN && $course->giang_vien_id === $user->id);
    }
}
