<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\Semester;
use App\Services\GpaService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PortalController extends Controller
{
    public function grades(Request $request, GpaService $gpa): View
    {
        $student = $request->user()->student()->with('lop.major.faculty')->first();

        abort_if(! $student, 403, 'Tài khoản chưa được liên kết với hồ sơ sinh viên.');

        $semesterId = $request->integer('semester_id') ?: null;

        $enrollments = Enrollment::query()
            ->with(['course.subject', 'course.semester', 'grade'])
            ->where('student_id', $student->id)
            ->whereHas('grade', fn ($query) => $query->whereNotNull('khoa_luc'))
            ->when($semesterId, fn ($query) => $query->whereHas('course', fn ($course) => $course->where('semester_id', $semesterId)))
            ->get()
            ->sortByDesc(fn (Enrollment $enrollment) => $enrollment->course->semester?->ngay_bat_dau);

        return view('portal.grades', [
            'student' => $student,
            'enrollments' => $enrollments,
            'gpa' => $gpa->tinh($student, $semesterId, true),
            'semesters' => Semester::orderByDesc('nam_hoc')->get(),
            'semesterId' => $semesterId,
        ]);
    }
}
