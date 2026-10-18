<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Models\Course;
use App\Models\Student;
use App\Services\GpaService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function transcript(Request $request, Student $student, GpaService $gpa): View
    {
        $user = $request->user();

        $allowed = in_array($user->role, [Role::ADMIN, Role::GIAO_VU], true)
            || ($user->role === Role::SINH_VIEN && $user->student?->id === $student->id)
            || ($user->role === Role::GIANG_VIEN && $student->enrollments()
                ->whereHas('course', fn ($query) => $query->where('giang_vien_id', $user->id))
                ->exists());

        abort_unless($allowed, 403);

        $student->load([
            'lop.major.faculty',
            'enrollments.course.subject',
            'enrollments.course.semester',
            'enrollments.grade',
        ]);

        return view('reports.transcript', [
            'student' => $student,
            'gpa' => $gpa->tinh($student),
        ]);
    }

    public function grades(Course $course): View
    {
        $this->authorize('view', $course);

        $course->load(['subject', 'semester', 'giangVien', 'enrollments.student.lop', 'enrollments.grade']);

        return view('reports.grades', compact('course'));
    }
}
