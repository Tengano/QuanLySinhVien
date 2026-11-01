<?php

namespace App\Services;

use App\Models\Diem;
use App\Models\SinhVien;
use Illuminate\Database\Eloquent\Builder;

final class GpaService
{
    /**
     * @return array{
     *     gpa: float,
     *     tong_tin_chi: int,
     *     tin_chi_dat: int,
     *     xep_loai: string,
     *     mon_hoc: array<int, array<string, mixed>>
     * }
     */
    public function tinh(SinhVien $student, ?int $semesterId = null, bool $onlyLocked = false): array
    {
        $grades = Diem::query()
            ->with(['enrollment.course.subject'])
            ->whereHas('enrollment', function (Builder $query) use ($student, $semesterId): void {
                $query->where('sinh_vien_id', $student->id);

                if ($semesterId !== null) {
                    $query->whereHas('course', fn (Builder $course) => $course->where('hoc_ky_id', $semesterId));
                }
            })
            ->whereNotNull('diem_he4')
            ->when($onlyLocked, fn (Builder $query) => $query->whereNotNull('khoa_luc'))
            ->get();

        $theoMon = [];

        foreach ($grades as $grade) {
            $subject = $grade->enrollment?->course?->subject;

            if (! $subject) {
                continue;
            }

            $diemHe4 = (float) $grade->diem_he4;
            $hienTai = $theoMon[$subject->id] ?? null;

            // Môn học lại: chỉ lấy lần có điểm hệ 4 cao nhất.
            if ($hienTai !== null && $diemHe4 <= $hienTai['diem_he4']) {
                continue;
            }

            $theoMon[$subject->id] = [
                'ten_mh' => $subject->ten_mh,
                'so_tin_chi' => (int) $subject->so_tin_chi,
                'diem_tb' => $grade->diem_tb,
                'diem_chu' => $grade->diem_chu,
                'diem_he4' => $diemHe4,
                'dat' => (bool) $grade->dat,
            ];
        }

        $tongTinChi = array_sum(array_column($theoMon, 'so_tin_chi'));
        $tinChiDat = array_sum(array_map(
            fn (array $mon): int => $mon['dat'] ? $mon['so_tin_chi'] : 0,
            $theoMon
        ));
        $tongDiem = array_sum(array_map(
            fn (array $mon): float => $mon['diem_he4'] * $mon['so_tin_chi'],
            $theoMon
        ));

        $gpa = $tongTinChi > 0 ? round($tongDiem / $tongTinChi, 2) : 0.0;

        return [
            'gpa' => $gpa,
            'tong_tin_chi' => $tongTinChi,
            'tin_chi_dat' => $tinChiDat,
            'xep_loai' => $this->xepLoai($gpa),
            'mon_hoc' => array_values($theoMon),
        ];
    }

    public function xepLoai(float $gpa): string
    {
        return match (true) {
            $gpa >= 3.60 => 'Xuất sắc',
            $gpa >= 3.20 => 'Giỏi',
            $gpa >= 2.50 => 'Khá',
            $gpa >= 2.00 => 'Trung bình',
            default => 'Yếu',
        };
    }
}
