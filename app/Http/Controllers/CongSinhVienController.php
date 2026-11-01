<?php

namespace App\Http\Controllers;

use App\Models\GhiDanh;
use App\Models\HocKy;
use App\Services\GpaService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CongSinhVienController extends Controller
{
    public function grades(Request $request, GpaService $gpa): View
    {
        $student = $request->user()->student()->with('lop.major.faculty')->first();

        abort_if(! $student, 403, 'Tài khoản chưa được liên kết với hồ sơ sinh viên.');

        $semesterId = $request->integer('hoc_ky_id') ?: null;

        $enrollments = GhiDanh::query()
            ->with(['course.subject', 'course.semester', 'grade'])
            ->where('sinh_vien_id', $student->id)
            ->whereHas('grade', fn ($query) => $query->whereNotNull('khoa_luc'))
            ->when($semesterId, fn ($query) => $query->whereHas('course', fn ($course) => $course->where('hoc_ky_id', $semesterId)))
            ->get()
            ->sortByDesc(fn (GhiDanh $enrollment) => $enrollment->course->semester?->ngay_bat_dau);

        return view('cong-sinh-vien.bang-diem', [
            'student' => $student,
            'enrollments' => $enrollments,
            'gpa' => $gpa->tinh($student, $semesterId, true),
            'semesters' => HocKy::orderByDesc('nam_hoc')->get(),
            'semesterId' => $semesterId,
        ]);
    }
}
