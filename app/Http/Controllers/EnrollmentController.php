<?php

namespace App\Http\Controllers;

use App\Enums\TrangThaiDangKy;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EnrollmentController extends Controller
{
    public function create(Course $course): View
    {
        $this->authorize('manageEnrollments', $course);

        $enrolledIds = $course->enrollments()->pluck('student_id');

        return view('courses.enroll', [
            'course' => $course->load(['subject', 'semester']),
            'students' => Student::with('lop')
                ->whereNotIn('id', $enrolledIds)
                ->orderBy('ma_sv')
                ->get(),
            'currentCount' => $course->enrollments()->where('trang_thai', '!=', TrangThaiDangKy::RUT->value)->count(),
        ]);
    }

    public function store(Request $request, Course $course): RedirectResponse
    {
        $this->authorize('manageEnrollments', $course);

        $data = $request->validate([
            'student_ids' => ['required', 'array', 'min:1'],
            'student_ids.*' => ['integer', 'exists:students,id'],
        ], [
            'student_ids.required' => 'Vui lòng chọn ít nhất một sinh viên.',
        ]);

        $current = $course->enrollments()->where('trang_thai', '!=', TrangThaiDangKy::RUT->value)->count();
        $selected = array_unique($data['student_ids']);
        $remaining = max(0, $course->so_sv_toi_da - $current);

        if (count($selected) > $remaining) {
            return back()->with('error', "Lớp chỉ còn {$remaining} chỗ trống, không thể ghi danh ".count($selected).' sinh viên.');
        }

        foreach ($selected as $studentId) {
            Enrollment::firstOrCreate(
                ['student_id' => $studentId, 'course_id' => $course->id, 'lan_hoc' => 1],
                ['trang_thai' => TrangThaiDangKy::DANG_HOC]
            );
        }

        return redirect()->route('courses.show', $course)
            ->with('success', 'Đã ghi danh '.count($selected).' sinh viên.');
    }

    public function destroy(Enrollment $enrollment): RedirectResponse
    {
        $course = $enrollment->course;
        $this->authorize('manageEnrollments', $course);

        if ($enrollment->grade?->khoa_luc) {
            return back()->with('error', 'Không thể rút sinh viên đã được chốt điểm.');
        }

        $enrollment->delete();

        return back()->with('success', 'Đã rút sinh viên khỏi lớp học phần.');
    }
}
