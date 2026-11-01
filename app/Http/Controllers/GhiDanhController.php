<?php

namespace App\Http\Controllers;

use App\Enums\TrangThaiDangKy;
use App\Models\LopHocPhan;
use App\Models\GhiDanh;
use App\Models\SinhVien;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GhiDanhController extends Controller
{
    public function create(LopHocPhan $course): View
    {
        $this->authorize('manageEnrollments', $course);

        $enrolledIds = $course->enrollments()->pluck('sinh_vien_id');

        return view('lop-hoc-phan.ghi-danh', [
            'course' => $course->load(['subject', 'semester']),
            'students' => SinhVien::with('lop')
                ->whereNotIn('id', $enrolledIds)
                ->orderBy('ma_sv')
                ->get(),
            'currentCount' => $course->enrollments()->where('trang_thai', '!=', TrangThaiDangKy::RUT->value)->count(),
        ]);
    }

    public function store(Request $request, LopHocPhan $course): RedirectResponse
    {
        $this->authorize('manageEnrollments', $course);

        $data = $request->validate([
            'student_ids' => ['required', 'array', 'min:1'],
            'student_ids.*' => ['integer', 'exists:sinh_vien,id'],
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
            GhiDanh::firstOrCreate(
                ['sinh_vien_id' => $studentId, 'lop_hoc_phan_id' => $course->id, 'lan_hoc' => 1],
                ['trang_thai' => TrangThaiDangKy::DANG_HOC]
            );
        }

        return redirect()->route('courses.show', $course)
            ->with('success', 'Đã ghi danh '.count($selected).' sinh viên.');
    }

    public function destroy(GhiDanh $enrollment): RedirectResponse
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
