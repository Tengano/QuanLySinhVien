<?php

namespace App\Services;

use App\Enums\VaiTro;
use App\Enums\TrangThaiLopHocPhan;
use App\Enums\TrangThaiSinhVien;
use App\Models\LopHocPhan;
use App\Models\Diem;
use App\Models\SinhVien;
use App\Models\MonHoc;
use App\Models\NguoiDung;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

final class ThongKeService
{
    /**
     * @return array<string, mixed>
     */
    public function admin(): array
    {
        return Cache::remember('dashboard.admin', now()->addMinutes(5), function (): array {
            $cards = [
                'sinh_vien' => SinhVien::query()->where('trang_thai', TrangThaiSinhVien::DANG_HOC->value)->count(),
                'lop_hp' => LopHocPhan::query()->where('trang_thai', TrangThaiLopHocPhan::MO->value)->count(),
                'mon_hoc' => MonHoc::query()->count(),
                'giang_vien' => NguoiDung::query()->where('vai_tro', VaiTro::GIANG_VIEN->value)->where('trang_thai', true)->count(),
            ];

            $svTheoKhoa = DB::table('sinh_vien')
                ->join('lop', 'sinh_vien.lop_id', '=', 'lop.id')
                ->join('nganh', 'lop.nganh_id', '=', 'nganh.id')
                ->join('khoa', 'nganh.khoa_id', '=', 'khoa.id')
                ->whereNull('sinh_vien.deleted_at')
                ->where('sinh_vien.trang_thai', TrangThaiSinhVien::DANG_HOC->value)
                ->select('khoa.ten_khoa', DB::raw('COUNT(*) AS total'))
                ->groupBy('khoa.ten_khoa')
                ->orderByDesc('total')
                ->pluck('total', 'ten_khoa')
                ->all();

            $svTheoNganh = DB::table('sinh_vien')
                ->join('lop', 'sinh_vien.lop_id', '=', 'lop.id')
                ->join('nganh', 'lop.nganh_id', '=', 'nganh.id')
                ->whereNull('sinh_vien.deleted_at')
                ->where('sinh_vien.trang_thai', TrangThaiSinhVien::DANG_HOC->value)
                ->select('nganh.ten_nganh', DB::raw('COUNT(*) AS total'))
                ->groupBy('nganh.ten_nganh')
                ->orderByDesc('total')
                ->limit(6)
                ->pluck('total', 'ten_nganh')
                ->all();

            $datRot = DB::table('diem')
                ->join('ghi_danh', 'diem.ghi_danh_id', '=', 'ghi_danh.id')
                ->join('lop_hoc_phan', 'ghi_danh.lop_hoc_phan_id', '=', 'lop_hoc_phan.id')
                ->join('mon_hoc', 'lop_hoc_phan.mon_hoc_id', '=', 'mon_hoc.id')
                ->whereNotNull('diem.dat')
                ->selectRaw('mon_hoc.ten_mh, SUM(CASE WHEN diem.dat = 1 THEN 1 ELSE 0 END) AS dat, SUM(CASE WHEN diem.dat = 0 THEN 1 ELSE 0 END) AS rot')
                ->groupBy('mon_hoc.ten_mh')
                ->orderByRaw('COUNT(*) DESC')
                ->limit(8)
                ->get();

            $diemTheoKy = DB::table('diem')
                ->join('ghi_danh', 'diem.ghi_danh_id', '=', 'ghi_danh.id')
                ->join('lop_hoc_phan', 'ghi_danh.lop_hoc_phan_id', '=', 'lop_hoc_phan.id')
                ->join('hoc_ky', 'lop_hoc_phan.hoc_ky_id', '=', 'hoc_ky.id')
                ->whereNotNull('diem.diem_tb')
                ->select('hoc_ky.nam_hoc', 'hoc_ky.hoc_ky_so', 'hoc_ky.ten_hoc_ky', DB::raw('AVG(diem.diem_tb) AS trung_binh'))
                ->groupBy('hoc_ky.id', 'hoc_ky.nam_hoc', 'hoc_ky.hoc_ky_so', 'hoc_ky.ten_hoc_ky')
                ->orderBy('hoc_ky.nam_hoc')
                ->orderBy('hoc_ky.hoc_ky_so')
                ->get();

            return compact('cards', 'svTheoKhoa', 'svTheoNganh', 'datRot', 'diemTheoKy');
        });
    }

    /**
     * @return array<string, mixed>
     */
    public function giangVien(NguoiDung $user): array
    {
        $courses = LopHocPhan::query()
            ->with(['subject', 'semester'])
            ->withCount('enrollments')
            ->where('giang_vien_id', $user->id)
            ->orderByDesc('id')
            ->get();

        $chuaNhap = $courses->filter(function (LopHocPhan $course): bool {
            return ! Diem::query()
                ->whereHas('enrollment', fn ($query) => $query->where('lop_hoc_phan_id', $course->id))
                ->exists();
        })->count();

        return compact('courses', 'chuaNhap');
    }
}
