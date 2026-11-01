<?php

namespace App\Http\Controllers;

use App\Enums\VaiTro;
use App\Models\LopHocPhan;
use App\Models\SinhVien;
use App\Services\GpaService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BaoCaoController extends Controller
{
    public function transcript(Request $request, SinhVien $student, GpaService $gpa): View
    {
        $user = $request->user();

        $allowed = in_array($user->vai_tro, [VaiTro::ADMIN, VaiTro::GIAO_VU], true)
            || ($user->vai_tro === VaiTro::SINH_VIEN && $user->student?->id === $student->id)
            || ($user->vai_tro === VaiTro::GIANG_VIEN && $student->enrollments()
                ->whereHas('course', fn ($query) => $query->where('giang_vien_id', $user->id))
                ->exists());

        abort_unless($allowed, 403);

        $student->load([
            'lop.major.faculty',
            'enrollments.course.subject',
            'enrollments.course.semester',
            'enrollments.grade',
        ]);

        return view('bao-cao.phieu-diem', [
            'student' => $student,
            'gpa' => $gpa->tinh($student),
        ]);
    }

    public function grades(LopHocPhan $course): View
    {
        $this->authorize('view', $course);

        $course->load(['subject', 'semester', 'giangVien', 'enrollments.student.lop', 'enrollments.grade']);

        return view('bao-cao.bang-diem-lop', compact('course'));
    }
}
