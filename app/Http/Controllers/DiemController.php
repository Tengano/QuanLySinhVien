<?php

namespace App\Http\Controllers;

use App\Enums\VaiTro;
use App\Models\LopHocPhan;
use App\Models\Diem;
use App\Services\DiemService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DiemController extends Controller
{
    public function edit(LopHocPhan $course): View
    {
        $this->authorize('manageGrades', $course);

        $course->load(['subject', 'semester', 'enrollments.student.lop', 'enrollments.grade']);

        return view('lop-hoc-phan.diem', compact('course'));
    }

    public function update(Request $request, LopHocPhan $course, DiemService $diem): RedirectResponse
    {
        $this->authorize('manageGrades', $course);

        $data = $request->validate([
            'grades' => ['required', 'array'],
            'grades.*.diem_qua_trinh' => ['nullable', 'numeric', 'between:0,10'],
            'grades.*.diem_thi' => ['nullable', 'numeric', 'between:0,10'],
        ], [
            'grades.*.diem_qua_trinh.between' => 'Điểm quá trình phải từ 0 đến 10.',
            'grades.*.diem_thi.between' => 'Điểm thi phải từ 0 đến 10.',
        ]);

        $locked = $course->enrollments()
            ->whereHas('grade', fn ($query) => $query->whereNotNull('khoa_luc'))
            ->exists();

        if ($locked) {
            return back()->with('error', 'Lớp đã chốt điểm. Hãy mở khoá trước khi sửa.');
        }

        DB::transaction(function () use ($course, $data, $diem, $request): void {
            foreach ($data['grades'] as $enrollmentId => $row) {
                $enrollment = $course->enrollments()->whereKey($enrollmentId)->first();

                if (! $enrollment) {
                    continue;
                }

                $quaTrinh = $this->toFloat($row['diem_qua_trinh'] ?? null);
                $thi = $this->toFloat($row['diem_thi'] ?? null);
                $ketQua = $diem->tinh($quaTrinh, $thi);

                Diem::updateOrCreate(
                    ['ghi_danh_id' => $enrollment->id],
                    [
                        'diem_qua_trinh' => $quaTrinh,
                        'diem_thi' => $thi,
                        'diem_tb' => $ketQua['diem_tb'],
                        'diem_chu' => $ketQua['diem_chu'],
                        'diem_he4' => $ketQua['diem_he4'],
                        'dat' => $ketQua['dat'],
                        'nguoi_nhap_id' => $request->user()->id,
                        'nhap_luc' => now(),
                    ]
                );
            }
        });

        return back()->with('success', 'Đã lưu điểm và tính lại kết quả.');
    }

    public function lock(LopHocPhan $course): RedirectResponse
    {
        $this->authorize('manageGrades', $course);

        DB::transaction(function () use ($course): void {
            $enrollmentIds = $course->enrollments()->pluck('id');

            foreach ($enrollmentIds as $enrollmentId) {
                Diem::firstOrCreate(['ghi_danh_id' => $enrollmentId]);
            }

            Diem::whereIn('ghi_danh_id', $enrollmentIds)->update(['khoa_luc' => now()]);
        });

        return back()->with('success', 'Đã chốt điểm. Lớp chuyển sang chỉ đọc.');
    }

    public function unlock(LopHocPhan $course): RedirectResponse
    {
        abort_unless(in_array(request()->user()->vai_tro, [VaiTro::ADMIN, VaiTro::GIAO_VU], true), 403);

        Diem::whereHas('enrollment', fn ($query) => $query->where('lop_hoc_phan_id', $course->id))
            ->update(['khoa_luc' => null]);

        return back()->with('success', 'Đã mở khoá điểm để chỉnh sửa.');
    }

    private function toFloat(mixed $value): ?float
    {
        return ($value === null || $value === '') ? null : (float) $value;
    }
}
